<?php

namespace App\Http\Livewire;

use App\Models\History;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use NXP\MathExecutor;

// mengubah stack dari inputan . menjadi 0.
class Calculator extends Component
{
    public $stack; // Inputan dari user
    public $display; // Teks yang di tampilkan di layar
    public $operators = ['+', '-', '*', '/', '^', '%']; // Operator reguler
    public $calculationOperators = ['!']; // Operator yang langsung menghitung nilai
    public $selectedOperator = null;
    public $lastOperatorPressed = null;
    public History $history;

    protected $listeners = [
        'useHistoryId'
    ];

    // menampilkan history sesuai dengan id yang di mau
    public function useHistoryId($id){
        $this->stack = History::find($id)->result;
        $this->render();
    }
    public function mount()
    {
        $this->clear();
    }

    public function render()
    {
        return view('livewire.calculator');
    }

    public function clear()
    {
        $this->clearStack();
        $this->setDisplay();
        $this->lastOperatorPressed = null;
        $this->selectedOperator = null;
    }

    public function getClearTextProperty()
    {
        return $this->stackIsEmpty() ? 'AC' : 'C';
    }

    public function add()
    {
        $this->operator('+');
    }

    public function subtract()
    {
        $this->operator('-');
    }

    public function multiply()
    {
        $this->operator('*');
    }

    public function divide()
    {
        $this->operator('/');
    }

    public function negate()
    {
        $this->operator('!');
    }

    public function percent()
    {
        $this->operator('%');
    }
    
    public function rank()
    {
        $this->operator('^');
    }

    private function operator($operator)
    {
        $this->lastOperatorPressed = $operator;

        if ($this->stackIsEmpty()) {
            $this->clear();
            return;
        } elseif ($this->stack == '0.' && $this->isACalculationOperator($operator)) {
            $this->clear();
            return;
        }

        $this->selectedOperator = $operator;

        // mengubah karakter terakhir jika sudah berupa sebuah operator
        $lastCharInStack = substr($this->stack, -1) ?: '';
        if ($this->isAnOperator($operator) && $this->isAnOperator($lastCharInStack)) {
            $this->stack = substr($this->stack, 0, -1) . $operator;
            return;
        }

        if (! $this->shouldCalculate($operator)) {
            $this->addToStacks($operator);
            $this->setDisplay();
            return;
        }

        try {
            $math = new MathExecutor();

            if ($this->isACalculationOperator($operator)) {
                $lastNumber = $this->getStackLastNumber();

                if ($operator == '!') {  
                    // mengubah nilai dengan mengkali dengan -1
                    $total = $math->execute("-1*" . (float)$lastNumber);
                } 
            } else {
                
                if ($operator == '=' && preg_match('/^(\-?\d*\.?\d*)([\+\-\*\/\^\%])$/', $this->stack, $chunks)) {
                    $this->stack .= $chunks[1];
                }

                // Mengatasi permasalahan float
                preg_match('/^(\-?\d*\.?\d*)([\+\-\*\/\^\%])(\d*\.?\d*)$/', $this->stack, $chunks);
                $total = $math->execute((float)$chunks[1] . $chunks[2] . (float)$chunks[3]);
            }

            // menyimpan history perhitungan ke dalam database
            $histories = $this->stack;
            History::insert([
                'calculate_history' => $histories,
                'result' => $total,
            ]);

            $total = round($total, 10);
            $this->emit('updateHistory');
            // jika Out of range
            if ($total > 999999999999 || $total < -999999999999) {
                throw new \Exception('Number out of range');
            }

            // Menambah operator ketika perhitungan sudah dihitung
            if ($this->isAnOperator($operator)) {
                $this->stack = $total . $operator;
            } else {
                $this->stack = $total;
            }

            $this->setDisplay();
        } catch (\Exception $e) {
            $this->clear();
            $this->setDisplay($e->getMessage());
        }
    }

    public function number($number)
    {
        if ($this->lastOperatorPressed === '=') {
            $this->clear();
        }

        $this->addToStacks($number);
        $this->setDisplay();
    }

    public function decimal()
    {
        if ($this->lastOperatorPressed === '=') {
            $this->clear();
        }

        $lastCharInStack = substr($this->stack, -1) ?: '';

        if ($this->stackIsEmpty() || $this->isAnOperator($lastCharInStack)) {
            $this->addToStacks('0.');
            $this->setDisplay();
            return;
        }

        preg_match('/\d*\.?\d*$/', $this->stack, $chunks);
        if ($chunks[0] && Str::contains($chunks[0], '.')) {
            return;
        }

        $this->addToStacks('.');
        $this->setDisplay();
    }

    public function equal()
    {
        $this->lastOperatorPressed = '=';

        // jika hanya menginput titik
        if ($this->stack == '0.') {
            $this->clear();
            return;
        }

        // hanya angka
        if (preg_match('/^(\-?\d*\.?\d*)$/', $this->stack)) {
            return;
        }

        $this->operator('=');
    }

    private function stackIsEmpty()
    {
        return strlen($this->stack) === 0;
    }

    private function addToStacks($char)
    {
        $lastCharInStack = substr($this->stack, -1) ?: '';
        
        if ($char === '.' && $lastCharInStack === '.') {
            return;
        }

        $this->stack .= $char;
    }
    
    private function clearStack()
    {
        $this->stack = '';
    }

    // Menguraikan 1 atau 2 angka dan kebalikan yang terakhir
    // contoh format: 1 or -1 or 1*2 or -1*2
    private function getStackLastNumber()
    {
        preg_match('/^(\-?\d*\.?\d*)[\+\-\*\/\^\%]?(\d*\.?\d*)?$/', $this->stack, $chunks);
        $chunks = array_filter($chunks); // menghapus string yang kosong
        
        return end($chunks);
    }

    private function setDisplay($message = null)
    {
        if ($message) {
            $this->display = $message;
            return;
        }

        $this->display = $this->getStackLastNumber() ?: '0';
    }

    private function clearDisplay()
    {
        $this->setDisplay(0);
    }
    
    private function isAnOperator($char)
    {
        return in_array($char, $this->operators, true);
    }

    private function isACalculationOperator($char)
    {
        return in_array($char, $this->calculationOperators, true);
    }

    private function shouldCalculate($operator)
    {
        return (
            $operator == '=' ||
            $this->isACalculationOperator($operator) || 
            ($this->isAnOperator($operator) && $validMathFormat = preg_match('/^(\-?\d*\.?\d*)([\+\-\*\/\^\%])(\d*\.?\d*)$/', $this->stack))
        );
    }
}
<?php

namespace App\Http\Livewire;

use App\Models\History as ModelsHistory;
use Livewire\Component;

class History extends Component
{
    
    protected $listeners = [
        'updateHistory'
    ];

    public function render()
    {
        $histories = ModelsHistory::get();
        return view('livewire.history',[
            'histories' => $histories,
        ]);
    }

    public function updateHistory(){
        $this->render();
    }

    public function useHistory($id){
        $this->emit('useHistoryId',$id);
    }
}

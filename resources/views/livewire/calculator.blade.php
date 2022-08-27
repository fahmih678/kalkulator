<div>
    <div class="calc-display">
        <div class="calc-display-span secondary-display" id="secondary-display">
        </div>
        <div class="calc-display-span primary-display" id="display">{{ $stack }}
        </div>
    </div>
    <hr>
    {{-- <div class="calc-display-hr"></div> --}}
    <div class="calc-btn" id="btn">

        <button class="calc-btn-primary btn-bg" wire:click="clear">{{ $this->clearText }}</button>
        <button class="calc-btn-primary btn-bg" wire:click="negate">&plusmn;</button>
        <button class="calc-btn-primary btn-bg" wire:click="percent">&percnt;</button>
        <button class="calc-btn-primary btn-bg" wire:click="rank">^</button>

        <button class="calc-btn-primary" wire:click="number( 7 )">7</button>
        <button class="calc-btn-primary" wire:click="number( 8 )">8</button>
        <button class="calc-btn-primary" wire:click="number( 9 )">9</button>
        <button class="calc-btn-primary btn-bg" wire:click="divide">&divide;</button>
        <button class="calc-btn-primary" wire:click="number( 4 )">4</button>
        <button class="calc-btn-primary" wire:click="number( 5 )">5</button>
        <button class="calc-btn-primary" wire:click="number( 6 )">6</button>
        <button class="calc-btn-primary btn-bg" wire:click="multiply">&times;</button>
        <button class="calc-btn-primary" wire:click="number( 1 )">1</button>
        <button class="calc-btn-primary" wire:click="number( 2 )">2</button>
        <button class="calc-btn-primary" wire:click="number( 3 )">3</button>
        <button class="calc-btn-primary btn-bg" wire:click="add">&plus;</button>
        <button class="calc-btn-primary btn-bg" wire:click="decimal">.</button>
        <button class="calc-btn-primary" wire:click="number(0)">0</button>
        <button class="calc-btn-primary btn-bg-equal" wire:click="equal">&equals;</button>
        <button class="calc-btn-primary btn-bg" wire:click="subtract">&minus;</button>
    </div>
</div>

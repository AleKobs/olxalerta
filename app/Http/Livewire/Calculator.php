<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Calculator extends Component
{

    public $number = 0;
    public function __construct() {
        $this->number = rand(1,9);
    }
    public function render()
    {
        return view('livewire.calculator');
    }
    public function increment() {
        $this->number++;
    }
    public function rand() {
        $this->number = rand(0,999);
    }
}

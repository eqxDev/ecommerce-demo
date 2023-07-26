<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Home')]
class Home extends Component
{
    public function render()
    {
        return view('livewire.home');
    }
}

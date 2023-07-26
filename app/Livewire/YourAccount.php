<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class YourAccount extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.your-account', [
            'orders' => \Auth::user()->orders()->with('items')->orderBy('orders.id', 'desc')->paginate(5),
        ]);
    }
}

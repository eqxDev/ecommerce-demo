<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class ProductGrid extends Component
{
    use WithPagination;

    public $sort = 'low-high';

    protected $queryString = [
        'sort' => ['except' => 'low-high'],
    ];

    public function sortUpdated($name, $value)
    {
        $this->validate([
            'sort' => 'required|in:low-high,high-low',
        ]);

        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.product-grid', ['products' => $this->getProducts()]);
    }

    private function getProducts()
    {
        return \App\Models\Product::query()
            ->with('variants.colour', 'variants.size')
            ->when($this->sort === 'low-high', function ($query) {
                $query->orderBy('price');
            }, function ($query) {
                $query->orderByDesc('price');
            })
            ->paginate(12);
    }
}

<?php

namespace App\Livewire;

use App\Models\ProductVariant;
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
            ->addSelect(['lowest_price' => ProductVariant::select('price')
                ->whereColumn('product_id', 'products.id')
                ->orderBy('price', 'asc')
                ->limit(1)
            ])
            ->when($this->sort === 'low-high', function ($query) {
                $query->orderBy('lowest_price');
            }, function ($query) {
                $query->orderByDesc('lowest_price');
            })
            ->paginate(12);

    }
}

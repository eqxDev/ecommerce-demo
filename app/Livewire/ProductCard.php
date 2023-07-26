<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductCard extends Component
{
    public Product $product;
    public $min_variant_price = 0;
    public function mount()
    {
        $min_price_variant = $this->product->variants()->orderBy('price', 'asc')->first();
        if ($min_price_variant) {
            $this->min_variant_price = $min_price_variant->price;
        }
    }

    public function render()
    {
        return view('livewire.product-card');
    }
}

<?php

namespace App\Livewire;

use App\Models\ProductVariant;
use Auth;
use Livewire\Component;

class Basket extends Component
{

    protected $listeners = ['basketUpdated' => 'updateBasket'];

    public bool $open = false;
    public $items = [];

    public float $subtotal = 0;

    public function updateBasket()
    {
        $this->mount();
        $this->open = true;
    }

    public function mount()
    {
        $items = [];
        if (Auth::check()) {
            // Get the basket from the database for logged in users
            $basket = \App\Models\Basket::where('user_id', Auth::id())->first();
            $items = $basket ? $basket->items : [];
        } else {
            // Get the basket from the session for guest users
            $items = session()->get('basket', []);
        }

        $data = ProductVariant::query()
            ->whereIn('product_variants.id', array_keys($items))
            ->with('product', 'colour', 'size')
            ->get()
            ->keyBy('id')
            ->toArray();

        $this->subtotal = 0;
        foreach ($items as $variant_id => $quantity) {
            $items[$variant_id] = [
                'variant' => $data[$variant_id],
                'quantity' => $quantity,
            ];
            $this->subtotal +=  $data[$variant_id]['price'] * $quantity;
        }

        $this->items = $items;
    }

    public function remove(ProductVariant $variantId)
    {
        if ($variantId == null) {
            return;
        }
        $variantId = $variantId->id;

        if (Auth::check()) {
            // Remove the item from the database basket for logged in users
            $basket = \App\Models\Basket::where('user_id', Auth::id())->first();
            $items = $basket->items ?? [];
            if ($basket && array_key_exists($variantId, $items)) {
                unset($items[$variantId]);
            }
            $basket->items = $items;
            $basket->save();
        } else {
            // Remove the item from the session basket for guest users
            $basket = session()->get('basket', []);
            if (array_key_exists($variantId, $basket)) {
                unset($basket[$variantId]);
                session()->put('basket', $basket);
            }
        }

        // Refresh the items in the basket
        $this->mount();
    }

    public function render()
    {
        return view('livewire.basket');
    }
}

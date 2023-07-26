<?php

namespace App\Livewire;

use App\Models\Basket;
use App\Models\Product;
use Auth;
use Cache;
use Livewire\Component;
use Session;
use function Livewire\of;

class ProductView extends Component
{
    public Product $product;

    public $colour_id;
    public $size_id;

    public $colour_to_sizes_mapping = [];
    public $size_to_colours_mapping = [];
    public $colour_options = [];
    public $size_options = [];

    public function mount()
    {
        $this->colour_to_sizes_mapping = [];
        $this->size_to_colours_mapping = [];

        foreach ($this->product->variants as $variant) {
            $this->colour_to_sizes_mapping[$variant->colour_id][] = $variant->size_id;
            $this->size_to_colours_mapping[$variant->size_id][] = $variant->colour_id;
        }

        $this->colour_options = $this->product->variants->map(function ($data) {
            return [
                'colour_id' => $data->colour_id,
                'name' => $data->colour->name,
                'hex' => $data->colour->hex,
                'disabled' => $data->stock == 0,
            ];
        })->keyBy('colour_id')->unique()->toArray();

        $this->size_options = $this->product->variants->map(function ($data) {
            return [
                'size_id' => $data->size_id,
                'name' => $data->size->name,
                'disabled' => $data->stock == 0,
            ];
        })->sort(fn($a, $b) => $a['size_id'] <=> $b['size_id'])
            ->keyBy('size_id')->unique()->toArray();
    }

    public function setColour($id)
    {
        \Validator::validate([
            'id' => $id,
        ], [
            'id' => 'required|exists:colours,id',
        ]);

        if ($this->colour_options[$id]['disabled']) {
            return;
        }

        $this->colour_id = $id;

        // Filter size options based on the selected colour
        $allowed_sizes = $this->colour_to_sizes_mapping[$id] ?? [];
        foreach ($this->size_options as $key => $size_option) {
            $variant = $this->product->variants()->where([
                'colour_id' => $this->colour_id,
                'size_id' => $size_option['size_id'],
            ])->first();
            $this->size_options[$key]['disabled'] = !in_array($size_option['size_id'], $allowed_sizes) || ($variant && $variant->stock == 0);
        }
    }

    public function setSize($id)
    {
        \Validator::validate([
            'id' => $id,
        ], [
            'id' => 'required|exists:sizes,id',
        ]);

        if ($this->size_options[$id]['disabled']) {
            return;
        }

        $this->size_id = $id;

        // Filter colour options based on the selected size
        $allowed_colours = $this->size_to_colours_mapping[$id] ?? [];
        foreach ($this->colour_options as $key => $colour_option) {
            $variant = $this->product->variants()->where([
                'colour_id' => $colour_option['colour_id'],
                'size_id' => $this->size_id,
            ])->first();
            $this->colour_options[$key]['disabled'] = !in_array($colour_option['colour_id'], $allowed_colours) || ($variant && $variant->stock == 0);
        }
    }

    public function addToCart()
    {
        $this->validate([
            'colour_id' => 'required|exists:colours,id',
            'size_id' => 'required|exists:sizes,id',
        ]);

        // Start cache lock
        $lock = Cache::lock('product_variant_' . $this->product->id . '_' . $this->colour_id . '_' . $this->size_id, 10);

        if ($lock->get()) {
            $variant = $this->product->variants()
                ->where([
                    'colour_id' => $this->colour_id,
                    'size_id' => $this->size_id,
                ])
                ->first();

            // Check stock before adding to cart
            if ($variant->stock <= 0) {
                $lock->release();
                $this->redirect(route('product.view', $this->product->id));
                return;
            }

            $quantity = 1;
            if (Auth::check()) {
                // Update the basket in the database for logged in users
                $basket = Basket::firstOrCreate(['user_id' => Auth::id()]);
                $basketItems = $basket->items;
                if (isset($basketItems[$variant->id])) {
                    $basketItems[$variant->id] += $quantity;
                } else {
                    $basketItems[$variant->id] = $quantity;
                }
                $basket->items = $basketItems;
                $basket->save();
            } else {
                // Update the session for guest users
                $basket = Session::get('basket', []);
                if (isset($basket[$variant->id])) {
                    $basket[$variant->id] += $quantity;
                } else {
                    $basket[$variant->id] = $quantity;
                }
                Session::put('basket', $basket);
            }

            $this->dispatch('basketUpdated');

            // Release lock
            $lock->release();
        }
    }


    public function render()
    {
        return view('livewire.product-view')
            ->title($this->product->name);
    }
}

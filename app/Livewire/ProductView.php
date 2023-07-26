<?php

namespace App\Livewire;

use App\Models\Basket;
use App\Models\Product;
use Auth;
use Cache;
use Livewire\Component;
use Session;
use Str;
use function Livewire\of;

class ProductView extends Component
{
    public Product $product;

    public $colour_id;
    public $size_id;

    public $variantNotAvailable = false;
    public $min_variant_price = 0;
    public $variant_price = 0;
    public $colour_to_sizes_mapping = [];
    public $size_to_colours_mapping = [];
    public $colour_options = [];
    public $size_options = [];

    public function mount()
    {
        $this->mapVariant();
        $this->generateColourAndSizeOptions();
        $this->setMinVariantPrice();
    }

    public function mapVariant()
    {
        foreach ($this->product->variants as $variant) {
            $this->colour_to_sizes_mapping[$variant->colour_id][] = $variant->size_id;
            $this->size_to_colours_mapping[$variant->size_id][] = $variant->colour_id;
        }
    }

    public function generateColourAndSizeOptions()
    {
        $this->colour_options = $this->product->variants->map(fn($data) => [
            'colour_id' => $data->colour_id,
            'name' => $data->colour->name,
            'hex' => $data->colour->hex,
            'disabled' => $data->stock == 0,
        ])->keyBy('colour_id')->unique()->toArray();

        $this->size_options = $this->product->variants->map(fn($data) => [
            'size_id' => $data->size_id,
            'name' => $data->size->name,
            'disabled' => $data->stock == 0,
        ])->sort(fn($a, $b) => $a['size_id'] <=> $b['size_id'])
            ->keyBy('size_id')->unique()->toArray();
    }

    public function setMinVariantPrice()
    {
        $min_price_variant = $this->product->variants()->orderBy('price', 'asc')->first();
        if ($min_price_variant) {
            $this->min_variant_price = $min_price_variant->price;
        }
    }

    public function updateOptionsAvailability($selectedId, $variantDimension, $options, $mapping)
    {
        \Validator::validate(['id' => $selectedId], ['id' => 'required|exists:' . Str::plural($variantDimension) . ',id']);

        if ($options[$selectedId]['disabled']) {
            return;
        }

        $allowed_variants = $mapping[$selectedId] ?? [];
        foreach ($options as $key => $option) {
            $variant = $this->product->variants()->where([
                $variantDimension . '_id' => $selectedId,
                $this->oppositeDimension($variantDimension) . '_id' => $option[$this->oppositeDimension($variantDimension) . '_id'],
            ])->first();
            $options[$key]['disabled'] = !in_array($option[$this->oppositeDimension($variantDimension) . '_id'], $allowed_variants) || ($variant && $variant->stock == 0);
        }

        $this->updateVariantPriceAndAvailability();
    }

    public function setColour($id)
    {
        $this->colour_id = $id;
        $this->updateOptionsAvailability($id, 'colour', $this->size_options, $this->colour_to_sizes_mapping);
    }

    public function setSize($id)
    {
        $this->size_id = $id;
        $this->updateOptionsAvailability($id, 'size', $this->colour_options, $this->size_to_colours_mapping);
    }

    public function oppositeDimension($dimension)
    {
        return $dimension == 'size' ? 'colour' : 'size';
    }

    public function updateVariantPriceAndAvailability()
    {
        if ($this->colour_id !== null && $this->size_id !== null) {
            $variant = $this->product->variants()
                ->where([
                    'colour_id' => $this->colour_id,
                    'size_id' => $this->size_id,
                ])
                ->first();

            if ($variant) {
                $this->variant_price = $variant->price;

                // Using the function getCurrentBasketQuantity for basket quantity retrieval
                $currentQuantity = $this->getCurrentBasketQuantity($variant);
                $this->variantNotAvailable = ($currentQuantity + 1 > $variant->stock);
            }
        }
    }

    public function getCurrentBasketQuantity($variant)
    {
        if (Auth::check()) {
            $basket = Basket::firstOrCreate(['user_id' => Auth::id()]);
            $items = $basket->items ?? [];
            return $items[$variant->id] ?? 0;
        } else {
            $basket = Session::get('basket', []);
            return $basket[$variant->id] ?? 0;
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

            $this->updateVariantPriceAndAvailability();
            // Using the function updateBasket for updating basket

            $this->updateBasket($variant);

            // Release lock
            $lock->release();
        }
    }

    public function updateBasket($variant)
    {
        // if the total quantity exceeds the stock, don't proceed

        if ($this->variantNotAvailable) {

            session()->flash('error', 'The quantity you are trying to add exceeds the available stock.');
            return;
        }

        $quantity = 1; // set this to the quantity being added to the cart

        if (Auth::check()) {
            // Update the basket in the database for logged in users
            $basket = Basket::firstOrCreate(['user_id' => Auth::id()]);
            $basketItems = $basket->items ?? [];
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
    }

    public function render()
    {
        return view('livewire.product-view');
    }
}

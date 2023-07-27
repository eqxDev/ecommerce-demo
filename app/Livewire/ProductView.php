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

    public $variantNotAvailable = false;
    public $min_variant_price = 0;
    public $variant_price = 0;
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

        $min_price_variant = $this->product->variants()->orderBy('price', 'asc')->first();
        if ($min_price_variant) {
            $this->min_variant_price = $min_price_variant->price;
        }
    }

    public function setColour($id)
    {
        if (!$this->validateVariantId($id, 'colours')) {
            return;
        }

        $this->colour_id = $id;
        $this->filterSizeOptions();
        $this->updateVariantPriceAndAvailability();
    }

    public function setSize($id)
    {
        if (!$this->validateVariantId($id, 'sizes')) {
            return;
        }

        $this->size_id = $id;
        $this->filterColourOptions();
        $this->updateVariantPriceAndAvailability();
    }

    public function updateVariantPriceAndAvailability()
    {
        $variant = $this->findVariant();

        if ($variant) {
            $this->variant_price = $variant->price;
            $this->variantNotAvailable = $this->isVariantNotAvailable($variant);
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
            $variant = $this->findVariant();

            // Check stock before adding to cart
            if ($variant->stock <= 0) {
                $lock->release();
                $this->redirect(route('product.view', $this->product->id));
                return;
            }

            $this->updateVariantPriceAndAvailability();

            if ($this->variantNotAvailable) {
                session()->flash('error', 'The quantity you are trying to add exceeds the available stock.');
                $lock->release();
                return;
            }

            $this->updateBasket($variant);
            $this->dispatch('basketUpdated');

            // Release lock
            $lock->release();
        }
    }

    private function validateVariantId($id, $variantType)
    {
        \Validator::validate([
            'id' => $id,
        ], [
            'id' => 'required|exists:' . $variantType . ',id',
        ]);

        if ($variantType === 'colours' && $this->colour_options[$id]['disabled']) {
            return false;
        }

        if ($variantType === 'sizes' && $this->size_options[$id]['disabled']) {
            return false;
        }

        return true;
    }

    private function filterSizeOptions()
    {
        $allowed_sizes = $this->colour_to_sizes_mapping[$this->colour_id] ?? [];
        $this->filterOptions($this->size_options, $allowed_sizes, 'size_id');
    }

    private function filterColourOptions()
    {
        $allowed_colours = $this->size_to_colours_mapping[$this->size_id] ?? [];
        $this->filterOptions($this->colour_options, $allowed_colours, 'colour_id');
    }

    private function filterOptions(&$options, $allowedOptions, $optionId)
    {
        foreach ($options as $key => $option) {
            $variant = $this->product->variants()->where([
                'colour_id' => $this->colour_id,
                'size_id' => $option[$optionId],
            ])->first();
            $options[$key]['disabled'] = !in_array($option[$optionId], $allowedOptions) || ($variant && $variant->stock == 0);
        }
    }

    private function findVariant()
    {
        return $this->product->variants()
            ->where([
                'colour_id' => $this->colour_id,
                'size_id' => $this->size_id,
            ])
            ->first();
    }

    private function isVariantNotAvailable($variant)
    {
        $quantity = 1;
        $basket = $this->getBasket();
        $currentQuantity = $basket[$variant->id] ?? 0;

        return ($currentQuantity + $quantity > $variant->stock);
    }

    private function getBasket()
    {
        return Auth::check() ?
            Basket::firstOrCreate(['user_id' => Auth::id()])->items :
            Session::get('basket', []);
    }

    private function updateBasket($variant)
    {
        $quantity = 1; // set this to the quantity being added to the cart
        $basket = $this->getBasket();

        if (isset($basket[$variant->id])) {
            $basket[$variant->id] += $quantity;
        } else {
            $basket[$variant->id] = $quantity;
        }

        if (Auth::check()) {
            $this->updateBasketInDB($basket);
        } else {
            Session::put('basket', $basket);
        }
    }

    private function updateBasketInDB($basketItems)
    {
        $basket = Basket::firstOrCreate(['user_id' => Auth::id()]);
        $basket->items = $basketItems;
        $basket->save();
    }

    public function render()
    {
        return view('livewire.product-view')
            ->title($this->product->name);
    }
}

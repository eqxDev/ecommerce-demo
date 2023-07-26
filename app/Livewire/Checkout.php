<?php

namespace App\Livewire;

use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class Checkout extends Component
{
    public $items = [];

    public $subtotal = 0;
    public $vat = 0;
    public $total = 0;

    public $order_data = [
        'billing_name' => null,
        'billing_address' => null,
        'billing_address2' => null,
        'billing_city' => null,
        'billing_county' => null,
        'billing_postcode' => null,

        'shipping_copy' => true,
        'shipping_name' => null,
        'shipping_address' => null,
        'shipping_address2' => null,
        'shipping_city' => null,
        'shipping_county' => null,
        'shipping_postcode' => null,
    ];

    public function mount()
    {
        $items = \Auth::user()->basket->items ?? [];

        if (count($items) < 1) {
            return $this->redirect('/');
        }

        $data = ProductVariant::query()
            ->whereIn('product_variants.id', array_keys($items))
            ->with('product', 'colour', 'size')
            ->get()
            ->keyBy('id')
            ->toArray();

        foreach ($items as $variant_id => $quantity) {
            $items[$variant_id] = [
                'variant' => $data[$variant_id],
                'quantity' => $quantity,
            ];
            $this->subtotal += $data[$variant_id]['product']['price'] * $quantity;
        }

        $this->vat = round($this->subtotal * 0.2, 2);
        $this->total = round($this->subtotal + $this->vat, 2);
        $this->items = $items;
    }

    public function checkout()
    {
        $this->validate([
            'order_data.billing_name' => 'required|string',
            'order_data.billing_address' => 'required|string',
            'order_data.billing_address2' => 'sometimes|nullable|string',
            'order_data.billing_city' => 'required|string',
            'order_data.billing_county' => 'required|string',
            'order_data.billing_postcode' => 'required|string',
            'order_data.shipping_name' => 'required_if:order_data.shipping_copy,1|nullable|string',
            'order_data.shipping_address' => 'required_if:order_data.shipping_copy,1|nullable|string',
            'order_data.shipping_address2' => 'sometimes|nullable|string',
            'order_data.shipping_city' => 'required_if:order_data.shipping_copy,1|nullable|string',
            'order_data.shipping_county' => 'required_if:order_data.shipping_copy,1|nullable|string',
            'order_data.shipping_postcode' => 'required_if:order_data.shipping_copy,1|nullable|string',
        ]);

        // Holds locks
        $locks = [];

        // Try to acquire a lock for each item in the cart.
        foreach ($this->items as $item) {
            $locks[$item['variant']['id']] = Cache::lock('checkout_process_variant_' . $item['variant']['id'], 10);

            if (!$locks[$item['variant']['id']]->get()) {
                // Unable to acquire lock for this item, release all locks and return some kind of error.
                foreach ($locks as $lock) {
                    $lock->release();
                }
                return $this->addError('stock', 'Unable to checkout due to high demand. Please try again.');
            }
        }

        if ($this->order_data['shipping_copy']) {
            $this->order_data['shipping_name'] = $this->order_data['billing_name'];
            $this->order_data['shipping_address'] = $this->order_data['billing_address'];
            $this->order_data['shipping_address2'] = $this->order_data['billing_address2'];
            $this->order_data['shipping_city'] = $this->order_data['billing_city'];
            $this->order_data['shipping_county'] = $this->order_data['billing_county'];
            $this->order_data['shipping_postcode'] = $this->order_data['billing_postcode'];
        }


        try {
            // Starting transaction
            DB::beginTransaction();

            $data_encrypted = \Crypt::encrypt($this->order_data);
            $order = \Auth::user()->orders()->create([
                'data' => $data_encrypted,
                'total' => $this->total,
                'status' => 'pending',
            ]);

            foreach ($this->items as $item) {
                $variant = ProductVariant::find($item['variant']['id']);

                if ($variant->stock < $item['quantity']) {
                    DB::rollBack();  // Rolling back if stock is less

                    foreach ($locks as $lock) {
                        $lock->release();
                    }
                    return $this->addError('stock', 'Some items in your cart are out of stock.');
                }

                $variant->stock -= $item['quantity'];
                $variant->save();

                $order->items()->create([
                    'product_variant_id' => $item['variant']['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['variant']['product']['price'],
                ]);
            }

            DB::commit(); // Only if everything is successful, it'll reach here and changes will be saved

            \Auth::user()->basket()->delete();

            foreach ($locks as $lock) {
                $lock->release();
            }

            return $this->redirect(route('checkout.success', $order));

        } catch (\Exception $e) {
            // Something went wrong
            DB::rollBack(); // Rolling back in case of errors

            foreach ($locks as $lock) {
                $lock->release();
            }

            return $this->addError('stock', 'Something went wrong. Please try again.');
        }
    }


    public function render()
    {
        return view('livewire.checkout')->title('Checkout');
    }
}

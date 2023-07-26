<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;

class CheckoutSuccess extends Component
{
    public Order $order;

    public $items = [];
    public $subtotal = 0;
    public $vat = 0;

    public $order_data = [];

    public function mount(Order $order)
    {
        if ($order->user_id != \Auth::id()) {
            return $this->redirect(YourAccount::class);
        }

        $this->order_data = \Crypt::decrypt($order->data);
        $this->order = $order;

        foreach ($order->items as $item) {
            $this->items[$item->product_variant_id] = [
                'variant' => $item->productVariant,
                'quantity' => $item->quantity,
                'price' => $item->price
            ];
            $this->subtotal += $item->price * $item->quantity;
        }

        $this->vat = round($this->subtotal * 0.2, 2);
    }

    public function render()
    {
        return view('livewire.checkout-success');
    }
}

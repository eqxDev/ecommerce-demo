<?php
namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\Basket;

class BasketService
{
    public function moveSessionBasketToDatabase()
    {
        $sessionBasket = session()->get('basket', []);

        if(empty($sessionBasket)) {
            return;
        }

        $basket = Basket::firstOrCreate(['user_id' => Auth::id()]);
        $dbBasketItems = $basket->items ?? [];

        // Merge sessionBasket into basket in the database
        foreach($sessionBasket as $variantId => $quantity) {
            if (isset($dbBasketItems[$variantId])) {
                $dbBasketItems[$variantId] += $quantity;
            } else {
                $dbBasketItems[$variantId] = $quantity;
            }
        }

        $basket->items = $dbBasketItems;
        $basket->save();

        // Clear session basket
        session()->forget('basket');
    }
}

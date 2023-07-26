<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', \App\Livewire\Home::class);
Route::get('/shop/{product:id}', \App\Livewire\ProductView::class)->name('product.view');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/checkout', \App\Livewire\Checkout::class)->name('checkout');
    Route::get('/checkout/{order}/success', \App\Livewire\CheckoutSuccess::class)->name('checkout.success');
    Route::get('/your-account', \App\Livewire\YourAccount::class)->name('your-account');
});

Auth::routes();

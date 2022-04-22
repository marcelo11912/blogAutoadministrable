<?php

use App\Http\Controllers\PayPalController;
use App\Http\Livewire\Shop\Cart\IndexComponent as CartIndexComponent;
use App\Http\Livewire\Shop\CheckoutComponent;
use App\Http\Livewire\Shop\Products;
use App\Http\Livewire\Shop\RegisterComponent;
use App\Http\Livewire\Shop\SingleProduct;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function(){
    return view('front.index');
});

Route::get('products',Products::class)->name('all.products');
Route::get('/cart', CartIndexComponent::class)->name('cart');

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/checkout', CheckoutComponent::class)->name('checkout');

Route::get('/paypal/checkout/{order}', [PayPalController::class,'getExpressCheckout'])->name('paypal.checkout');
Route::get('/paypal-success/{order}', [PayPalController::class,'getExpressCheckoutSuccess'])->name('paypal.success');
Route::get('/paypal-cancel', [PayPalController::class,'cancelPage'])->name('paypal.cancel');

Route::get('/register-shop', RegisterComponent::class)->name('register.shop');

Route::get('/{product}',SingleProduct::class)->name('single.product');

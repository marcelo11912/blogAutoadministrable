<?php

namespace App\Http\Livewire\Shop;

use Srmklive\PayPal\Services\ExpressCheckout;
use App\Models\Order;
use Livewire\Component;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class CheckoutComponent extends Component
{

    public $user_id, $order_number, $status, $item_count, $is_paid, $payment_method, $total,
        $billing_first_name, $billing_last_name,
        $billing_company, $billing_country_region, $billing_address_street, $billing_address_apartment, $billing_town_city, $billing_zip, $billing_state,
        $billing_phone, $billing_email_address, $billing_ship_to_a_different_address,
        $shipping_first_name, $shipping_last_name, $shipping_company, $shipping_country_region, $shipping_address_street, $shipping_address_apartment,
        $shipping_town_city, $shipping_zip, $shipping_phone, $shipping_state, $shipping_email_address, $shipping_ship_to_a_different_address,
        $order_notes;
    public $email, $password, $remember,$logged_in;   
    public $token;
    public $countries,$states,$cities;
 

    public function mount()
    {
        $response = Http::withHeaders([
            "Accept" => "application/json",
            "api-token" => "oVeDpCw2mkc9p2QRQl_BE6wQjJHJBGDqVaSqeSqGT1ZTILGHu6rl_CTXAgMgXL5MUk8",
            "user-email" => "marcelo15052000@gmail.com"
        ])->get('https://www.universal-tutorial.com/api/getaccesstoken');

         $this->token = $response->json('auth_token');

         $countries = Http::withHeaders([
            "Authorization" => "Bearer ".$this->token,
            "Accept" => "application/json"
        ])->get('https://www.universal-tutorial.com/api/countries/');
        $this->countries= $countries->json();
        $this->states = [];
        $this->cities = [];
        
        if (Auth::check()) {
            $this->logged_in = true;
        } else {
            $this->logged_in = false;
        }
    }

    public function getStates(){
        $states = Http::withHeaders([
            "Authorization" => "Bearer ".$this->token,
            "Accept" => "application/json"
        ])->get('https://www.universal-tutorial.com/api/states/'.$this->billing_country_region);
        $this->states = $states->json();
      
    }

    public function getCities(){
        $cities = Http::withHeaders([
            "Authorization" => "Bearer ".$this->token,
            "Accept" => "application/json"
        ])->get('https://www.universal-tutorial.com/api/cities/'.$this->billing_state);
        $this->cities= $cities->json();
    
    }

    public function render()
    {
        return view('livewire.shop.checkout-component')->extends('layouts.front')->section('content');
    }

    public function make_order()
    {
        $this->validate([
            'fullname' => 'required',
            'address' => "required",
            'city' => "required",
            'state' => "required",
            'zipcode' => "required",
            'phone' => "required",
            'payment_method' => "required",
        ]);

        $order = new Order();
        $order->user_id = auth()->id();

        $order->order_number = uniqid('OrderNumber-');
        $order->item_count = Cart::session(auth()->id())->getContent()->count();


        $order->shipping_fullname = $this->fullname;
        $order->shipping_address = $this->address;
        $order->shipping_city = $this->city;
        $order->shipping_state = $this->state;
        $order->shipping_zipcode = $this->zipcode;
        $order->shipping_phone = $this->phone;
        $order->payment_method = $this->payment_method;
        $order->total = Cart::session(auth()->id())->getTotal();

        if (is_null($this->billing_fullname)) {
            $order->billing_fullname = $this->fullname;
            $order->billing_address = $this->address;
            $order->billing_city = $this->city;
            $order->billing_state = $this->state;
            $order->billing_zipcode = $this->zipcode;
            $order->billing_phone = $this->phone;
        } else {
            $order->billing_fullname = $this->billing_fullname;
            $order->billing_address = $this->billing_address;
            $order->billing_city = $this->billing_city;
            $order->billing_state = $this->billing_state;
            $order->billing_zipcode = $this->billing_zipcode;
            $order->billing_phone = $this->billing_phone;
        }

        $order->save();

        $order->generateSubOrders();

        $cartItems = Cart::session(auth()->id())->getContent();
        foreach ($cartItems as  $item) {
            $order->items()->attach($item->id, [
                'price' => $item->price,
                'quantity' => $item->quantity,
            ]);
        }

        if ($this->payment_method == "paypal") {
            return redirect()->route('paypal.checkout', $order->id);
        } else {
            Cart::session(auth()->id())->clear();
            return redirect()->route('shop.index');
        }
    }

    public function logIn()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $remember = $this->remember ? true : false;

        auth()->attempt([
            'email' => $this->email,
            'password' => $this->password
        ], $remember);
        //Rellenar los datos del formulario con los datos del usuarios
        $this->logged_in = true;
    }


}

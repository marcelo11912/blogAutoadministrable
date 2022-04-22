<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function allProducts(){
        return view('shop.products');
    }
}

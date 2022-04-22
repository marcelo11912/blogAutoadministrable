<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;


    public function items()
    {
        return $this->belongsToMany(Producto::class, 'order_items', 'order_id', 'producto_id')->withPivot('price','quantity');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subOrders(){
        return $this->hasMany(SubOrder::class);
    }

    public function generateSubOrders(){
        $items = $this->items;
        foreach ($items->groupBy('shop_id') as $shopId => $productos ) {
            $shop = Shop::find($shopId);
            $subOrders = $this->subOrders()->create([
                'order_id' => $this->id,
                'seller_id'=> $shop->user_id,
                'item_count'=> $productos->count(),
                'total'=> $productos->sum('pivot.price'),
            ]);

            foreach ($productos as $product) {
                $subOrders->items()->attach($productos->id,[
                    'price' => $product->pivot->price,
                    'quantity'=> $product->pivot->quantity,
                ]);
            }
        
        }
        
    }

}

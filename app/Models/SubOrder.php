<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'seller_id',
        'status',
        'item_count',
        'total',
    ];

    public function items()
    {
        return $this->belongsToMany(Producto::class, 'sub_order_items', 'sub_order_id', 'producto_id')->withPivot('price','quantity');
    }
}



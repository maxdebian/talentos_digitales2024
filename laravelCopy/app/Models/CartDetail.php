<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartDetail extends Model
{
    use HasFactory;
    protected $table='carts_details';
    protected $fillable=[
        'cart_id',
        'product_id',
        'cost_price',
        'increase',
        'count',
    ];
    protected $hidden=['created_at','updated_at'];
    /**
     * Get the product associated with the CartDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $table='carts';
    protected $fillable=[
        'date',
        'user_created',
        'user_updated',
    ];
    protected $hidden=['created_at','updated_at'];

    /**
     * Get the user_data associated with the Cart
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user_data()
    {
        return $this->hasOne(UserData::class, 'user_id', 'user_created');
    }

    /**
     * Get all of the carts_details for the Cart
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carts_details()
    {
        return $this->hasMany(CartDetail::class, 'cart_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table='products';
    protected $fillable=[
        'description',
        'product_image',
        'cost_price',
        'increase',
        'stock',
        'enabled',
        'user_created',
        'user_updated',
    ];
    protected $hidden=['deleted_at','created_at','updated_at'];

    /**
     * Get the user_created that owns the Provider
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user_created()
    {
        return $this->belongsTo(User::class, 'user_created');
    }
    /**
     * Get the user_updated that owns the Provider
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user_updated()
    {
        return $this->belongsTo(User::class, 'user_created');
    }
    /**
     * The product_provider that belong to the ProductProvider
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function product_provider()
    {
        return $this->belongsToMany(Provider::class, 'product_provider', 'product_id', 'provider_id');
    }

}

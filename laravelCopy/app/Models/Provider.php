<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table='providers';
    protected $fillable=[
        'first_name',
        'last_name',
        'dni',
        'address',
        'mobile',
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
     * The provider_product that belong to the Provider
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function provider_product()
    {
        return $this->belongsToMany(Product::class, 'product_provider', 'product_id', 'provider_id');
    }

}

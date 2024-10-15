<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObraSocial extends Model
{
    use HasFactory;
    protected $table = 'obras_sociales';
    protected $fillable = [
        'descripcion',
        'numero_seguro',
        'user_created',
        'user_updated'
    ];
    protected $hidden=['created_at','updated_at'];


}

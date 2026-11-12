<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    use SoftDeletes,HasFactory;

    protected $fillable = [
        'ruc',
        'razonsocial',
        'direccion',
        'telefono',
        'email'
    ];
}

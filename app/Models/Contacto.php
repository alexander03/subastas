<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contacto extends Model
{
    use SoftDeletes,HasFactory;

    protected $fillable = [
        'nombres',
        'apellidos',
        'tipodocumento',
        'numero',
        'correo',
        'telefono',
        'mensaje'
    ];
}

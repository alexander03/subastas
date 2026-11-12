<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opcionmenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'ruta',
        'icono',
        'orden',
        'grupomenu_id'
    ];

    public function grupomenu()
    {
        return $this->hasOne(Grupomenu::class,'id','grupomenu_id');
    }
}

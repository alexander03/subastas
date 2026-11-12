<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bien extends Model
{
    use SoftDeletes,HasFactory;

    protected $fillable = [
        'situacion',
        'valorreferencia',
        'proceso_id',
        'garantia',
        'producto_id',
        'bid_id',
        'segundoactual',
        'fechainicio',
        'fechafin'
    ];
    protected $with = ['producto'];

    public function proceso()
    {
        return $this->hasOne(Proceso::class,'id','proceso_id');
    }

    public function producto()
    {
        return $this->hasOne(Producto::class,'id','producto_id');
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'bien_id');
    }

    public function bid()
    {
        return $this->hasOne(Bid::class,'id','bid_id');
    }
}

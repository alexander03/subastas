<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Etapa extends Model
{
    use SoftDeletes,HasFactory;

    protected $fillable = [
        'tipo',
        'fechainicio',
        'fechafin',
        'situacion',
        'proceso_id',
        'etapa_id'
    ];

    public function proceso()
    {
        return $this->hasOne(Proceso::class,'id','proceso_id');
    }

    public function etapa()
    {
        return $this->hasOne(Etapa::class,'id','etapa_id');
    }
}

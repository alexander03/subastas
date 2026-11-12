<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Proceso extends Model implements HasMedia
{
    use SoftDeletes,HasFactory,InteractsWithMedia;

    protected $fillable = [
        'nombre',
        'descripcion',
        'fechainicio',
        'fechafin',
        'tipo',
        'situacion',
        'modalidad',
        'proceso_id',
        'bases',
        'preciobase',
        'tiempo',
        'user_id',
        'tiempo',
        'empresa_id'
    ];

    protected $with = ['media','bienes','empresa','etapas'];

    public function proceso()
    {
        return $this->hasOne(Proceso::class,'id','proceso_id');
    }

    public function bienes()
    {
        return $this->hasMany(Bien::class,'proceso_id','id');
    }

    public function etapas()
    {
        return $this->hasMany(Etapa::class,'proceso_id','id');
    }

    public function user()
    {
        return $this->belongsToMany(User::class, 'bases', 'proceso_id', 'usuario_id');
    }

    public function empresa()
    {
        return $this->hasOne(Empresa::class,'id','empresa_id');
    }
}

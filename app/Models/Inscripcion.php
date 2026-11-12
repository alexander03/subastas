<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Inscripcion extends Model implements HasMedia
{
    use SoftDeletes,HasFactory,InteractsWithMedia;

    protected $fillable = [
        'fecha',
        'monto',
        'situacion',
        'facturacion',
        'comentario',
        'etapa_id',
        'bien_id',
        'usuario_id'
    ];

    protected $with = ['media'];

    public function bien()
    {
        return $this->hasOne(Bien::class,'id','bien_id');
    }

    public function etapa()
    {
        return $this->hasOne(Etapa::class,'id','etapa_id');
    }

    public function usuario()
    {
        return $this->hasOne(User::class,'id','usuario_id');
    }

}

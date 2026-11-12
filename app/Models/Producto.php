<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class Producto extends Model implements HasMedia
{
    use SoftDeletes,HasFactory, InteractsWithMedia;
    protected $fillable = [
        'nombre',
        'descripcion',
        'valor',
        'situacion',
        'categoria_id',
        'distrito_id',
        'moneda'
    ];
    protected $with = ['categoria', 'media','distrito'];

    public function categoria()
    {
        return $this->hasOne(Categoria::class,'id','categoria_id');
    }

    public function distrito()
    {
        return $this->hasOne(Distrito::class,'id','distrito_id');
    }

    /*public function slider()
    {
        return $this->hasMany(Slider::class,'producto_id','id');
    }*/
}

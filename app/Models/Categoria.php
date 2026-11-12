<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Categoria extends Model implements HasMedia
{
    use SoftDeletes,HasFactory, InteractsWithMedia;
    
    protected $with = ['media'];

    protected $fillable = [
        'nombre',
        'descripcion'
    ];


}

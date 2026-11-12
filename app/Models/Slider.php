<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Slider extends Model  implements HasMedia
{
    use SoftDeletes,HasFactory, InteractsWithMedia;
    protected $fillable = [
        'producto_id',
    ];
    protected $with = ['media', 'producto'];

    public function producto()
    {
        return $this->hasOne(Producto::class,'id','producto_id');
    }
}

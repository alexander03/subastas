<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bases extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'proceso_id',
        'situacion',
        'comentario',
        'usuario_id'
    ];

    public function proceso()
    {
        return $this->hasOne(Proceso::class,'id','proceso_id');
    }

    public function usuario()
    {
        return $this->hasOne(User::class,'id','usuario_id');
    }
}

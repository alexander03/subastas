<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acceso extends Model
{
    use HasFactory;
    protected $fillable = [
        'opcionmenu_id',
        'tipousuario_id',
        'id'
    ];

    public function opcionmenu()
    {
        return $this->hasOne(Opcionmenu::class,'id','opcionmenu_id');
    }

    public function tipousuario()
    {
        return $this->hasOne(Tipousuario::class,'id','tipousuario_id');
    }

    public function scopeGetByTipousuario($query,$tipousuario_id) {
        return $query->join('opcionmenus','opcionmenus.id','=','accesos.opcionmenu_id')
            ->join('grupomenus','grupomenus.id','=','opcionmenus.grupomenu_id')
            ->where('tipousuario_id', '=', $tipousuario_id)
            ->orderBy('grupomenus.orden','asc')
            ->orderBy('opcionmenus.orden','asc')
            ->select('accesos.*');
    }
}

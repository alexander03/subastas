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

    /**
     * Etapas: 1 = Inscripción, 3 = Subasta.
     */
    public function etapaPorTipo($tipo)
    {
        return $this->etapas->first(function ($e) use ($tipo) {
            return (string) $e->tipo === (string) $tipo;
        });
    }

    /**
     * Estado de la ventana de inscripción: abierta, no_iniciada o cerrada.
     * Si el proceso no tiene etapa de inscripción no se restringe.
     */
    public function estadoInscripcion()
    {
        $etapa = $this->etapaPorTipo('1');
        if (is_null($etapa)) {
            return 'abierta';
        }
        $ahora = now();
        if ($ahora->lt(\Carbon\Carbon::parse($etapa->fechainicio))) {
            return 'no_iniciada';
        }
        if ($ahora->gt(\Carbon\Carbon::parse($etapa->fechafin))) {
            return 'cerrada';
        }
        return 'abierta';
    }

    /**
     * Hora en que empieza la subasta (etapa 3, o fecha de inicio del proceso).
     */
    public function inicioSubasta()
    {
        $etapa = $this->etapaPorTipo('3');
        $fecha = $etapa ? $etapa->fechainicio : $this->fechainicio;
        return $fecha ? \Carbon\Carbon::parse($fecha) : null;
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

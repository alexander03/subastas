<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    use HasFactory;

    protected $table = 'ubigeo_peru_provinces';

    protected $fillable = [
        'id',
        'name',
        'department_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'name' => 'string',
        'department_id' => 'string',
    ];

    public function departamento()
    {
        return $this->hasOne(Departamento::class,'id','department_id');
    }
}

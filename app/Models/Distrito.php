<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    use HasFactory;

    protected $table = 'ubigeo_peru_districts';

    protected $fillable = [
        'name',
        'province_id',
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
        'province_id' => 'string',
        'department_id' => 'string',
    ];

    protected $with = ['departamento','provincia'];

    public function departamento()
    {
        return $this->hasOne(Departamento::class,'id','department_id');
    }

    public function provincia()
    {
        return $this->hasOne(Provincia::class,'id','province_id');
    }
}

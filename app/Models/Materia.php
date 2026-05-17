<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id_materia';
    protected $fillable = ['nombre_materia'];

    public function expedientes()
    {
        return $this->hasMany(Expediente::class, 'id_materia', 'id_materia');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Juzgado extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id_juzgado';
    protected $fillable = ['nombre_juzgado'];

    public function expedientes()
    {
        return $this->hasMany(Expediente::class, 'id_juzgado', 'id_juzgado');
    }
}

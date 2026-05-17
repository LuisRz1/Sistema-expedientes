<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id_estado';
    protected $fillable = ['nombre_estado'];

    public function expedientes()
    {
        return $this->hasMany(Expediente::class, 'id_estado', 'id_estado');
    }
}

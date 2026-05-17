<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expediente extends Model
{
    protected $primaryKey = 'id_registro';
    protected $fillable = [
        'numero_expediente',
        'id_materia',
        'id_juzgado',
        'demandante',
        'demandado',
        'id_estado',
        'fecha_resolucion',
        'contenido_resolucion',
        'antecedentes',
        'observacion',
    ];

    protected $casts = [
        'fecha_resolucion' => 'date',
        'fecha_carga'      => 'datetime',
    ];

    const CREATED_AT = null;
    const UPDATED_AT = null;

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia', 'id_materia');
    }

    public function juzgado()
    {
        return $this->belongsTo(Juzgado::class, 'id_juzgado', 'id_juzgado');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
    }
}

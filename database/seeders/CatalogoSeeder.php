<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('juzgados')->insertOrIgnore([
            ['nombre_juzgado' => '1° JUZGADO CIVIL - TRUJILLO'],
            ['nombre_juzgado' => '1° JUZGADO MIXTO - SEDE LA ESPERANZA'],
            ['nombre_juzgado' => '1° SALA CIVIL'],
            ['nombre_juzgado' => '2° JUZGADO CIVIL - TRUJILLO'],
            ['nombre_juzgado' => '3° JUZGADO CIVIL - TRUJILLO'],
            ['nombre_juzgado' => '3° SALA CIVIL'],
            ['nombre_juzgado' => '4° JUZGADO CIVIL- TRUJILLO'],
            ['nombre_juzgado' => '5° JUZGADO CIVIL- TRUJILLO'],
            ['nombre_juzgado' => '6° JUZGADO CIVIL- TRUJILLO'],
            ['nombre_juzgado' => '7° JUZGADO CIVIL- TRUJILLO'],
            ['nombre_juzgado' => '7° JUZGADO CONSTITUCIONAL'],
            ['nombre_juzgado' => '8° JUZGADO CIVIL- TRUJILLO'],
            ['nombre_juzgado' => '9° JUZGADO CIVIL- TRUJILLO'],
            ['nombre_juzgado' => 'JUZGADO CIVIL - ASCOPE'],
            ['nombre_juzgado' => 'JUZGADO CIVIL- SEDE MBJ LA ESPERANZA'],
            ['nombre_juzgado' => 'JUZGADO CIVIL- SEDE PAIJAN'],
            ['nombre_juzgado' => 'JUZGADO CIVIL TRANSITORIO'],
            ['nombre_juzgado' => 'JUZGADO CIVIL- SEDE VIRU'],
            ['nombre_juzgado' => 'JUZGADO CIVIL TRANSITORIO - SEDE MBJ LA ESPERANZA'],
            ['nombre_juzgado' => 'JUZGADO CIVIL TRANSITORIO - SEDE ASCOPE'],
            ['nombre_juzgado' => 'JUZGADO CIVIL TRANSITORIO - VIRU'],
        ]);

        DB::table('materias')->insertOrIgnore([
            ['nombre_materia' => 'ACCION DE AMPARO'],
            ['nombre_materia' => 'DEMANDA - PRUEBA ANTICIPADA'],
            ['nombre_materia' => 'DESALOJO'],
            ['nombre_materia' => 'IMPUGNACIÓN DE ACTA O RESOLUCIÓN ADMINISTRATIVA'],
            ['nombre_materia' => 'INTERDICTO'],
            ['nombre_materia' => 'INTERDICTO DE RETENER'],
            ['nombre_materia' => 'MEJOR DERECHO DE POSESIÓN'],
            ['nombre_materia' => 'MEJOR DERECHO DE PROPIEDAD'],
            ['nombre_materia' => 'OBLIGACIÓN DE DAR SUMA DE DINERO'],
            ['nombre_materia' => 'PRESCRIPCIÓN ADQUISITIVA DE DOMINIO'],
            ['nombre_materia' => 'RECTIFICACIÓN DE ÁREAS Y LINDEROS'],
            ['nombre_materia' => 'REINVINDICACIÓN Y ACCESIÓN'],
            ['nombre_materia' => 'REINVINDICACIÓN'],
            ['nombre_materia' => 'REIVINDICACIÓN Y ACCESIÓN'],
            ['nombre_materia' => 'REIVINDICACIÓN'],
            ['nombre_materia' => 'REVISIÓN DE EXPROPIACIÓN'],
            ['nombre_materia' => 'REVISIÓN DEL VALOR DE TASACIÓN'],
            ['nombre_materia' => 'SERVIDUMBRE LEGAL DE PASO'],
            ['nombre_materia' => 'DECLARACION DE NULIDAD DE ACTO ADMINISTRATIVO'],
        ]);

        DB::table('estados')->insertOrIgnore([
            ['nombre_estado' => 'EXPEDIENTES EN TRAMITE'],
            ['nombre_estado' => 'EXPEDIENTES EN TRÁMITE'],
            ['nombre_estado' => 'EN TRAMITE'],
            ['nombre_estado' => 'EN TRÁMITE'],
            ['nombre_estado' => 'EN APELACION'],
            ['nombre_estado' => 'EN APELACIÓN'],
            ['nombre_estado' => 'IMPROCEDENTE'],
            ['nombre_estado' => 'EXPEDIENTES EN ARCHIVO'],
            ['nombre_estado' => 'ARCHIVO DEFINITIVO'],
            ['nombre_estado' => 'ARCHIVO PROVISIONAL'],
            ['nombre_estado' => 'EXPEDIENTES EN EJECUCION'],
            ['nombre_estado' => 'EXPEDIENTES EN EJECUCIÓN'],
            ['nombre_estado' => 'EN EJECUCION'],
            ['nombre_estado' => 'EN EJECUCIÓN'],
            ['nombre_estado' => 'CONCLUIDO/RESUELTO/SENTENCIADO'],
            ['nombre_estado' => 'CONCLUIDO-RESUELTO'],
            ['nombre_estado' => 'CONCLUIDO - RESUELTO'],
            ['nombre_estado' => 'RESUELTO'],
            ['nombre_estado' => 'RESUELTO/ATENDIDO'],
            ['nombre_estado' => 'SENTENCIADO /RESUELTO'],
            ['nombre_estado' => 'SENTENCIADO/ RESUELTO'],
            ['nombre_estado' => 'SENTENCIADO/RESUELTO'],
            ['nombre_estado' => 'SENTENCIADO - RESUELTO'],
            ['nombre_estado' => 'CON RESOLUCION CONSENTIDA'],
            ['nombre_estado' => 'CON RESOLUCIÓN CONSENTIDA'],
        ]);
    }
}

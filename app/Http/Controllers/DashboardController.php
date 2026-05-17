<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use App\Models\Expediente;
use App\Models\Juzgado;
use App\Models\Materia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Totals
            $total = Expediente::count();

            // Canonical estado groups: all DB variants collapse into one label
            $estadoGroups = [
                'EN TRAMITE'                     => ['EN TRAMITE','EN TRÁMITE','EXPEDIENTES EN TRAMITE','EXPEDIENTES EN TRÁMITE'],
                'EN APELACION'                   => ['EN APELACION','EN APELACIÓN'],
                'IMPROCEDENTE'                   => ['IMPROCEDENTE'],
                'EN ARCHIVO'                     => ['EXPEDIENTES EN ARCHIVO','ARCHIVO DEFINITIVO','ARCHIVO PROVISIONAL'],
                'EN EJECUCION'                   => ['EN EJECUCION','EN EJECUCIÓN','EXPEDIENTES EN EJECUCION','EXPEDIENTES EN EJECUCIÓN'],
                'CONCLUIDO/RESUELTO/SENTENCIADO' => ['CONCLUIDO/RESUELTO/SENTENCIADO','CONCLUIDO-RESUELTO','CONCLUIDO - RESUELTO','RESUELTO','RESUELTO/ATENDIDO','SENTENCIADO /RESUELTO','SENTENCIADO/ RESUELTO','SENTENCIADO/RESUELTO','SENTENCIADO - RESUELTO'],
                'CON RESOLUCION CONSENTIDA'      => ['CON RESOLUCION CONSENTIDA','CON RESOLUCIÓN CONSENTIDA'],
            ];

            $rawEstados = Estado::withCount('expedientes')->get();

            $porEstado = collect($estadoGroups)->map(function ($variants, $label) use ($rawEstados) {
                $count = $rawEstados->filter(function ($e) use ($variants) {
                    $name = mb_strtoupper(trim($e->nombre_estado));
                    return in_array($name, array_map('mb_strtoupper', $variants));
                })->sum('expedientes_count');
                return (object)['nombre_estado' => $label, 'expedientes_count' => $count];
            })->sortByDesc('expedientes_count')->values();

            // Full catalog for tables (show all, even with 0 expedientes)
            $porJuzgado = Juzgado::withCount('expedientes')
                ->orderByDesc('expedientes_count')
                ->orderBy('nombre_juzgado')
                ->get();

            $porMateria = Materia::withCount('expedientes')
                ->orderByDesc('expedientes_count')
                ->orderBy('nombre_materia')
                ->get();

            // Charts: keep top 15 juzgados for readability
            $porJuzgadoChart = $porJuzgado->take(15)->values();

            // By year
            $porAnio = Expediente::select(
                DB::raw('YEAR(fecha_resolucion) as anio'),
                DB::raw('COUNT(*) as total')
            )
                ->whereNotNull('fecha_resolucion')
                ->groupBy('anio')
                ->orderBy('anio')
                ->get();

            // Canonical estado groups (used in pivot tables)
            $canonicalGroups = [
                'EN TRAMITE'                     => ['EN TRAMITE','EN TRÁMITE','EXPEDIENTES EN TRAMITE','EXPEDIENTES EN TRÁMITE'],
                'EN APELACION'                   => ['EN APELACION','EN APELACIÓN'],
                'IMPROCEDENTE'                   => ['IMPROCEDENTE','EXPEDIENTES IMPROCEDENTES'],
                'EN ARCHIVO'                     => ['EXPEDIENTES EN ARCHIVO','ARCHIVO DEFINITIVO','ARCHIVO PROVISIONAL'],
                'EN EJECUCION'                   => ['EN EJECUCION','EN EJECUCIÓN','EXPEDIENTES EN EJECUCION','EXPEDIENTES EN EJECUCIÓN'],
                'CONCLUIDO/RESUELTO'             => ['CONCLUIDO/RESUELTO/SENTENCIADO','CONCLUIDO-RESUELTO','CONCLUIDO - RESUELTO','RESUELTO','RESUELTO/ATENDIDO','SENTENCIADO /RESUELTO','SENTENCIADO/ RESUELTO','SENTENCIADO/RESUELTO','SENTENCIADO - RESUELTO'],
                'CON RES. CONSENTIDA'            => ['CON RESOLUCION CONSENTIDA','CON RESOLUCIÓN CONSENTIDA'],
            ];

            // Build estado_id -> canonical_key map
            $estadoCatalog = Estado::all(['id_estado','nombre_estado']);
            $estadoCanonMap = []; // id_estado => canonical label
            foreach ($estadoCatalog as $e) {
                $name = mb_strtoupper(trim($e->nombre_estado));
                foreach ($canonicalGroups as $label => $variants) {
                    if (in_array($name, array_map('mb_strtoupper', $variants))) {
                        $estadoCanonMap[$e->id_estado] = $label;
                        break;
                    }
                }
            }

            // Pivot: juzgado x canonical estado
            $pivotJuzgado = DB::table('expedientes')
                ->join('juzgados', 'expedientes.id_juzgado', '=', 'juzgados.id_juzgado')
                ->select('juzgados.id_juzgado', 'juzgados.nombre_juzgado', 'expedientes.id_estado', DB::raw('COUNT(*) as cnt'))
                ->groupBy('juzgados.id_juzgado', 'juzgados.nombre_juzgado', 'expedientes.id_estado')
                ->get()
                ->groupBy('id_juzgado');

            // Pivot: materia x canonical estado
            $pivotMateria = DB::table('expedientes')
                ->join('materias', 'expedientes.id_materia', '=', 'materias.id_materia')
                ->select('materias.id_materia', 'materias.nombre_materia', 'expedientes.id_estado', DB::raw('COUNT(*) as cnt'))
                ->groupBy('materias.id_materia', 'materias.nombre_materia', 'expedientes.id_estado')
                ->get()
                ->groupBy('id_materia');

            return view('dashboard.index', compact(
                'total',
                'porEstado',
                'porJuzgado',
                'porJuzgadoChart',
                'porMateria',
                'porAnio',
                'canonicalGroups',
                'estadoCanonMap',
                'pivotJuzgado',
                'pivotMateria'
            ));
        } catch (Throwable $e) {
            Log::error('Dashboard failed to load', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $porEstado = collect();
            $porJuzgado = collect();
            $porJuzgadoChart = collect();
            $porMateria = collect();
            $porAnio = collect();
            $canonicalGroups = [];
            $estadoCanonMap = [];
            $pivotJuzgado = collect();
            $pivotMateria = collect();
            $total = 0;

            return view('dashboard.index', compact(
                'total',
                'porEstado',
                'porJuzgado',
                'porJuzgadoChart',
                'porMateria',
                'porAnio',
                'canonicalGroups',
                'estadoCanonMap',
                'pivotJuzgado',
                'pivotMateria'
            ))->with('error', 'No se pudo cargar la data del dashboard. Revisa los logs de Railway para el detalle tecnico.');
        }
    }
}

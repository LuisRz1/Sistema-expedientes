<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use App\Models\Expediente;
use App\Models\Juzgado;
use App\Models\Materia;
use Illuminate\Http\Request;

class ExpedienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Expediente::with(['juzgado', 'materia', 'estado']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('numero_expediente', 'like', "%{$s}%")
                  ->orWhere('demandante', 'like', "%{$s}%")
                  ->orWhere('demandado', 'like', "%{$s}%");
            });
        }

        if ($request->filled('id_juzgado')) {
            $query->where('id_juzgado', $request->id_juzgado);
        }

        if ($request->filled('id_materia')) {
            $query->where('id_materia', $request->id_materia);
        }

        if ($request->filled('id_estado')) {
            $query->where('id_estado', $request->id_estado);
        }

        if ($request->filled('anio')) {
            $query->whereYear('fecha_resolucion', $request->anio);
        }

        $expedientes = $query->orderByDesc('id_registro')->paginate(20)->withQueryString();
        $juzgados    = Juzgado::orderBy('nombre_juzgado')->get();
        $materias    = Materia::orderBy('nombre_materia')->get();
        $estados     = Estado::withCount('expedientes')->orderBy('nombre_estado')->get();

        return view('expedientes.index', compact('expedientes', 'juzgados', 'materias', 'estados'));
    }

    public function create()
    {
        $juzgados = Juzgado::orderBy('nombre_juzgado')->get();
        $materias = Materia::orderBy('nombre_materia')->get();
        $estados  = Estado::orderBy('nombre_estado')->get();
        return view('expedientes.create', compact('juzgados', 'materias', 'estados'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'numero_expediente'    => 'required|string|max:50|unique:expedientes,numero_expediente',
            'id_materia'           => 'nullable|exists:materias,id_materia',
            'id_juzgado'           => 'nullable|exists:juzgados,id_juzgado',
            'demandante'           => 'nullable|string',
            'demandado'            => 'nullable|string',
            'id_estado'            => 'nullable|exists:estados,id_estado',
            'fecha_resolucion'     => 'nullable|date',
            'contenido_resolucion' => 'nullable|string',
            'antecedentes'         => 'nullable|string',
        ]);

        Expediente::create($data);

        return redirect()->route('expedientes.index')
            ->with('success', 'Expediente creado correctamente.');
    }

    public function show(Expediente $expediente)
    {
        $expediente->load(['juzgado', 'materia', 'estado']);
        return view('expedientes.show', compact('expediente'));
    }

    public function edit(Expediente $expediente)
    {
        $juzgados = Juzgado::orderBy('nombre_juzgado')->get();
        $materias = Materia::orderBy('nombre_materia')->get();
        $estados  = Estado::orderBy('nombre_estado')->get();
        return view('expedientes.edit', compact('expediente', 'juzgados', 'materias', 'estados'));
    }

    public function update(Request $request, Expediente $expediente)
    {
        $data = $request->validate([
            'numero_expediente'    => 'required|string|max:50|unique:expedientes,numero_expediente,' . $expediente->id_registro . ',id_registro',
            'id_materia'           => 'nullable|exists:materias,id_materia',
            'id_juzgado'           => 'nullable|exists:juzgados,id_juzgado',
            'demandante'           => 'nullable|string',
            'demandado'            => 'nullable|string',
            'id_estado'            => 'nullable|exists:estados,id_estado',
            'fecha_resolucion'     => 'nullable|date',
            'contenido_resolucion' => 'nullable|string',
            'antecedentes'         => 'nullable|string',
        ]);

        $expediente->update($data);

        return redirect()->route('expedientes.index')
            ->with('success', 'Expediente actualizado correctamente.')
            ->with('updated_id', $expediente->id_registro);
    }

    public function destroy(Expediente $expediente)
    {
        $expediente->delete();
        return redirect()->route('expedientes.index')
            ->with('success', 'Expediente eliminado correctamente.');
    }
}

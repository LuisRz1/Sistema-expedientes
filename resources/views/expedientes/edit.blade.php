@extends('layouts.app')
@section('title', 'Editar Expediente')
@section('page-title', 'Editar Expediente: ' . $expediente->numero_expediente)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card">
        <form action="{{ route('expedientes.update', $expediente) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')
            @include('expedientes._form')
            <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Actualizar Expediente
                </button>
                <a href="{{ route('expedientes.index') }}" class="btn-secondary">Cancelar</a>
                <div class="ml-auto">
                    <button type="button" onclick="document.getElementById('delete-form').submit()"
                            class="btn-danger"
                            onclick="return confirm('¿Eliminar este expediente?')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Eliminar
                    </button>
                </div>
            </div>
        </form>

        <form id="delete-form" action="{{ route('expedientes.destroy', $expediente) }}" method="POST"
              onsubmit="return confirm('¿Eliminar este expediente?')" class="hidden">
            @csrf @method('DELETE')
        </form>
    </div>
</div>
@endsection

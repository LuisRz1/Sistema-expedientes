@extends('layouts.app')
@section('title', 'Resultado de Importación')
@section('page-title', 'Resultado de Importación')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card text-center">
            <p class="text-3xl font-bold text-teal-600">{{ $totalImported }}</p>
            <p class="text-sm text-slate-500 mt-1">Expedientes importados</p>
        </div>
        <div class="card text-center">
            <p class="text-3xl font-bold text-amber-500">{{ $totalSkipped }}</p>
            <p class="text-sm text-slate-500 mt-1">Filas omitidas</p>
        </div>
        <div class="card text-center">
            <p class="text-3xl font-bold text-red-500">{{ count($allErrors) }}</p>
            <p class="text-sm text-slate-500 mt-1">Errores</p>
        </div>
    </div>

    @if(count($allErrors) > 0)
    <div class="card">
        <h3 class="text-sm font-semibold text-red-600 mb-3">Detalle de errores</h3>
        <ul class="space-y-1 max-h-64 overflow-y-auto">
            @foreach($allErrors as $err)
            <li class="text-xs text-red-700 bg-red-50 px-3 py-1.5 rounded">{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="flex gap-3">
        <a href="{{ route('expedientes.index') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Ver Expedientes
        </a>
        <a href="{{ route('import.index') }}" class="btn-secondary">Importar otro archivo</a>
    </div>
</div>
@endsection

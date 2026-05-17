@extends('layouts.app')
@section('title', 'Expediente ' . $expediente->numero_expediente)
@section('page-title', 'Expediente: ' . $expediente->numero_expediente)

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('expedientes.index') }}" class="btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Volver
        </a>
        <a href="{{ route('expedientes.edit', $expediente) }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Editar
        </a>
    </div>

    <div class="card">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">N° Expediente</dt>
                <dd class="text-base font-bold text-primary-700">{{ $expediente->numero_expediente }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Estado</dt>
                <dd>{{ $expediente->estado?->nombre_estado ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Materia</dt>
                <dd class="text-slate-700">{{ $expediente->materia?->nombre_materia ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Juzgado</dt>
                <dd class="text-slate-700">{{ $expediente->juzgado?->nombre_juzgado ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Demandante</dt>
                <dd class="text-slate-700">{{ $expediente->demandante ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Demandado</dt>
                <dd class="text-slate-700">{{ $expediente->demandado ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Fecha de Resolución</dt>
                <dd class="text-slate-700">{{ $expediente->fecha_resolucion ? $expediente->fecha_resolucion->format('d/m/Y') : '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Fecha de Carga</dt>
                <dd class="text-slate-500 text-xs">{{ $expediente->fecha_carga?->format('d/m/Y H:i') ?? '—' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Contenido de la Resolución Judicial</dt>
                <dd class="text-slate-700 whitespace-pre-line text-sm leading-relaxed bg-slate-50 rounded-lg p-3">
                    {{ $expediente->contenido_resolucion ?? '—' }}
                </dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Antecedentes</dt>
                <dd class="text-slate-700 whitespace-pre-line text-sm leading-relaxed bg-slate-50 rounded-lg p-3">
                    {{ $expediente->antecedentes ?? '—' }}
                </dd>
            </div>
        </dl>
    </div>
</div>
@endsection

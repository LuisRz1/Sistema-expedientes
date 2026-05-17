@extends('layouts.app')
@section('title', 'Nuevo Expediente')
@section('page-title', 'Nuevo Expediente')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card">
        <form action="{{ route('expedientes.store') }}" method="POST" class="space-y-6">
            @csrf
            @include('expedientes._form', ['expediente' => null])
            <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Guardar Expediente
                </button>
                <a href="{{ route('expedientes.index') }}" class="btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

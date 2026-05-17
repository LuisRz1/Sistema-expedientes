@extends('layouts.app')
@section('title', 'Seleccionar Hojas')
@section('page-title', 'Seleccionar Hojas a Importar')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="card">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            </div>
            <div>
                <h2 class="font-semibold text-slate-800">Hojas detectadas en el archivo</h2>
                <p class="text-xs text-slate-500">Selecciona las hojas que deseas importar</p>
            </div>
        </div>

        <form action="{{ route('import.process') }}" method="POST" class="space-y-5">
            @csrf

            <div class="space-y-2">
                @foreach($sheetNames as $index => $name)
                <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-primary-400 has-[:checked]:bg-primary-50">
                    <input type="checkbox" name="sheets[]" value="{{ $name }}"
                           class="w-4 h-4 accent-teal-600" checked>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-slate-700">{{ $name }}</p>
                        <p class="text-xs text-slate-400">Hoja {{ $index + 1 }}</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </label>
                @endforeach
            </div>

            @error('sheets') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

            <div class="flex gap-3">
                <button type="submit" class="btn-primary flex-1 justify-center py-2.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Importar Seleccionadas
                </button>
                <a href="{{ route('import.index') }}" class="btn-secondary flex-shrink-0">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

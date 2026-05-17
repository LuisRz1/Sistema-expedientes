@extends('layouts.app')
@section('title', 'Importar Excel')
@section('page-title', 'Importar Excel')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="card">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            </div>
            <div>
                <h2 class="font-semibold text-slate-800">Importar desde Excel</h2>
                <p class="text-xs text-slate-500">Sube tu archivo .xlsx con expedientes</p>
            </div>
        </div>

        <form action="{{ route('import.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Archivo Excel (.xlsx / .xls)</label>
                <div class="border-2 border-dashed border-slate-200 rounded-xl p-8 text-center hover:border-primary-400 transition-colors cursor-pointer"
                     onclick="document.getElementById('excel_file').click()">
                    <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-sm text-slate-500" id="file-label">Haz clic para seleccionar tu archivo</p>
                    <p class="text-xs text-slate-400 mt-1">XLSX, XLS · máx. 20 MB</p>
                </div>
                <input type="file" id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv"
                       class="hidden" onchange="updateLabel(this)" required>
                @error('excel_file') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-xs text-blue-700 space-y-1">
                <p class="font-semibold">Columnas esperadas en el Excel:</p>
                <p>N° · EXPEDIENTE · MATERIA · JUZGADO · DEMANDANTE · DEMANDADO · ESTADO · FECHA DE RESOLUCIÓN JUDICIAL · CONTENIDO DE LA RESOLUCIÓN · ANTECEDENTES</p>
                <p class="mt-2">Los valores de Juzgado, Materia y Estado se mapearán automáticamente aunque vengan escritos de forma diferente.</p>
            </div>

            <button type="submit" class="btn-primary w-full justify-center py-2.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Subir y Ver Hojas
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateLabel(input) {
    const label = document.getElementById('file-label');
    if (input.files && input.files[0]) {
        label.textContent = input.files[0].name;
        label.classList.add('text-primary-700', 'font-medium');
    }
}
</script>
@endpush

{{-- Partial: shared form fields for create/edit --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    {{-- Número de Expediente --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">N° Expediente <span class="text-red-500">*</span></label>
        <input type="text" name="numero_expediente"
               value="{{ old('numero_expediente', $expediente->numero_expediente ?? '') }}"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('numero_expediente') border-red-400 @enderror"
               placeholder="ej. 92-2010" required>
        @error('numero_expediente') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Juzgado --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Juzgado</label>
        <select name="id_juzgado" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <option value="">— Sin juzgado —</option>
            @foreach($juzgados as $j)
                <option value="{{ $j->id_juzgado }}" {{ old('id_juzgado', $expediente->id_juzgado ?? '') == $j->id_juzgado ? 'selected' : '' }}>
                    {{ $j->nombre_juzgado }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Materia --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Materia Procesal</label>
        <select name="id_materia" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <option value="">— Sin materia —</option>
            @foreach($materias as $m)
                <option value="{{ $m->id_materia }}" {{ old('id_materia', $expediente->id_materia ?? '') == $m->id_materia ? 'selected' : '' }}>
                    {{ $m->nombre_materia }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Estado --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Estado</label>
        <select name="id_estado" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <option value="">— Sin estado —</option>
            @foreach($estados as $e)
                <option value="{{ $e->id_estado }}" {{ old('id_estado', $expediente->id_estado ?? '') == $e->id_estado ? 'selected' : '' }}>
                    {{ $e->nombre_estado }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Fecha resolución --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Fecha de Resolución Judicial</label>
        <input type="date" name="fecha_resolucion"
               value="{{ old('fecha_resolucion', isset($expediente->fecha_resolucion) ? $expediente->fecha_resolucion->format('Y-m-d') : '') }}"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
    </div>

    {{-- Demandante --}}
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Demandante</label>
        <input type="text" name="demandante"
               value="{{ old('demandante', $expediente->demandante ?? '') }}"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
    </div>

    {{-- Demandado --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1">Demandado</label>
        <input type="text" name="demandado"
               value="{{ old('demandado', $expediente->demandado ?? '') }}"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
    </div>

    {{-- Contenido resolución --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1">Contenido de la Resolución Judicial</label>
        <textarea name="contenido_resolucion" rows="4"
                  class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-y">{{ old('contenido_resolucion', $expediente->contenido_resolucion ?? '') }}</textarea>
    </div>

    {{-- Antecedentes --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1">Antecedentes</label>
        <textarea name="antecedentes" rows="3"
                  class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-y">{{ old('antecedentes', $expediente->antecedentes ?? '') }}</textarea>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
    <div>
        <label class="form-label">N° Expediente <span style="color:#dc2626">*</span></label>
        <input type="text" name="numero_expediente" class="form-input"
               value="{{ old('numero_expediente') }}" placeholder="ej. 92-2010" required>
        @error('numero_expediente') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="form-label">Juzgado</label>
        <select name="id_juzgado" class="form-select">
            <option value="">— Sin juzgado —</option>
            @foreach($juzgados as $j)
                <option value="{{ $j->id_juzgado }}" {{ old('id_juzgado') == $j->id_juzgado ? 'selected' : '' }}>{{ $j->nombre_juzgado }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label">Materia Procesal</label>
        <select name="id_materia" class="form-select">
            <option value="">— Sin materia —</option>
            @foreach($materias as $m)
                <option value="{{ $m->id_materia }}" {{ old('id_materia') == $m->id_materia ? 'selected' : '' }}>{{ $m->nombre_materia }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label">Estado</label>
        <select name="id_estado" class="form-select">
            <option value="">— Sin estado —</option>
            @foreach($estados as $e)
                <option value="{{ $e->id_estado }}" {{ old('id_estado') == $e->id_estado ? 'selected' : '' }}>{{ $e->nombre_estado }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="form-label">Fecha de Resolución Judicial</label>
        <input type="date" name="fecha_resolucion" class="form-input" value="{{ old('fecha_resolucion') }}">
    </div>

    <div>
        <label class="form-label">Demandante</label>
        <input type="text" name="demandante" class="form-input" value="{{ old('demandante') }}">
    </div>

    <div style="grid-column:span 2">
        <label class="form-label">Demandado</label>
        <input type="text" name="demandado" class="form-input" value="{{ old('demandado') }}">
    </div>

    <div style="grid-column:span 2">
        <label class="form-label">Contenido de la Resolución Judicial</label>
        <textarea name="contenido_resolucion" class="form-textarea" style="min-height:90px">{{ old('contenido_resolucion') }}</textarea>
    </div>

    <div style="grid-column:span 2">
        <label class="form-label">Antecedentes</label>
        <textarea name="antecedentes" class="form-textarea" style="min-height:70px">{{ old('antecedentes') }}</textarea>
    </div>
</div>

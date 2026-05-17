@extends('layouts.app')
@section('title', 'Expedientes')
@section('page-title', 'Expedientes Judiciales')

@section('content')

{{-- ══════════════════════════════════════════
     TOP BAR
══════════════════════════════════════════ --}}
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px">
    <div>
        <p style="font-size:.82rem;color:#64748b;margin-top:2px">
            {{ $expedientes->total() }} expediente(s) encontrado(s)
        </p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        <button onclick="openModal('modal-import')" class="btn btn-accent">
            <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Importar Excel
        </button>
        <button onclick="openModal('modal-create')" class="btn btn-primary">
            <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo Expediente
        </button>
    </div>
</div>

{{-- ══════════════════════════════════════════
     STATS
══════════════════════════════════════════ --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:14px;margin-bottom:22px">
    @foreach($estados as $est)
    @php
        $colors = [
            'EXPEDIENTES EN TRÁMITE'         => ['bg'=>'#eff6ff','icon'=>'#3b82f6','border'=>'#bfdbfe'],
            'EN APELACIÓN'                   => ['bg'=>'#fffbeb','icon'=>'#f59e0b','border'=>'#fde68a'],
            'IMPROCEDENTE'                   => ['bg'=>'#fef2f2','icon'=>'#ef4444','border'=>'#fecaca'],
            'EXPEDIENTES EN ARCHIVO'         => ['bg'=>'#f8fafc','icon'=>'#94a3b8','border'=>'#e2e8f0'],
            'EXPEDIENTES EN EJECUCIÓN'       => ['bg'=>'#f0fdfa','icon'=>'#0f766e','border'=>'#99f6e4'],
            'CONCLUIDO/RESUELTO/SENTENCIADO' => ['bg'=>'#f0fdf4','icon'=>'#16a34a','border'=>'#bbf7d0'],
            'CON RESOLUCIÓN CONSENTIDA'      => ['bg'=>'#faf5ff','icon'=>'#9333ea','border'=>'#e9d5ff'],
        ];
        $c = $colors[$est->nombre_estado] ?? ['bg'=>'#f8fafc','icon'=>'#64748b','border'=>'#e2e8f0'];
        $cnt = $est->expedientes_count ?? 0;
    @endphp
    <div class="card stat-card" style="border-color:{{ $c['border'] }};padding:16px">
        <div class="stat-icon" style="background:{{ $c['bg'] }};width:36px;height:36px;border-radius:10px">
            <svg style="width:16px;height:16px;color:{{ $c['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div style="min-width:0">
            <p style="font-size:1.35rem;font-weight:800;color:#0f172a;line-height:1.1">{{ $cnt }}</p>
            <p style="font-size:.68rem;color:#64748b;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="{{ $est->nombre_estado }}">{{ $est->nombre_estado }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- ══════════════════════════════════════════
     FILTERS + TABLE
══════════════════════════════════════════ --}}
<div class="card" style="padding:0;overflow:hidden">

    {{-- Filter bar --}}
    <div style="padding:16px 18px;border-bottom:1px solid #f1f5f9;display:flex;flex-wrap:wrap;gap:10px;align-items:center">
        <form method="GET" action="{{ route('expedientes.index') }}" id="filter-form"
              style="display:contents">
            <div class="search-wrap" style="flex:1;min-width:200px">
                <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Buscar expediente, demandante o demandado…"
                       class="form-input" style="padding-left:36px">
            </div>

            <select name="id_juzgado" class="form-select" style="min-width:180px;max-width:220px" onchange="this.form.submit()">
                <option value="">Todos los Juzgados</option>
                @foreach($juzgados as $j)
                    <option value="{{ $j->id_juzgado }}" {{ request('id_juzgado') == $j->id_juzgado ? 'selected' : '' }}>
                        {{ $j->nombre_juzgado }}
                    </option>
                @endforeach
            </select>

            <select name="id_materia" class="form-select" style="min-width:180px;max-width:220px" onchange="this.form.submit()">
                <option value="">Todas las Materias</option>
                @foreach($materias as $m)
                    <option value="{{ $m->id_materia }}" {{ request('id_materia') == $m->id_materia ? 'selected' : '' }}>
                        {{ $m->nombre_materia }}
                    </option>
                @endforeach
            </select>

            <select name="id_estado" class="form-select" style="min-width:160px;max-width:200px" onchange="this.form.submit()">
                <option value="">Todos los Estados</option>
                @foreach($estados as $e)
                    <option value="{{ $e->id_estado }}" {{ request('id_estado') == $e->id_estado ? 'selected' : '' }}>
                        {{ $e->nombre_estado }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-ghost btn-sm">
                <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Filtrar
            </button>
            @if(request()->hasAny(['search','id_juzgado','id_materia','id_estado']))
                <a href="{{ route('expedientes.index') }}" class="btn btn-sm" style="background:#f1f5f9;color:#64748b">
                    <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div style="overflow-x:auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>N° Expediente</th>
                    <th>Materia</th>
                    <th>Juzgado</th>
                    <th>Demandante</th>
                    <th>Demandado</th>
                    <th>Estado</th>
                    <th>Fecha Res.</th>
                    <th style="text-align:right">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($expedientes as $exp)
            @php
                $estadoColors = [
                    'EXPEDIENTES EN TRÁMITE'         => 'background:#dbeafe;color:#1d4ed8',
                    'EN APELACIÓN'                   => 'background:#fef3c7;color:#b45309',
                    'IMPROCEDENTE'                   => 'background:#fee2e2;color:#dc2626',
                    'EXPEDIENTES EN ARCHIVO'         => 'background:#f1f5f9;color:#64748b',
                    'EXPEDIENTES EN EJECUCIÓN'       => 'background:#ccfbf1;color:#0f766e',
                    'CONCLUIDO/RESUELTO/SENTENCIADO' => 'background:#dcfce7;color:#16a34a',
                    'CON RESOLUCIÓN CONSENTIDA'      => 'background:#f3e8ff;color:#9333ea',
                ];
                $badgeStyle = $estadoColors[$exp->estado?->nombre_estado] ?? 'background:#f1f5f9;color:#64748b';
            @endphp
            <tr id="row-{{ $exp->id_registro }}">
                <td>
                    <button onclick="openDetail({{ $exp->id_registro }})"
                            style="font-weight:700;color:#0f766e;font-size:.84rem;background:none;border:none;cursor:pointer;padding:0;text-decoration:underline;text-underline-offset:2px">
                        {{ $exp->numero_expediente }}
                    </button>
                </td>
                <td style="max-width:160px">
                    <span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $exp->materia?->nombre_materia }}">
                        {{ $exp->materia?->nombre_materia ?? '—' }}
                    </span>
                </td>
                <td style="max-width:170px">
                    <span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:.8rem;color:#64748b" title="{{ $exp->juzgado?->nombre_juzgado }}">
                        {{ $exp->juzgado?->nombre_juzgado ?? '—' }}
                    </span>
                </td>
                <td style="max-width:150px">
                    <span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $exp->demandante }}">
                        {{ $exp->demandante ?? '—' }}
                    </span>
                </td>
                <td style="max-width:150px">
                    <span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $exp->demandado }}">
                        {{ $exp->demandado ?? '—' }}
                    </span>
                </td>
                <td>
                    @if($exp->estado)
                        <span class="badge" style="{{ $badgeStyle }}">{{ $exp->estado->nombre_estado }}</span>
                    @else
                        <span style="color:#cbd5e1">—</span>
                    @endif
                </td>
                <td style="white-space:nowrap;color:#64748b;font-size:.8rem">
                    {{ $exp->fecha_resolucion ? $exp->fecha_resolucion->format('d/m/Y') : '—' }}
                </td>
                <td>
                    <div style="display:flex;gap:4px;justify-content:flex-end">
                        {{-- Ver --}}
                        <button onclick="openDetail({{ $exp->id_registro }})" class="btn btn-icon btn-sm"
                                title="Ver detalle" style="background:#f0fdfa;color:#0f766e;border:1px solid #99f6e4">
                            <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                        {{-- Editar --}}
                        <button onclick="openEdit({{ $exp->id_registro }})" class="btn btn-icon btn-sm"
                                title="Editar" style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe">
                            <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        {{-- Eliminar --}}
                        <button onclick="openDelete({{ $exp->id_registro }}, '{{ addslashes($exp->numero_expediente) }}')"
                                class="btn btn-icon btn-sm" title="Eliminar"
                                style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca">
                            <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="padding:60px 20px;text-align:center">
                    <svg style="width:48px;height:48px;color:#e2e8f0;margin:0 auto 12px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p style="color:#94a3b8;font-size:.9rem;font-weight:500">No se encontraron expedientes</p>
                    <p style="color:#cbd5e1;font-size:.8rem;margin-top:4px">Cambia los filtros o importa datos desde Excel</p>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($expedientes->hasPages())
    <div style="padding:14px 18px;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <p style="font-size:.8rem;color:#64748b">
            Mostrando {{ $expedientes->firstItem() }}–{{ $expedientes->lastItem() }} de {{ $expedientes->total() }}
        </p>
        <div style="display:flex;gap:4px">
            @if($expedientes->onFirstPage())
                <span class="btn btn-sm" style="background:#f8fafc;color:#cbd5e1;cursor:default">‹ Anterior</span>
            @else
                <a href="{{ $expedientes->previousPageUrl() }}" class="btn btn-ghost btn-sm">‹ Anterior</a>
            @endif
            @if($expedientes->hasMorePages())
                <a href="{{ $expedientes->nextPageUrl() }}" class="btn btn-ghost btn-sm">Siguiente ›</a>
            @else
                <span class="btn btn-sm" style="background:#f8fafc;color:#cbd5e1;cursor:default">Siguiente ›</span>
            @endif
        </div>
    </div>
    @endif
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL: CREAR EXPEDIENTE
══════════════════════════════════════════════════════════ --}}
<div id="modal-create" class="modal-backdrop">
    <div class="modal-box" style="max-width:680px">
        <div class="modal-header">
            <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#0f766e,#0d6460);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg style="width:17px;height:17px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <span class="modal-title">Nuevo Expediente</span>
            <button onclick="closeModal('modal-create')" class="btn btn-icon btn-sm" style="margin-left:auto;background:#f8fafc;color:#64748b;border:1px solid #e2e8f0">✕</button>
        </div>
        <form action="{{ route('expedientes.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                @include('expedientes._form_modal', ['expediente' => null])
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal-create')" class="btn btn-sm" style="background:#f1f5f9;color:#64748b">Cancelar</button>
                <button type="submit" class="btn btn-primary btn-sm">
                    <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Guardar Expediente
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL: EDITAR EXPEDIENTE
══════════════════════════════════════════════════════════ --}}
<div id="modal-edit" class="modal-backdrop">
    <div class="modal-box" style="max-width:680px">
        <div class="modal-header">
            <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#1d4ed8,#1e40af);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg style="width:17px;height:17px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <span class="modal-title">Editar Expediente</span>
            <button onclick="closeModal('modal-edit')" class="btn btn-icon btn-sm" style="margin-left:auto;background:#f8fafc;color:#64748b;border:1px solid #e2e8f0">✕</button>
        </div>
        <form id="edit-form" action="" method="POST">
            @csrf @method('PUT')
            <div class="modal-body" id="edit-body">
                <div style="text-align:center;padding:40px">
                    <div style="width:36px;height:36px;border:3px solid #0f766e;border-top-color:transparent;border-radius:50%;animation:spin .7s linear infinite;margin:0 auto"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal-edit')" class="btn btn-sm" style="background:#f1f5f9;color:#64748b">Cancelar</button>
                <button type="submit" class="btn btn-accent btn-sm">
                    <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL: DETALLE
══════════════════════════════════════════════════════════ --}}
<div id="modal-detail" class="modal-backdrop">
    <div class="modal-box" style="max-width:700px">
        <div class="modal-header">
            <div style="width:36px;height:36px;border-radius:10px;background:#f0fdfa;border:1px solid #99f6e4;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg style="width:17px;height:17px;color:#0f766e" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <span class="modal-title" id="detail-title">Detalle del Expediente</span>
            <button onclick="closeModal('modal-detail')" class="btn btn-icon btn-sm" style="margin-left:auto;background:#f8fafc;color:#64748b;border:1px solid #e2e8f0">✕</button>
        </div>
        <div class="modal-body" id="detail-body">
            <div style="text-align:center;padding:40px">
                <div style="width:36px;height:36px;border:3px solid #0f766e;border-top-color:transparent;border-radius:50%;animation:spin .7s linear infinite;margin:0 auto"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeModal('modal-detail')" class="btn btn-sm" style="background:#f1f5f9;color:#64748b">Cerrar</button>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL: ELIMINAR
══════════════════════════════════════════════════════════ --}}
<div id="modal-delete" class="modal-backdrop">
    <div class="modal-box" style="max-width:420px">
        <div class="modal-header">
            <div style="width:36px;height:36px;border-radius:10px;background:#fef2f2;border:1px solid #fecaca;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg style="width:17px;height:17px;color:#dc2626" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <span class="modal-title" style="color:#dc2626">Confirmar Eliminación</span>
            <button onclick="closeModal('modal-delete')" class="btn btn-icon btn-sm" style="margin-left:auto;background:#f8fafc;color:#64748b;border:1px solid #e2e8f0">✕</button>
        </div>
        <div class="modal-body" style="text-align:center;padding:28px 24px">
            <p style="color:#374151;font-size:.93rem;margin-bottom:6px">¿Estás seguro de que deseas eliminar el expediente</p>
            <p id="delete-expediente-num" style="font-weight:800;font-size:1.1rem;color:#dc2626"></p>
            <p style="color:#94a3b8;font-size:.82rem;margin-top:8px">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeModal('modal-delete')" class="btn btn-sm" style="background:#f1f5f9;color:#64748b">Cancelar</button>
            <form id="delete-form" action="" method="POST" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Sí, eliminar
                </button>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL: IMPORTAR EXCEL (Step 1)
══════════════════════════════════════════════════════════ --}}
<div id="modal-import" class="modal-backdrop">
    <div class="modal-box" style="max-width:520px">
        <div class="modal-header">
            <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#1d4ed8,#1e40af);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg style="width:17px;height:17px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            </div>
            <span class="modal-title">Importar desde Excel</span>
            <button onclick="closeModal('modal-import')" class="btn btn-icon btn-sm" style="margin-left:auto;background:#f8fafc;color:#64748b;border:1px solid #e2e8f0">✕</button>
        </div>
        <form id="import-upload-form" action="{{ route('import.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body" style="display:flex;flex-direction:column;gap:18px">

                <div class="upload-zone" id="upload-zone" onclick="document.getElementById('excel_file').click()"
                     ondragover="event.preventDefault();this.classList.add('drag')"
                     ondragleave="this.classList.remove('drag')"
                     ondrop="handleDrop(event)">
                    <svg style="width:40px;height:40px;color:#cbd5e1;margin:0 auto 10px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <p id="upload-label" style="font-size:.88rem;color:#64748b;font-weight:500">Arrastra tu archivo o haz clic aquí</p>
                    <p style="font-size:.75rem;color:#94a3b8;margin-top:4px">XLSX · XLS · máx. 20 MB</p>
                </div>
                <input type="file" id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv"
                       style="display:none" onchange="updateUploadLabel(this)" required>

                <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px;font-size:.78rem;color:#1d4ed8;line-height:1.6">
                    <p style="font-weight:700;margin-bottom:4px">Columnas esperadas en cada hoja:</p>
                    N° · EXPEDIENTE · MATERIA · JUZGADO · DEMANDANTE · DEMANDADO · ESTADO · FECHA DE RESOLUCIÓN · CONTENIDO · ANTECEDENTES
                    <p style="margin-top:6px;color:#3b82f6">Juzgado, Materia y Estado se mapean automáticamente aunque vengan escritos diferente.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal-import')" class="btn btn-sm" style="background:#f1f5f9;color:#64748b">Cancelar</button>
                <button type="button" onclick="submitUpload()" class="btn btn-accent btn-sm">
                    <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Subir y Ver Hojas
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL: SELECCIONAR HOJAS (Step 2)
══════════════════════════════════════════════════════════ --}}
<div id="modal-sheets" class="modal-backdrop">
    <div class="modal-box" style="max-width:460px">
        <div class="modal-header">
            <div style="width:36px;height:36px;border-radius:10px;background:#f0fdf4;border:1px solid #bbf7d0;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg style="width:17px;height:17px;color:#16a34a" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            </div>
            <span class="modal-title">Seleccionar Hojas</span>
            <button onclick="closeModal('modal-sheets')" class="btn btn-icon btn-sm" style="margin-left:auto;background:#f8fafc;color:#64748b;border:1px solid #e2e8f0">✕</button>
        </div>
        <form id="sheets-form" action="{{ route('import.process') }}" method="POST">
            @csrf
            <div class="modal-body" id="sheets-body" style="display:flex;flex-direction:column;gap:10px">
                {{-- Populated by JS --}}
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal-sheets')" class="btn btn-sm" style="background:#f1f5f9;color:#64748b">Cancelar</button>
                <button type="submit" class="btn btn-primary btn-sm">
                    <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Importar Seleccionadas
                </button>
            </div>
        </form>
    </div>
</div>


@endsection

@push('scripts')
<style>
@keyframes spin { to { transform:rotate(360deg); } }
@keyframes rowHighlightFade {
    0%   { background-color: #fef08a; box-shadow: 0 0 0 2px #facc15 inset; }
    70%  { background-color: #fef9c3; box-shadow: 0 0 0 2px #fde047 inset; }
    100% { background-color: transparent; box-shadow: none; }
}
.row-updated {
    animation: rowHighlightFade 3.5s ease-out forwards;
}
</style>
<script>
// ── Highlight last updated row ────────────────────────────────
(function () {
    const updatedId = {{ session('updated_id', 'null') }};
    if (updatedId) {
        const row = document.getElementById('row-' + updatedId);
        if (row) {
            row.classList.add('row-updated');
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
})();
// ── Datos del servidor para modales ──────────────────────────
const ROUTES = {
    show:    '{{ route("expedientes.show", ":id") }}',
    edit:    '{{ route("expedientes.edit", ":id") }}',
    update:  '{{ route("expedientes.update", ":id") }}',
    destroy: '{{ route("expedientes.destroy", ":id") }}',
    upload:  '{{ route("import.upload") }}',
};
const juzgados = @json($juzgados);
const materias = @json($materias);
const estados  = @json($estados);
const expedientesData = @json($expedientes->items());

// ── Detail modal ─────────────────────────────────────────────
function openDetail(id) {
    openModal('modal-detail');
    const exp = expedientesData.find(e => e.id_registro == id);
    if (!exp) { document.getElementById('detail-body').innerHTML = '<p style="color:#94a3b8;padding:20px">No encontrado</p>'; return; }

    const juzgado = (juzgados.find(j => j.id_juzgado == exp.id_juzgado) || {}).nombre_juzgado || '—';
    const materia = (materias.find(m => m.id_materia == exp.id_materia) || {}).nombre_materia || '—';
    const estado  = (estados.find(s => s.id_estado  == exp.id_estado)  || {}).nombre_estado  || '—';
    const fecha   = exp.fecha_resolucion ? new Date(exp.fecha_resolucion).toLocaleDateString('es-PE') : '—';

    document.getElementById('detail-title').textContent = 'Expediente: ' + exp.numero_expediente;
    document.getElementById('detail-body').innerHTML = `
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px 24px">
            ${dl('N° Expediente', `<span style="font-weight:800;color:#0f766e;font-size:1rem">${exp.numero_expediente}</span>`)}
            ${dl('Estado', estado)}
            ${dl('Materia', materia)}
            ${dl('Juzgado', juzgado)}
            ${dl('Demandante', exp.demandante || '—')}
            ${dl('Demandado', exp.demandado || '—')}
            ${dl('Fecha de Resolución', fecha)}
            <div></div>
            <div style="grid-column:span 2">${dl('Contenido de la Resolución', `<div style="background:#f8fafc;border-radius:8px;padding:10px 12px;font-size:.82rem;line-height:1.6;white-space:pre-line;max-height:140px;overflow-y:auto">${exp.contenido_resolucion || '—'}</div>`)}</div>
            <div style="grid-column:span 2">${dl('Antecedentes', `<div style="background:#f8fafc;border-radius:8px;padding:10px 12px;font-size:.82rem;line-height:1.6;white-space:pre-line;max-height:120px;overflow-y:auto">${exp.antecedentes || '—'}</div>`)}</div>
        </div>`;
}
function dl(label, val) {
    return `<div><p style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:3px">${label}</p><div style="font-size:.84rem;color:#1e293b">${val}</div></div>`;
}

// ── Edit modal ────────────────────────────────────────────────
function openEdit(id) {
    openModal('modal-edit');
    const exp = expedientesData.find(e => e.id_registro == id);
    if (!exp) return;

    document.getElementById('edit-form').action = ROUTES.update.replace(':id', id);
    document.getElementById('edit-body').innerHTML = buildFormHTML(exp);
}

function buildFormHTML(exp) {
    const fDate = exp?.fecha_resolucion ? exp.fecha_resolucion.substring(0,10) : '';
    const grid2 = (label, field) => `
        <div>
            <label class="form-label">${label}</label>
            ${field}
        </div>`;

    const selectJuzgado = `<select name="id_juzgado" class="form-select">
        <option value="">— Sin juzgado —</option>
        ${juzgados.map(j=>`<option value="${j.id_juzgado}" ${(exp?.id_juzgado==j.id_juzgado)?'selected':''}>${j.nombre_juzgado}</option>`).join('')}
    </select>`;
    const selectMateria = `<select name="id_materia" class="form-select">
        <option value="">— Sin materia —</option>
        ${materias.map(m=>`<option value="${m.id_materia}" ${(exp?.id_materia==m.id_materia)?'selected':''}>${m.nombre_materia}</option>`).join('')}
    </select>`;
    const selectEstado = `<select name="id_estado" class="form-select">
        <option value="">— Sin estado —</option>
        ${estados.map(s=>`<option value="${s.id_estado}" ${(exp?.id_estado==s.id_estado)?'selected':''}>${s.nombre_estado}</option>`).join('')}
    </select>`;

    return `<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        ${grid2('N° Expediente <span style="color:#dc2626">*</span>', `<input type="text" name="numero_expediente" class="form-input" value="${exp?.numero_expediente||''}" required>`)}
        ${grid2('Juzgado', selectJuzgado)}
        ${grid2('Materia Procesal', selectMateria)}
        ${grid2('Estado', selectEstado)}
        ${grid2('Fecha de Resolución', `<input type="date" name="fecha_resolucion" class="form-input" value="${fDate}">`)}
        ${grid2('Demandante', `<input type="text" name="demandante" class="form-input" value="${exp?.demandante||''}">`)}
        <div style="grid-column:span 2">${grid2('Demandado', `<input type="text" name="demandado" class="form-input" value="${exp?.demandado||''}">`).replace('<div>','').replace('</div>','')}</div>
        <div style="grid-column:span 2">${grid2('Contenido de la Resolución', `<textarea name="contenido_resolucion" class="form-textarea" style="min-height:90px">${exp?.contenido_resolucion||''}</textarea>`).replace('<div>','').replace('</div>','')}</div>
        <div style="grid-column:span 2">${grid2('Antecedentes', `<textarea name="antecedentes" class="form-textarea" style="min-height:70px">${exp?.antecedentes||''}</textarea>`).replace('<div>','').replace('</div>','')}</div>
    </div>`;
}

// ── Delete modal ──────────────────────────────────────────────
function openDelete(id, numero) {
    document.getElementById('delete-form').action = ROUTES.destroy.replace(':id', id);
    document.getElementById('delete-expediente-num').textContent = '"' + numero + '"';
    openModal('modal-delete');
}

// ── Import: upload step ───────────────────────────────────────
function updateUploadLabel(input) {
    if (input.files && input.files[0]) {
        document.getElementById('upload-label').textContent = input.files[0].name;
        document.getElementById('upload-label').style.color = '#0f766e';
    }
}
function handleDrop(e) {
    e.preventDefault();
    document.getElementById('upload-zone').classList.remove('drag');
    const dt = e.dataTransfer;
    if (dt.files.length) {
        document.getElementById('excel_file').files = dt.files;
        updateUploadLabel(document.getElementById('excel_file'));
    }
}
function submitUpload() {
    const file = document.getElementById('excel_file').files[0];
    if (!file) { alert('Selecciona un archivo primero.'); return; }

    const btn = event.target;
    btn.disabled = true;
    btn.textContent = 'Subiendo…';

    const fd = new FormData(document.getElementById('import-upload-form'));
    fetch(ROUTES.upload, { method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'} })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg> Subir y Ver Hojas';
            if (data.error) { alert(data.error); return; }
            closeModal('modal-import');
            populateSheetsModal(data.sheets);
            openModal('modal-sheets');
        })
        .catch(() => {
            btn.disabled = false;
            btn.textContent = 'Error, intenta de nuevo';
            alert('Error al subir el archivo.');
        });
}

function populateSheetsModal(sheets) {
    const body = document.getElementById('sheets-body');
    body.innerHTML = `<p style="font-size:.84rem;color:#475569;margin-bottom:6px">Selecciona las hojas a importar:</p>` +
        sheets.map((name, i) => `
        <label style="display:flex;align-items:center;gap:12px;padding:12px 14px;border:1.5px solid #e2e8f0;border-radius:10px;cursor:pointer;transition:all .15s"
               onmouseenter="this.style.borderColor='#0f766e';this.style.background='#f0fdfa'"
               onmouseleave="this.style.borderColor=this.querySelector('input').checked?'#0f766e':'#e2e8f0';this.style.background=this.querySelector('input').checked?'#f0fdfa':'#fff'">
            <input type="checkbox" name="sheets[]" value="${name}" checked
                   style="width:16px;height:16px;accent-color:#0f766e"
                   onchange="this.closest('label').style.borderColor=this.checked?'#0f766e':'#e2e8f0';this.closest('label').style.background=this.checked?'#f0fdfa':'#fff'">
            <div>
                <p style="font-size:.85rem;font-weight:600;color:#1e293b">${name}</p>
                <p style="font-size:.72rem;color:#94a3b8">Hoja ${i+1}</p>
            </div>
        </label>`).join('');
}
</script>
@endpush


@section('content')
<div class="space-y-4">
    {{-- Toolbar --}}
    <div class="flex flex-wrap gap-3 items-center justify-between">
        <form method="GET" action="{{ route('expedientes.index') }}" class="flex flex-wrap gap-2 flex-1">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Buscar por expediente, demandante o demandado…"
                   class="flex-1 min-w-48 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">

            <select name="id_juzgado" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">Todos los Juzgados</option>
                @foreach($juzgados as $j)
                    <option value="{{ $j->id_juzgado }}" {{ request('id_juzgado') == $j->id_juzgado ? 'selected' : '' }}>
                        {{ $j->nombre_juzgado }}
                    </option>
                @endforeach
            </select>

            <select name="id_materia" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">Todas las Materias</option>
                @foreach($materias as $m)
                    <option value="{{ $m->id_materia }}" {{ request('id_materia') == $m->id_materia ? 'selected' : '' }}>
                        {{ $m->nombre_materia }}
                    </option>
                @endforeach
            </select>

            <select name="id_estado" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">Todos los Estados</option>
                @foreach($estados as $e)
                    <option value="{{ $e->id_estado }}" {{ request('id_estado') == $e->id_estado ? 'selected' : '' }}>
                        {{ $e->nombre_estado }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Filtrar
            </button>
            @if(request()->hasAny(['search','id_juzgado','id_materia','id_estado','anio']))
                <a href="{{ route('expedientes.index') }}" class="btn-secondary">Limpiar</a>
            @endif
        </form>
        <a href="{{ route('expedientes.create') }}" class="btn-primary flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo Expediente
        </a>
    </div>

    {{-- Table --}}
    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Expediente</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Materia</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Juzgado</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Demandante</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Estado</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Fecha Res.</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($expedientes as $exp)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3">
                            <a href="{{ route('expedientes.show', $exp) }}" class="font-semibold text-primary-700 hover:text-primary-900">
                                {{ $exp->numero_expediente }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-slate-600 max-w-[180px] truncate">{{ $exp->materia?->nombre_materia ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600 max-w-[180px] truncate">{{ $exp->juzgado?->nombre_juzgado ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600 max-w-[160px] truncate">{{ $exp->demandante ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($exp->estado)
                                @php
                                    $stateColors = [
                                        'EXPEDIENTES EN TRÁMITE'        => 'bg-blue-100 text-blue-700',
                                        'EN APELACIÓN'                  => 'bg-amber-100 text-amber-700',
                                        'IMPROCEDENTE'                  => 'bg-red-100 text-red-700',
                                        'EXPEDIENTES EN ARCHIVO'        => 'bg-slate-100 text-slate-600',
                                        'EXPEDIENTES EN EJECUCIÓN'      => 'bg-teal-100 text-teal-700',
                                        'CONCLUIDO/RESUELTO/SENTENCIADO'=> 'bg-green-100 text-green-700',
                                        'CON RESOLUCIÓN CONSENTIDA'     => 'bg-purple-100 text-purple-700',
                                    ];
                                    $color = $stateColors[$exp->estado->nombre_estado] ?? 'bg-slate-100 text-slate-600';
                                @endphp
                                <span class="badge {{ $color }}">{{ $exp->estado->nombre_estado }}</span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600 whitespace-nowrap">
                            {{ $exp->fecha_resolucion ? $exp->fecha_resolucion->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('expedientes.show', $exp) }}" title="Ver"
                                   class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-primary-700 hover:bg-primary-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('expedientes.edit', $exp) }}" title="Editar"
                                   class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('expedientes.destroy', $exp) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar expediente {{ $exp->numero_expediente }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Eliminar"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            No se encontraron expedientes.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expedientes->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $expedientes->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

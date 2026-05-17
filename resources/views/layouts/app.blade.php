<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Expedientes')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT:'#0f766e','50':'#f0fdfa','100':'#ccfbf1','200':'#99f6e4','300':'#5eead4','400':'#2dd4bf','500':'#14b8a6','600':'#0d9488','700':'#0f766e','800':'#115e59','900':'#134e4a' },
                        accent:  { DEFAULT:'#1d4ed8','50':'#eff6ff','100':'#dbeafe','200':'#bfdbfe','300':'#93c5fd','400':'#60a5fa','500':'#3b82f6','600':'#2563eb','700':'#1d4ed8','800':'#1e40af','900':'#1e3a8a' },
                    }
                }
            }
        }
    </script>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', 'Segoe UI', system-ui, sans-serif; background: #f1f5f9; }

        /* ── Sidebar ── */
        #sidebar {
            background: linear-gradient(180deg, #0a1f3c 0%, #0d2d4a 100%);
            transition: transform .28s cubic-bezier(.4,0,.2,1), width .28s cubic-bezier(.4,0,.2,1);
            transform: translateX(-100%);
        }
        #sidebar.open { transform: translateX(0); }

        .nav-link {
            display:flex; align-items:center; gap:12px;
            padding:10px 14px; border-radius:10px;
            font-size:.85rem; font-weight:500; color:#94a3b8;
            text-decoration:none; transition:all .18s;
        }
        .nav-link:hover { background:rgba(255,255,255,.08); color:#fff; }
        .nav-link.active {
            background: linear-gradient(135deg,#0f766e,#0d6460);
            color:#fff;
            box-shadow: 0 4px 12px rgba(15,118,110,.35);
        }
        .nav-link .icon-wrap {
            width:32px; height:32px; border-radius:8px;
            display:flex; align-items:center; justify-content:center;
            background:rgba(255,255,255,.07); flex-shrink:0;
        }
        .nav-link.active .icon-wrap { background:rgba(255,255,255,.15); }

        /* ── Cards ── */
        .card { background:#fff; border-radius:16px; border:1px solid #e2e8f0; box-shadow:0 1px 4px rgba(0,0,0,.06); }

        /* ── Buttons ── */
        .btn { display:inline-flex; align-items:center; gap:7px; font-size:.83rem; font-weight:600; padding:8px 16px; border-radius:9px; border:none; cursor:pointer; transition:all .18s; text-decoration:none; }
        .btn-primary   { background:linear-gradient(135deg,#0f766e,#0d6460); color:#fff; box-shadow:0 2px 8px rgba(15,118,110,.3); }
        .btn-primary:hover { background:linear-gradient(135deg,#115e59,#0a5450); box-shadow:0 4px 14px rgba(15,118,110,.4); transform:translateY(-1px); }
        .btn-accent    { background:linear-gradient(135deg,#1d4ed8,#1e40af); color:#fff; box-shadow:0 2px 8px rgba(29,78,216,.3); }
        .btn-accent:hover { background:linear-gradient(135deg,#1e40af,#1e3a8a); box-shadow:0 4px 14px rgba(29,78,216,.4); transform:translateY(-1px); }
        .btn-ghost     { background:rgba(15,118,110,.08); color:#0f766e; border:1px solid rgba(15,118,110,.2); }
        .btn-ghost:hover { background:rgba(15,118,110,.15); }
        .btn-danger    { background:#fee2e2; color:#dc2626; border:1px solid #fecaca; }
        .btn-danger:hover { background:#fecaca; transform:translateY(-1px); }
        .btn-sm { padding:6px 12px; font-size:.78rem; border-radius:7px; }
        .btn-icon { width:32px; height:32px; padding:0; justify-content:center; border-radius:8px; }

        /* ── Badge ── */
        .badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:20px; font-size:.72rem; font-weight:600; letter-spacing:.02em; white-space:nowrap; }

        /* ── Table ── */
        .data-table { width:100%; border-collapse:separate; border-spacing:0; }
        .data-table thead th { background:#f8fafc; font-size:.73rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#64748b; padding:12px 14px; border-bottom:2px solid #e2e8f0; white-space:nowrap; }
        .data-table thead th:first-child { border-radius:10px 0 0 0; }
        .data-table thead th:last-child  { border-radius:0 10px 0 0; }
        .data-table tbody tr { transition:background .12s; }
        .data-table tbody tr:hover { background:#f8fafc; }
        .data-table tbody td { padding:12px 14px; font-size:.84rem; color:#374151; border-bottom:1px solid #f1f5f9; vertical-align:middle; }

        /* ── Modal ── */
        .modal-backdrop {
            position:fixed; inset:0; background:rgba(2,12,30,.55);
            backdrop-filter:blur(4px); z-index:50;
            display:none; align-items:center; justify-content:center;
            padding:20px;
        }
        .modal-backdrop.open { display:flex; }
        .modal-box {
            background:#fff; border-radius:20px; width:100%; max-height:90vh;
            overflow-y:auto; box-shadow:0 24px 64px rgba(0,0,0,.22);
            animation: modalIn .22s ease;
        }
        @keyframes modalIn { from{opacity:0;transform:scale(.96) translateY(12px)} to{opacity:1;transform:none} }
        .modal-header {
            display:flex; align-items:center; gap:12px;
            padding:20px 24px 16px; border-bottom:1px solid #f1f5f9; position:sticky; top:0; background:#fff; z-index:1; border-radius:20px 20px 0 0;
        }
        .modal-title { font-size:1rem; font-weight:700; color:#0f172a; }
        .modal-body  { padding:24px; }
        .modal-footer { padding:16px 24px; border-top:1px solid #f1f5f9; display:flex; gap:10px; justify-content:flex-end; background:#fafafa; border-radius:0 0 20px 20px; }

        /* ── Form ── */
        .form-label { display:block; font-size:.8rem; font-weight:600; color:#374151; margin-bottom:5px; }
        .form-input, .form-select, .form-textarea {
            width:100%; padding:9px 12px; border:1.5px solid #e2e8f0; border-radius:9px;
            font-size:.84rem; color:#1e293b; background:#fff; outline:none;
            transition:border-color .18s, box-shadow .18s;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color:#0f766e; box-shadow:0 0 0 3px rgba(15,118,110,.12);
        }
        .form-textarea { resize:vertical; min-height:80px; }
        .form-error { font-size:.75rem; color:#dc2626; margin-top:4px; }

        /* ── Stat card ── */
        .stat-card { display:flex; align-items:center; gap:14px; padding:18px 22px; }
        .stat-icon { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }

        /* ── Search ── */
        .search-wrap { position:relative; }
        .search-wrap svg { position:absolute; left:11px; top:50%; transform:translateY(-50%); color:#94a3b8; pointer-events:none; }
        .search-wrap input { padding-left:36px; }

        /* ── Overlay ── */
        #sidebar-overlay { position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:29; display:none; }
        #sidebar-overlay.show { display:block; }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width:5px; height:5px; }
        ::-webkit-scrollbar-track { background:#f1f5f9; }
        ::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:10px; }

        /* ── Upload zone ── */
        .upload-zone { border:2px dashed #cbd5e1; border-radius:12px; padding:36px 20px; text-align:center; cursor:pointer; transition:all .18s; }
        .upload-zone:hover, .upload-zone.drag { border-color:#0f766e; background:#f0fdfa; }
    </style>
    @stack('styles')
</head>
<body>

<div id="sidebar-overlay" onclick="closeSidebar()"></div>

<!-- ═══ SIDEBAR ═══ -->
<aside id="sidebar" class="fixed top-0 left-0 h-full w-64 z-30 flex flex-col">
    <!-- Logo -->
    <div class="flex items-center gap-3 px-5 py-5" style="border-bottom:1px solid rgba(255,255,255,.08)">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#0f766e,#0d6460)">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div>
            <p class="text-white font-bold text-sm leading-tight">Sistema de</p>
            <p class="font-bold text-sm leading-tight" style="color:#2dd4bf">Expedientes</p>
        </div>
        <button onclick="closeSidebar()" class="ml-auto lg:hidden" style="color:#64748b">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <!-- Nav -->
    <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">
        <p style="font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#475569;padding:0 10px 8px">Menú</p>

        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="icon-wrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10-2a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z"/></svg>
            </span>
            Dashboard
        </a>

        <a href="{{ route('expedientes.index') }}" class="nav-link {{ request()->routeIs('expedientes.*') ? 'active' : '' }}">
            <span class="icon-wrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </span>
            Expedientes
        </a>
    </nav>

    <!-- Footer -->
    <div class="px-5 py-4" style="border-top:1px solid rgba(255,255,255,.08)">
        <p style="font-size:.72rem;color:#475569">Gestión Judicial · 2026</p>
    </div>
</aside>

<!-- ═══ MAIN ═══ -->
<div id="main-wrap" style="margin-left:0; transition:margin-left .28s cubic-bezier(.4,0,.2,1)">
    <!-- Topbar -->
    <header class="sticky top-0 z-20 flex items-center gap-4 px-5 py-3" style="background:#fff;border-bottom:1px solid #e2e8f0;box-shadow:0 1px 6px rgba(0,0,0,.06)">
        <button id="toggle-btn" onclick="toggleSidebar()"
                class="w-9 h-9 flex items-center justify-center rounded-lg transition-colors"
                style="color:#64748b;border:1px solid #e2e8f0;">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div class="flex-1 min-w-0">
            <h1 class="text-base font-bold truncate" style="color:#0f172a">@yield('page-title','Panel Principal')</h1>
        </div>

        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background:linear-gradient(135deg,#0f766e,#1d4ed8)">SJ</div>
        </div>
    </header>

    <!-- Content -->
    <main class="p-5 lg:p-6">
        @if(session('success'))
        <div class="flex items-center gap-3 mb-5 px-4 py-3 rounded-xl text-sm font-medium" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
            @if(session('import_not_imported') && count(session('import_not_imported')) > 0)
                &nbsp;&nbsp;
                <button onclick="openModal('modal-import-not-imported')" style="text-decoration:underline;font-weight:700;background:none;border:none;cursor:pointer;color:#b91c1c">
                    Ver {{ count(session('import_not_imported')) }} no importado(s) →
                </button>
            @endif
            @if(session('import_unmatched') && count(session('import_unmatched')) > 0)
                &nbsp;&nbsp;
                <button onclick="openModal('modal-import-warnings')" style="text-decoration:underline;font-weight:700;background:none;border:none;cursor:pointer;color:#166534">
                    Ver {{ count(session('import_unmatched')) }} advertencia(s) →
                </button>
            @endif
        </div>
        @endif

        @if(session('error'))
        <div class="flex items-center gap-3 mb-5 px-4 py-3 rounded-xl text-sm font-medium" style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 5a1 1 0 112 0v4a1 1 0 11-2 0V5zm1 8a1.25 1.25 0 100 2.5A1.25 1.25 0 0010 13z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
        @endif

        @if(session('import_not_imported') && count(session('import_not_imported')) > 0)
        <div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium" style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b">
            No se importaron {{ count(session('import_not_imported')) }} fila(s).
            <button onclick="openModal('modal-import-not-imported')" style="text-decoration:underline;font-weight:700;background:none;border:none;cursor:pointer;color:#991b1b">
                Ver detalle
            </button>
        </div>

        <div id="modal-import-not-imported" class="modal-backdrop">
            <div class="modal-box" style="max-width:980px">
                <div class="modal-header">
                    <div style="width:36px;height:36px;border-radius:10px;background:#fef2f2;border:1px solid #fecaca;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg style="width:17px;height:17px;color:#b91c1c" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M4.93 19h14.14c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.2 16c-.77 1.33.19 3 1.73 3z"/></svg>
                    </div>
                    <div>
                        <span class="modal-title" style="color:#991b1b">{{ count(session('import_not_imported')) }} fila(s) no importadas</span>
                        <p style="font-size:.75rem;color:#b91c1c;margin-top:1px">Se muestra hoja, fila, expediente y motivo exacto</p>
                    </div>
                    <button onclick="closeModal('modal-import-not-imported')" class="btn btn-icon btn-sm" style="margin-left:auto;background:#f8fafc;color:#64748b;border:1px solid #e2e8f0">✕</button>
                </div>
                <div class="modal-body" style="padding:0">
                    <div style="overflow-x:auto;max-height:520px;overflow-y:auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Hoja</th>
                                    <th style="width:72px">Fila</th>
                                    <th style="width:160px">N° Expediente</th>
                                    <th>Motivo</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach(session('import_not_imported') as $ni)
                                <tr>
                                    <td style="font-size:.8rem;color:#334155">{{ $ni['hoja'] ?? '—' }}</td>
                                    <td style="font-size:.8rem;color:#64748b">{{ $ni['fila'] ?? '—' }}</td>
                                    <td style="font-size:.82rem;color:#0f172a;font-weight:700">{{ $ni['expediente'] ?? '—' }}</td>
                                    <td style="font-size:.8rem;color:#991b1b">{{ $ni['motivo'] ?? 'Sin detalle' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button onclick="closeModal('modal-import-not-imported')" class="btn btn-sm">Cerrar</button>
                </div>
            </div>
        </div>
        @endif

        @if(session('import_sheet_headings') && count(session('import_sheet_headings')) > 0)
        <div class="mb-4 px-4 py-3 rounded-xl text-xs" style="background:#f8fafc;border:1px solid #e2e8f0;color:#475569">
            <strong style="color:#0f172a">📋 Columnas detectadas por hoja:</strong>
            @foreach(session('import_sheet_headings') as $line)
            <div style="margin-top:4px;font-family:monospace">{{ $line }}</div>
            @endforeach
        </div>
        @endif

        @if(session('import_unmatched') && count(session('import_unmatched')) > 0)
        {{-- Modal: Import Warnings with inline fix + create --}}
        <div id="modal-import-warnings" class="modal-backdrop">
            <div class="modal-box" style="max-width:960px">
                <div class="modal-header">
                    <div style="width:36px;height:36px;border-radius:10px;background:#fffbeb;border:1px solid #fde68a;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg style="width:17px;height:17px;color:#d97706" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <span class="modal-title" style="color:#92400e">{{ count(session('import_unmatched')) }} fila(s) con campos no reconocidos</span>
                        <p style="font-size:.75rem;color:#b45309;margin-top:1px">Selecciona una opcion existente o crea un nuevo registro para cada fila</p>
                    </div>
                    <button onclick="closeModal('modal-import-warnings')" class="btn btn-icon btn-sm" style="margin-left:auto;background:#f8fafc;color:#64748b;border:1px solid #e2e8f0">✕</button>
                </div>
                <div class="modal-body" style="padding:0">
                    <div style="overflow-x:auto;max-height:540px;overflow-y:auto">
                        <table class="data-table" id="tbl-unmatched">
                            <thead>
                                <tr>
                                    <th style="width:46px">Fila</th>
                                    <th>N° Expediente</th>
                                    <th style="width:80px">Campo</th>
                                    <th>Valor en Excel</th>
                                    <th style="min-width:300px">Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php
                                $allEstados  = \App\Models\Estado::orderBy('nombre_estado')->get();
                                $allMaterias = \App\Models\Materia::orderBy('nombre_materia')->get();
                                $allJuzgados = \App\Models\Juzgado::orderBy('nombre_juzgado')->get();
                            @endphp
                            @foreach(session('import_unmatched') as $rowIdx => $row)
                                @foreach($row['advertencias'] as $advIdx => $adv)
                                @php
                                    $uid   = "row{$rowIdx}adv{$advIdx}";
                                    $campo = $adv['campo'];
                                    $color = $campo==='Estado' ? '#b45309' : ($campo==='Juzgado' ? '#166534' : '#1d4ed8');
                                    $bg    = $campo==='Estado' ? '#fffbeb' : ($campo==='Juzgado' ? '#f0fdf4' : '#eff6ff');
                                    $topSugg = $adv['sugerencias'][0] ?? null;
                                @endphp
                                <tr id="tr-{{ $uid }}">
                                    <td style="color:#94a3b8;font-size:.78rem">{{ $row['fila'] }}</td>
                                    <td style="font-weight:700;color:#0f766e;font-size:.82rem">{{ $row['expediente'] }}</td>
                                    <td><span style="background:{{ $bg }};color:{{ $color }};padding:3px 8px;border-radius:12px;font-size:.72rem;font-weight:600">{{ $campo }}</span></td>
                                    <td style="font-size:.82rem;color:#dc2626;font-weight:600">"{{ $adv['valor_excel'] }}"</td>
                                    <td style="padding:8px 12px">
                                        {{-- Mode toggle --}}
                                        <div style="display:flex;gap:6px;margin-bottom:6px">
                                            <button onclick="setFixMode('{{ $uid }}','select')"
                                                    id="btn-sel-{{ $uid }}"
                                                    style="font-size:.72rem;padding:3px 10px;border-radius:6px;border:1.5px solid #0f766e;background:#f0fdfa;color:#0f766e;cursor:pointer;font-weight:600">
                                                Seleccionar
                                            </button>
                                            <button onclick="setFixMode('{{ $uid }}','create')"
                                                    id="btn-new-{{ $uid }}"
                                                    style="font-size:.72rem;padding:3px 10px;border-radius:6px;border:1.5px solid #e2e8f0;background:#f8fafc;color:#64748b;cursor:pointer;font-weight:600">
                                                + Crear nuevo
                                            </button>
                                        </div>

                                        {{-- Existing select --}}
                                        <div id="panel-sel-{{ $uid }}">
                                            @if($campo === 'Estado')
                                            <select class="form-select fix-select" style="font-size:.78rem;padding:5px 8px"
                                                    data-exp="{{ $row['expediente'] }}" data-field="id_estado">
                                                <option value="">— Sin cambio —</option>
                                                @if($topSugg) <optgroup label="Sugerida (mejor coincidencia)">
                                                    <option value="{{ $topSugg['id'] }}">{{ $topSugg['name'] }} ({{ $topSugg['score'] }}%)</option>
                                                </optgroup><optgroup label="Todos los estados"> @endif
                                                @foreach($allEstados as $e)
                                                <option value="{{ $e->id_estado }}">{{ $e->nombre_estado }}</option>
                                                @endforeach
                                                @if($topSugg) </optgroup> @endif
                                            </select>
                                            @elseif($campo === 'Materia')
                                            <select class="form-select fix-select" style="font-size:.78rem;padding:5px 8px"
                                                    data-exp="{{ $row['expediente'] }}" data-field="id_materia">
                                                <option value="">— Sin cambio —</option>
                                                @if($topSugg) <optgroup label="Sugerida (mejor coincidencia)">
                                                    <option value="{{ $topSugg['id'] }}">{{ $topSugg['name'] }} ({{ $topSugg['score'] }}%)</option>
                                                </optgroup><optgroup label="Todas las materias"> @endif
                                                @foreach($allMaterias as $m)
                                                <option value="{{ $m->id_materia }}">{{ $m->nombre_materia }}</option>
                                                @endforeach
                                                @if($topSugg) </optgroup> @endif
                                            </select>
                                            @else
                                            <select class="form-select fix-select" style="font-size:.78rem;padding:5px 8px"
                                                    data-exp="{{ $row['expediente'] }}" data-field="id_juzgado">
                                                <option value="">— Sin cambio —</option>
                                                @if($topSugg) <optgroup label="Sugerido (mejor coincidencia)">
                                                    <option value="{{ $topSugg['id'] }}">{{ $topSugg['name'] }} ({{ $topSugg['score'] }}%)</option>
                                                </optgroup><optgroup label="Todos los juzgados"> @endif
                                                @foreach($allJuzgados as $j)
                                                <option value="{{ $j->id_juzgado }}">{{ $j->nombre_juzgado }}</option>
                                                @endforeach
                                                @if($topSugg) </optgroup> @endif
                                            </select>
                                            @endif
                                        </div>

                                        {{-- Create new panel --}}
                                        <div id="panel-new-{{ $uid }}" style="display:none">
                                            <div style="display:flex;gap:6px;align-items:center">
                                                <input type="text" id="inp-new-{{ $uid }}"
                                                       value="{{ $adv['valor_excel'] }}"
                                                       class="form-input" style="font-size:.78rem;padding:5px 8px;flex:1"
                                                       placeholder="Nombre del nuevo {{ strtolower($campo) }}">
                                                <button onclick="createNewCatalog('{{ $uid }}','{{ strtolower($campo) }}','{{ $row['expediente'] }}')"
                                                        class="btn btn-primary btn-sm" style="white-space:nowrap;padding:5px 12px;font-size:.76rem">
                                                    Crear y asignar
                                                </button>
                                            </div>
                                            <p id="msg-new-{{ $uid }}" style="display:none;font-size:.74rem;margin-top:4px;color:#166534"></p>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div id="fix-msg" style="display:none;padding:10px 20px;font-size:.82rem;font-weight:600"></div>
                </div>
                <div class="modal-footer">
                    <button onclick="closeModal('modal-import-warnings')" class="btn btn-sm" style="background:#f1f5f9;color:#64748b">Cerrar</button>
                    <button onclick="saveUnmatchedFixes()" id="btn-save-fixes" class="btn btn-primary btn-sm">
                        <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Guardar selecciones
                    </button>
                    <a href="{{ route('expedientes.index') }}" class="btn btn-accent btn-sm">Ir a expedientes</a>
                </div>
            </div>
        </div>
        <script>
        const CSRF = document.querySelector('meta[name=csrf-token]').content;
        const ROUTE_FIX    = '{{ route("import.fix") }}';
        const ROUTE_CREATE = '{{ route("import.catalog.create") }}';

        function setFixMode(uid, mode) {
            document.getElementById('panel-sel-' + uid).style.display = mode === 'select' ? 'block' : 'none';
            document.getElementById('panel-new-' + uid).style.display = mode === 'create' ? 'block' : 'none';
            const bSel = document.getElementById('btn-sel-' + uid);
            const bNew = document.getElementById('btn-new-' + uid);
            if (mode === 'select') {
                bSel.style.background = '#f0fdfa'; bSel.style.borderColor = '#0f766e'; bSel.style.color = '#0f766e';
                bNew.style.background = '#f8fafc'; bNew.style.borderColor = '#e2e8f0'; bNew.style.color = '#64748b';
            } else {
                bNew.style.background = '#f0fdfa'; bNew.style.borderColor = '#0f766e'; bNew.style.color = '#0f766e';
                bSel.style.background = '#f8fafc'; bSel.style.borderColor = '#e2e8f0'; bSel.style.color = '#64748b';
            }
        }

        function createNewCatalog(uid, tipo, expediente) {
            const inp = document.getElementById('inp-new-' + uid);
            const nombre = inp.value.trim().toUpperCase();
            if (!nombre) { alert('Ingresa un nombre'); return; }
            const btn = inp.nextElementSibling;
            btn.disabled = true; btn.textContent = '...';
            fetch(ROUTE_CREATE, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ tipo, nombre, expediente })
            })
            .then(r => r.json())
            .then(data => {
                const msg = document.getElementById('msg-new-' + uid);
                msg.style.display = 'block';
                msg.textContent = '✓ Creado y asignado: ' + data.nombre;
                document.getElementById('tr-' + uid).style.opacity = '0.4';
                btn.textContent = 'Listo';
            })
            .catch(() => { btn.disabled = false; btn.textContent = 'Crear y asignar'; alert('Error al crear'); });
        }

        function saveUnmatchedFixes() {
            const selects = document.querySelectorAll('.fix-select');
            const fixMap = {};
            selects.forEach(s => {
                if (!s.value) return;
                const exp = s.dataset.exp;
                if (!fixMap[exp]) fixMap[exp] = { expediente: exp };
                fixMap[exp][s.dataset.field] = s.value;
            });
            const fixes = Object.values(fixMap);
            const msg = document.getElementById('fix-msg');
            if (!fixes.length) {
                msg.style.display = 'block'; msg.style.color = '#92400e'; msg.style.background = '#fffbeb';
                msg.textContent = 'No seleccionaste ninguna opcion en el modo "Seleccionar".';
                return;
            }
            const btn = document.getElementById('btn-save-fixes');
            btn.disabled = true; btn.textContent = 'Guardando...';
            fetch(ROUTE_FIX, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ fixes })
            })
            .then(r => r.json())
            .then(data => {
                msg.style.display = 'block'; msg.style.color = '#166534'; msg.style.background = '#f0fdf4';
                msg.textContent = '✓ ' + data.updated + ' expediente(s) actualizados.';
                btn.textContent = 'Guardado';
                document.querySelectorAll('.fix-select').forEach(s => {
                    if (s.value) s.closest('tr').style.opacity = '0.4';
                });
            })
            .catch(() => { btn.disabled = false; btn.textContent = 'Guardar selecciones'; alert('Error al guardar.'); });
        }
        </script>
        @endif

        @yield('content')
    </main>
</div>

<script>
let sidebarOpen = false;

function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('sidebar-overlay').classList.add('show');
    document.getElementById('main-wrap').style.marginLeft = window.innerWidth >= 1024 ? '256px' : '0';
    sidebarOpen = true;
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebar-overlay').classList.remove('show');
    document.getElementById('main-wrap').style.marginLeft = '0';
    sidebarOpen = false;
}
function toggleSidebar() {
    sidebarOpen ? closeSidebar() : openSidebar();
}

// Modal helpers
function openModal(id) { document.getElementById(id).classList.add('open'); document.body.style.overflow='hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow=''; }

// Close modal on backdrop click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-backdrop')) {
        e.target.classList.remove('open');
        document.body.style.overflow='';
    }
});
</script>
@stack('scripts')
@if(session('import_unmatched') && count(session('import_unmatched')) > 0)
<script>document.addEventListener('DOMContentLoaded',()=>openModal('modal-import-warnings'));</script>
@endif
</body>
</html>

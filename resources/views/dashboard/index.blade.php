@extends('layouts.app')
@section('title', 'Panel Principal')
@section('page-title', 'Panel Principal')

@section('content')

@php
$kpiColors = [
    'EN TRAMITE'          => ['top'=>'#3b82f6','grad'=>'135deg,#3b82f6,#60a5fa','shadow'=>'rgba(59,130,246,.3)'],
    'EN APELACION'        => ['top'=>'#f59e0b','grad'=>'135deg,#d97706,#f59e0b','shadow'=>'rgba(245,158,11,.3)'],
    'IMPROCEDENTE'        => ['top'=>'#ef4444','grad'=>'135deg,#dc2626,#ef4444','shadow'=>'rgba(239,68,68,.3)'],
    'EN ARCHIVO'          => ['top'=>'#94a3b8','grad'=>'135deg,#64748b,#94a3b8','shadow'=>'rgba(100,116,139,.25)'],
    'EN EJECUCION'        => ['top'=>'#0d9488','grad'=>'135deg,#0f766e,#0d9488','shadow'=>'rgba(13,148,136,.3)'],
    'CONCLUIDO/RESUELTO'  => ['top'=>'#16a34a','grad'=>'135deg,#16a34a,#22c55e','shadow'=>'rgba(22,163,74,.3)'],
    'CON RES. CONSENTIDA' => ['top'=>'#9333ea','grad'=>'135deg,#7e22ce,#9333ea','shadow'=>'rgba(147,51,234,.3)'],
];
@endphp

{{-- KPI Cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(185px,1fr));gap:14px;margin-bottom:16px" id="kpi-grid">
    <div class="card" style="padding:20px 22px;display:flex;align-items:center;gap:14px;border-top:3px solid #0f766e">
        <div style="width:46px;height:46px;border-radius:14px;background:linear-gradient(135deg,#0f766e,#0d9488);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(15,118,110,.3)">
            <svg style="width:20px;height:20px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div>
            <p style="font-size:1.6rem;font-weight:800;color:#0f172a;line-height:1">{{ number_format($total) }}</p>
            <p style="font-size:.75rem;color:#64748b;margin-top:2px;font-weight:500">Total</p>
        </div>
    </div>
    @foreach($porEstado as $est)
    @php
        $c = $kpiColors[$est->nombre_estado] ?? ['top'=>'#64748b','grad'=>'135deg,#64748b,#94a3b8','shadow'=>'rgba(100,116,139,.2)'];
        $hasCount = $est->expedientes_count > 0;
    @endphp
    <div class="card kpi-estado {{ $hasCount ? 'kpi-active' : 'kpi-empty' }}"
         style="padding:20px 22px;display:{{ $hasCount ? 'flex' : 'none' }};align-items:center;gap:14px;border-top:3px solid {{ $c['top'] }}">
        <div style="width:46px;height:46px;border-radius:14px;background:linear-gradient({{ $c['grad'] }});display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px {{ $c['shadow'] }}">
            <svg style="width:20px;height:20px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div style="min-width:0">
            <p style="font-size:1.6rem;font-weight:800;color:#0f172a;line-height:1">{{ number_format($est->expedientes_count) }}</p>
            <p style="font-size:.72rem;color:#64748b;margin-top:2px;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="{{ $est->nombre_estado }}">{{ $est->nombre_estado }}</p>
            @if($total > 0)
            <div style="height:3px;background:#e2e8f0;border-radius:2px;margin-top:6px">
                <div style="height:100%;background:{{ $c['top'] }};border-radius:2px;width:{{ round($est->expedientes_count/$total*100) }}%"></div>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
<div style="margin-bottom:24px;text-align:center">
    <button id="btn-ver-mas" onclick="toggleEmptyKpis()" class="btn btn-ghost btn-sm" style="font-size:.78rem">
        Ver todos los estados (incluye con 0)
    </button>
</div>

{{-- Charts row 1 --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px">
    <div class="card" style="padding:0;overflow:hidden">
        <div style="padding:18px 20px 14px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px">
            <div>
                <p style="font-size:.93rem;font-weight:700;color:#0f172a">Por Estado</p>
                <p style="font-size:.73rem;color:#94a3b8;margin-top:1px">Distribucion porcentual</p>
            </div>
            <div style="display:flex;gap:6px">
                <button onclick="toggleChartType('chartEstado','doughnut','pie')" class="btn btn-ghost btn-sm" style="font-size:.73rem;padding:5px 10px">Tipo</button>
                <button onclick="downloadChart('chartEstado','estado')" class="btn btn-sm" style="background:#f1f5f9;color:#64748b;font-size:.73rem;padding:5px 10px">PNG</button>
            </div>
        </div>
        <div style="padding:20px;height:300px;position:relative"><canvas id="chartEstado"></canvas></div>
    </div>
    <div class="card" style="padding:0;overflow:hidden">
        <div style="padding:18px 20px 14px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px">
            <div>
                <p style="font-size:.93rem;font-weight:700;color:#0f172a">Por Año</p>
                <p style="font-size:.73rem;color:#94a3b8;margin-top:1px">Fecha de resolucion judicial</p>
            </div>
            <div style="display:flex;gap:6px;align-items:center">
                <select id="anio-desde" onchange="filterAnioChart()" class="form-select" style="font-size:.73rem;padding:5px 8px;min-width:74px">
                    <option value="">Desde</option>
                    @foreach($porAnio as $a) @if($a->anio) <option value="{{ $a->anio }}">{{ $a->anio }}</option> @endif @endforeach
                </select>
                <select id="anio-hasta" onchange="filterAnioChart()" class="form-select" style="font-size:.73rem;padding:5px 8px;min-width:74px">
                    <option value="">Hasta</option>
                    @foreach($porAnio as $a) @if($a->anio) <option value="{{ $a->anio }}">{{ $a->anio }}</option> @endif @endforeach
                </select>
                <button onclick="toggleChartType('chartAnio','bar','line')" class="btn btn-ghost btn-sm" style="font-size:.73rem;padding:5px 8px">L/B</button>
                <button onclick="downloadChart('chartAnio','anio')" class="btn btn-sm" style="background:#f1f5f9;color:#64748b;font-size:.73rem;padding:5px 8px">PNG</button>
            </div>
        </div>
        <div style="padding:20px;height:300px;position:relative"><canvas id="chartAnio"></canvas></div>
    </div>
</div>

{{-- Charts row 2 --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px">
    <div class="card" style="padding:0;overflow:hidden">
        <div style="padding:18px 20px 14px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
            <div><p style="font-size:.93rem;font-weight:700;color:#0f172a">Por Juzgado</p><p style="font-size:.73rem;color:#94a3b8;margin-top:1px">Top 15</p></div>
            <button onclick="downloadChart('chartJuzgado','juzgado')" class="btn btn-sm" style="background:#f1f5f9;color:#64748b;font-size:.73rem;padding:5px 10px">PNG</button>
        </div>
        <div style="padding:20px;height:420px;position:relative"><canvas id="chartJuzgado"></canvas></div>
    </div>
    <div class="card" style="padding:0;overflow:hidden">
        <div style="padding:18px 20px 14px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
            <div><p style="font-size:.93rem;font-weight:700;color:#0f172a">Por Materia</p><p style="font-size:.73rem;color:#94a3b8;margin-top:1px">Todas las materias</p></div>
            <button onclick="downloadChart('chartMateria','materia')" class="btn btn-sm" style="background:#f1f5f9;color:#64748b;font-size:.73rem;padding:5px 10px">PNG</button>
        </div>
        <div style="padding:20px;height:420px;position:relative"><canvas id="chartMateria"></canvas></div>
    </div>
</div>

{{-- Pivot + simple tables: ALL as tabs --}}
@php $canonKeys = array_keys($canonicalGroups); @endphp

<div class="card" style="padding:0;overflow:hidden;margin-bottom:20px">
    <div style="padding:18px 20px 0;border-bottom:1px solid #f1f5f9">
        <p style="font-size:.93rem;font-weight:700;color:#0f172a;margin-bottom:14px">Tablas Detalladas</p>
        <div style="display:flex;gap:2px;flex-wrap:wrap">
            @foreach([
                ['id'=>'tab-juzgado-simple',  'label'=>'Juzgado (total)'],
                ['id'=>'tab-materia-simple',   'label'=>'Materia (total)'],
                ['id'=>'tab-anio',             'label'=>'Por Año'],
                ['id'=>'tab-juzgado-pivot',    'label'=>'Juzgado × Estado'],
                ['id'=>'tab-materia-pivot',    'label'=>'Materia × Estado'],
            ] as $tab)
            <button onclick="showDashTab('{{ $tab['id'] }}')" id="btn-{{ $tab['id'] }}"
                    class="dash-tab-btn"
                    style="padding:8px 16px;font-size:.81rem;font-weight:600;border:none;cursor:pointer;border-bottom:2px solid {{ $loop->first ? '#0f766e' : 'transparent' }};color:{{ $loop->first ? '#0f766e' : '#94a3b8' }};background:transparent;transition:all .15s;white-space:nowrap">
                {{ $tab['label'] }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- Tab: Juzgado simple --}}
    <div id="tab-juzgado-simple" style="overflow-x:auto">
        <table class="data-table">
            <thead><tr><th>#</th><th>Juzgado</th><th style="text-align:right">Total</th><th style="min-width:140px">Barra</th></tr></thead>
            <tbody>
            @foreach($porJuzgado as $i => $j)
            @php $pj = $total > 0 ? round($j->expedientes_count/$total*100,1) : 0; @endphp
            <tr>
                <td style="color:#94a3b8;font-size:.78rem;width:36px">{{ $i+1 }}</td>
                <td style="font-size:.84rem;color:#374151">{{ $j->nombre_juzgado }}</td>
                <td style="text-align:right;font-weight:700;color:#0f172a">{{ number_format($j->expedientes_count) }}</td>
                <td><div style="height:5px;background:#f1f5f9;border-radius:3px;overflow:hidden"><div style="height:100%;background:#1d4ed8;border-radius:3px;width:{{ $pj }}%"></div></div></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- Tab: Materia simple --}}
    <div id="tab-materia-simple" style="display:none;overflow-x:auto">
        <table class="data-table">
            <thead><tr><th>#</th><th>Materia</th><th style="text-align:right">Total</th><th style="min-width:140px">Barra</th></tr></thead>
            <tbody>
            @foreach($porMateria as $i => $m)
            @php $pm = $total > 0 ? round($m->expedientes_count/$total*100,1) : 0; @endphp
            <tr>
                <td style="color:#94a3b8;font-size:.78rem;width:36px">{{ $i+1 }}</td>
                <td style="font-size:.84rem;color:#374151">{{ $m->nombre_materia }}</td>
                <td style="text-align:right;font-weight:700;color:#0f172a">{{ number_format($m->expedientes_count) }}</td>
                <td><div style="height:5px;background:#f1f5f9;border-radius:3px;overflow:hidden"><div style="height:100%;background:#0f766e;border-radius:3px;width:{{ $pm }}%"></div></div></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- Tab: Año --}}
    <div id="tab-anio" style="display:none;overflow-x:auto">
        <table class="data-table">
            <thead><tr><th>Año</th><th style="text-align:right">Total</th><th style="min-width:140px">Barra</th></tr></thead>
            <tbody>
            @foreach($porAnio->sortByDesc('anio') as $a)
            @php $pa = $total > 0 ? round($a->total/$total*100,1) : 0; @endphp
            <tr>
                <td style="font-weight:700;color:#0f172a">{{ $a->anio ?? '—' }}</td>
                <td style="text-align:right;font-weight:700;color:#0f172a">{{ number_format($a->total) }}</td>
                <td><div style="height:5px;background:#f1f5f9;border-radius:3px;overflow:hidden"><div style="height:100%;background:#9333ea;border-radius:3px;width:{{ $pa }}%"></div></div></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- Tab: Juzgado × Estado pivot --}}
    <div id="tab-juzgado-pivot" style="display:none;overflow-x:auto">
        <table class="data-table" style="min-width:860px">
            <thead>
                <tr>
                    <th style="min-width:180px">Juzgado</th>
                    <th style="text-align:right">Total</th>
                    @foreach($canonKeys as $ck)
                    <th style="text-align:right;font-size:.67rem;max-width:80px;white-space:normal;line-height:1.3;padding:8px 10px">{{ $ck }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
            @foreach($porJuzgado as $j)
            @php
                $jRows = $pivotJuzgado[$j->id_juzgado] ?? collect();
                $colCounts = array_fill_keys($canonKeys, 0);
                foreach ($jRows as $r) {
                    $lbl = $estadoCanonMap[$r->id_estado] ?? null;
                    if ($lbl && isset($colCounts[$lbl])) $colCounts[$lbl] += $r->cnt;
                }
            @endphp
            <tr>
                <td style="font-size:.79rem;color:#374151;max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="{{ $j->nombre_juzgado }}">{{ $j->nombre_juzgado }}</td>
                <td style="text-align:right;font-weight:700;color:#0f172a;font-size:.85rem">{{ number_format($j->expedientes_count) }}</td>
                @foreach($canonKeys as $ck)
                <td style="text-align:right;font-size:.82rem;color:{{ $colCounts[$ck] > 0 ? '#0f172a' : '#cbd5e1' }}">
                    {{ $colCounts[$ck] > 0 ? number_format($colCounts[$ck]) : '—' }}
                </td>
                @endforeach
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- Tab: Materia × Estado pivot --}}
    <div id="tab-materia-pivot" style="display:none;overflow-x:auto">
        <table class="data-table" style="min-width:860px">
            <thead>
                <tr>
                    <th style="min-width:160px">Materia</th>
                    <th style="text-align:right">Total</th>
                    @foreach($canonKeys as $ck)
                    <th style="text-align:right;font-size:.67rem;max-width:80px;white-space:normal;line-height:1.3;padding:8px 10px">{{ $ck }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
            @foreach($porMateria as $m)
            @php
                $mRows = $pivotMateria[$m->id_materia] ?? collect();
                $colCounts = array_fill_keys($canonKeys, 0);
                foreach ($mRows as $r) {
                    $lbl = $estadoCanonMap[$r->id_estado] ?? null;
                    if ($lbl && isset($colCounts[$lbl])) $colCounts[$lbl] += $r->cnt;
                }
            @endphp
            <tr>
                <td style="font-size:.79rem;color:#374151;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="{{ $m->nombre_materia }}">{{ $m->nombre_materia }}</td>
                <td style="text-align:right;font-weight:700;color:#0f172a;font-size:.85rem">{{ number_format($m->expedientes_count) }}</td>
                @foreach($canonKeys as $ck)
                <td style="text-align:right;font-size:.82rem;color:{{ $colCounts[$ck] > 0 ? '#0f172a' : '#cbd5e1' }}">
                    {{ $colCounts[$ck] > 0 ? number_format($colCounts[$ck]) : '—' }}
                </td>
                @endforeach
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family = "'Inter','Segoe UI',system-ui,sans-serif";
Chart.defaults.color = '#64748b';
const PALETTE = ['#0f766e','#1d4ed8','#16a34a','#f59e0b','#9333ea','#ef4444','#0d9488','#2563eb','#059669','#d97706','#7c3aed','#dc2626','#0891b2','#4f46e5','#ca8a04'];

const estadoLabels  = @json($porEstado->pluck('nombre_estado'));
const estadoData    = @json($porEstado->pluck('expedientes_count'));
const anioLabels    = @json($porAnio->pluck('anio'));
const anioData      = @json($porAnio->pluck('total'));
const juzgadoLabels = @json($porJuzgadoChart->pluck('nombre_juzgado'));
const juzgadoData   = @json($porJuzgadoChart->pluck('expedientes_count'));
const materiaLabels = @json($porMateria->pluck('nombre_materia'));
const materiaData   = @json($porMateria->pluck('expedientes_count'));

const chartEstado = new Chart(document.getElementById('chartEstado'), {
    type: 'doughnut',
    data: { labels: estadoLabels, datasets: [{ data: estadoData, backgroundColor: PALETTE, borderWidth: 2, borderColor: '#fff' }] },
    options: {
        responsive: true, maintainAspectRatio: false, cutout: '62%',
        plugins: {
            legend: { position: 'right', labels: { font: { size: 11 }, boxWidth: 12, padding: 12, usePointStyle: true } },
            tooltip: { callbacks: { label(ctx) { const t=ctx.dataset.data.reduce((a,b)=>a+b,0); return ` ${ctx.label}: ${ctx.parsed.toLocaleString()} (${((ctx.parsed/t)*100).toFixed(1)}%)`; } } }
        }
    }
});

const chartAnio = new Chart(document.getElementById('chartAnio'), {
    type: 'bar',
    data: { labels: anioLabels.slice(), datasets: [{ label: 'Expedientes', data: anioData.slice(), backgroundColor: 'rgba(29,78,216,.85)', borderRadius: 7, borderSkipped: false }] },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { callbacks: { label(ctx) { return ` ${ctx.parsed.y.toLocaleString()} expedientes`; } } } },
        scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0 } } }
    }
});

const chartJuzgado = new Chart(document.getElementById('chartJuzgado'), {
    type: 'bar',
    data: { labels: juzgadoLabels, datasets: [{ label: 'Expedientes', data: juzgadoData, backgroundColor: juzgadoData.map((_,i)=>PALETTE[i%PALETTE.length]+'cc'), borderRadius: 5, borderSkipped: false }] },
    options: {
        indexAxis: 'y', responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0 } }, y: { ticks: { font: { size: 10 }, callback(v) { const l=this.getLabelForValue(v); return l.length>28?l.substring(0,26)+'...':l; } } } }
    }
});

const chartMateria = new Chart(document.getElementById('chartMateria'), {
    type: 'bar',
    data: { labels: materiaLabels, datasets: [{ label: 'Expedientes', data: materiaData, backgroundColor: 'rgba(15,118,110,.8)', borderRadius: 5, borderSkipped: false }] },
    options: {
        indexAxis: 'y', responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0 } }, y: { ticks: { font: { size: 10 }, callback(v) { const l=this.getLabelForValue(v); return l.length>28?l.substring(0,26)+'...':l; } } } }
    }
});

const chartRegistry = { chartEstado, chartAnio, chartJuzgado, chartMateria };

function toggleChartType(id, typeA, typeB) {
    const ch = chartRegistry[id];
    if (!ch) return;
    ch.config.type = ch.config.type === typeA ? typeB : typeA;
    if (ch.config.type === 'line') {
        Object.assign(ch.data.datasets[0], { tension: 0.4, fill: true, backgroundColor: 'rgba(29,78,216,.12)', borderColor: '#1d4ed8', borderWidth: 2.5, pointRadius: 4 });
    } else {
        Object.assign(ch.data.datasets[0], { backgroundColor: 'rgba(29,78,216,.85)', borderRadius: 7 });
    }
    ch.update();
}

function downloadChart(id, name) {
    const ch = chartRegistry[id];
    if (!ch) return;
    const a = document.createElement('a');
    a.href = ch.toBase64Image('image/png', 1);
    a.download = `grafico-${name}-${new Date().toISOString().slice(0,10)}.png`;
    a.click();
}

function filterAnioChart() {
    const desde = parseInt(document.getElementById('anio-desde').value) || -Infinity;
    const hasta = parseInt(document.getElementById('anio-hasta').value) || Infinity;
    const fl=[], fd=[];
    anioLabels.forEach((l,i) => { const y=parseInt(l); if(y>=desde&&y<=hasta){fl.push(l);fd.push(anioData[i]);} });
    chartAnio.data.labels = fl; chartAnio.data.datasets[0].data = fd; chartAnio.update();
}

const dashTabs = ['tab-juzgado-simple','tab-materia-simple','tab-anio','tab-juzgado-pivot','tab-materia-pivot'];
function showDashTab(id) {
    dashTabs.forEach(t => document.getElementById(t).style.display = t===id ? 'block' : 'none');
    dashTabs.forEach(t => {
        const b = document.getElementById('btn-'+t);
        const active = t===id;
        b.style.borderBottomColor = active ? '#0f766e' : 'transparent';
        b.style.color = active ? '#0f766e' : '#94a3b8';
    });
}

let kpiExpanded = false;
function toggleEmptyKpis() {
    kpiExpanded = !kpiExpanded;
    document.querySelectorAll('.kpi-empty').forEach(el => { el.style.display = kpiExpanded ? 'flex' : 'none'; });
    document.getElementById('btn-ver-mas').textContent = kpiExpanded ? 'Ocultar estados con 0' : 'Ver todos los estados (incluye con 0)';
}
</script>
@endpush

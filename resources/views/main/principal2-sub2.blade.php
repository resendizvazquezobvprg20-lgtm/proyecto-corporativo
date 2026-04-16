{{-- resources/views/main/principal2-sub2.blade.php --}}
@extends('layouts.app')
@section('title', 'Reportes')
@section('page-title', 'Principal 2 — Reportes')

@push('styles')
<style>
.report-card { border-radius:12px;padding:20px;background:#fff;box-shadow:0 2px 10px rgba(0,0,0,.06);cursor:pointer;transition:transform .15s,box-shadow .15s;border:1px solid #f1f5f9; }
.report-card:hover { transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,.1); }
.report-icon { width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:12px; }
.report-title { font-weight:700;color:#1a2a4a;font-size:.95rem;margin-bottom:4px; }
.report-desc  { font-size:.78rem;color:#6b7280; }
.kpi-row { background:linear-gradient(135deg,#1a2a4a,#2979ff);border-radius:14px;padding:24px 28px;color:#fff;margin-bottom:24px; }
.kpi-item .val { font-size:1.6rem;font-weight:700;line-height:1; }
.kpi-item .lbl { font-size:.78rem;opacity:.75;margin-top:4px; }
.trend-up   { color:#86efac;font-size:.78rem; }
.trend-down { color:#fca5a5;font-size:.78rem; }
.period-btn { border:1px solid rgba(255,255,255,.3);background:rgba(255,255,255,.1);color:#fff;border-radius:6px;padding:3px 12px;font-size:.8rem;cursor:pointer; }
.period-btn.active { background:rgba(255,255,255,.25); }
</style>
@endpush

@section('content')
<div class="mb-3 d-flex gap-2 flex-wrap" id="accionesBtns"></div>

{{-- KPI Banner --}}
<div class="kpi-row mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <div style="font-size:1.1rem;font-weight:700">Resumen Ejecutivo</div>
            <div style="font-size:.8rem;opacity:.7">Abril 2026</div>
        </div>
        <div class="d-flex gap-2">
            <button class="period-btn">7D</button>
            <button class="period-btn active">30D</button>
            <button class="period-btn">90D</button>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-6 col-md-3">
            <div class="kpi-item">
                <div class="val">$184,320</div>
                <div class="lbl">Ingresos totales</div>
                <div class="trend-up"><i class="bi bi-arrow-up"></i> +12.4% vs mes anterior</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-item">
                <div class="val">1,248</div>
                <div class="lbl">Transacciones</div>
                <div class="trend-up"><i class="bi bi-arrow-up"></i> +8.1%</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-item">
                <div class="val">$147.70</div>
                <div class="lbl">Ticket promedio</div>
                <div class="trend-up"><i class="bi bi-arrow-up"></i> +3.9%</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-item">
                <div class="val">94.3%</div>
                <div class="lbl">Satisfacción</div>
                <div class="trend-down"><i class="bi bi-arrow-down"></i> -0.8%</div>
            </div>
        </div>
    </div>
</div>

{{-- Reportes disponibles --}}
<div class="mb-3 fw-semibold" style="color:#1a2a4a;font-size:.9rem">
    <i class="bi bi-file-earmark-bar-graph me-2"></i>Reportes Disponibles
</div>
<div class="row g-3 mb-4">
    @php $reportes = [
        ['bi-bar-chart-line','#2979ff','#eff6ff','Ventas por Período','Analiza el comportamiento de ventas por día, semana o mes.','Último acceso: hoy'],
        ['bi-people','#16a34a','#f0fdf4','Análisis de Clientes','Segmentación, retención y valor de vida del cliente.','Último acceso: ayer'],
        ['bi-box-seam','#d97706','#fff7ed','Rotación de Inventario','Productos más y menos vendidos, stock vs demanda.','Último acceso: hace 3 días'],
        ['bi-cash-stack','#7c3aed','#faf5ff','Estado Financiero','P&L, flujo de caja y márgenes de ganancia.','Último acceso: hace 1 semana'],
        ['bi-person-badge','#dc2626','#fef2f2','Rendimiento de Equipo','KPIs por vendedor y cumplimiento de metas.','Nuevo'],
        ['bi-graph-up-arrow','#0891b2','#ecfeff','Proyecciones','Forecast de ventas basado en tendencias históricas.','Nuevo'],
    ]; @endphp
    @foreach($reportes as $r)
    <div class="col-sm-6 col-xl-4">
        <div class="report-card">
            <div class="report-icon" style="background:{{ $r[2] }};color:{{ $r[1] }}">
                <i class="bi {{ $r[0] }}"></i>
            </div>
            <div class="report-title">{{ $r[2+1] }}</div>
            <div class="report-desc mb-3">{{ $r[3+1] }}</div>
            <div class="d-flex justify-content-between align-items-center">
                <span style="font-size:.72rem;color:#9ca3af">{{ $r[5] }}</span>
                <button class="btn btn-sm" style="background:{{ $r[2] }};color:{{ $r[1] }};font-size:.78rem;padding:3px 12px;border-radius:8px;font-weight:600">
                    Ver reporte
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Tabla últimos reportes generados --}}
<div class="card">
    <div class="card-header"><i class="bi bi-clock-history me-2"></i>Últimos Reportes Generados</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Reporte</th>
                        <th>Generado por</th>
                        <th>Fecha</th>
                        <th>Formato</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php $historial = [
                        ['Ventas Marzo 2026','admin','15/04/2026 09:12','PDF'],
                        ['Clientes Activos Q1','admin','14/04/2026 16:45','Excel'],
                        ['Inventario Crítico','admin','13/04/2026 11:30','PDF'],
                        ['Estado Financiero Mar','admin','10/04/2026 08:00','Excel'],
                        ['Rendimiento Equipo Q1','admin','07/04/2026 14:22','PDF'],
                    ]; @endphp
                    @foreach($historial as $h)
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $h[0] }}</td>
                        <td class="text-muted small">{{ $h[1] }}</td>
                        <td class="text-muted small">{{ $h[2] }}</td>
                        <td>
                            @if($h[3]==='PDF')
                                <span style="background:#fee2e2;color:#991b1b;font-size:.72rem;padding:2px 9px;border-radius:20px;font-weight:600"><i class="bi bi-filetype-pdf me-1"></i>PDF</span>
                            @else
                                <span style="background:#dcfce7;color:#166534;font-size:.72rem;padding:2px 9px;border-radius:20px;font-weight:600"><i class="bi bi-filetype-xlsx me-1"></i>Excel</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-outline-primary btn-sm py-0 px-2"><i class="bi bi-download"></i></button>
                            <button class="btn btn-outline-secondary btn-sm py-0 px-2 ms-1"><i class="bi bi-eye"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(async () => {
    try {
        const res = await apiFetch('/api/menu');
        if (!res || !res.ok) return;
        const menus = await res.json();
        let permisos = null;
        for (const menu of menus) {
            const sub = menu.submenus.find(s => s.id === 8);
            if (sub) { permisos = sub; break; }
        }
        if (!permisos) return;
        const btns = document.getElementById('accionesBtns');
        if (permisos.bitAgregar)  btns.innerHTML += '<button class="btn btn-success btn-sm"><i class="bi bi-plus-lg me-1"></i>Nuevo Reporte</button>';
        if (permisos.bitEditar)   btns.innerHTML += '<button class="btn btn-warning btn-sm"><i class="bi bi-pencil me-1"></i>Editar</button>';
        if (permisos.bitEliminar) btns.innerHTML += '<button class="btn btn-danger btn-sm"><i class="bi bi-trash me-1"></i>Eliminar</button>';
        if (permisos.bitConsulta) btns.innerHTML += '<button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Consultar</button>';
        if (permisos.bitDetalle)  btns.innerHTML += '<button class="btn btn-info btn-sm"><i class="bi bi-eye me-1"></i>Detalle</button>';
    } catch(e) {}
})();
// Period buttons
document.querySelectorAll('.period-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>
@endpush

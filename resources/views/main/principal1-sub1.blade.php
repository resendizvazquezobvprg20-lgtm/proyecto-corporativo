{{-- resources/views/main/principal1-sub1.blade.php --}}
@extends('layouts.app')
@section('title', 'Ventas')
@section('page-title', 'Principal 1 — Ventas')

@push('styles')
<style>
.stat-card { border-radius:12px; padding:20px 24px; color:#fff; display:flex; align-items:center; gap:16px; }
.stat-card .stat-icon { font-size:2.2rem; opacity:.85; }
.stat-card .stat-val  { font-size:1.8rem; font-weight:700; line-height:1; }
.stat-card .stat-lbl  { font-size:.8rem; opacity:.8; margin-top:2px; }
.bg-sale   { background:linear-gradient(135deg,#2979ff,#1565c0); }
.bg-client { background:linear-gradient(135deg,#00c853,#1b5e20); }
.bg-ticket { background:linear-gradient(135deg,#ff6d00,#e65100); }
.bg-return { background:linear-gradient(135deg,#d500f9,#6a0080); }
.section-title { font-weight:700; color:#1a2a4a; font-size:.95rem; margin-bottom:12px; }
.badge-status { font-size:.75rem; padding:3px 10px; border-radius:20px; font-weight:600; }
.badge-completado { background:#dcfce7; color:#166534; }
.badge-pendiente  { background:#fef9c3; color:#854d0e; }
.badge-cancelado  { background:#fee2e2; color:#991b1b; }
</style>
@endpush

@section('content')
<div class="mb-3 d-flex gap-2 flex-wrap" id="accionesBtns"></div>

{{-- KPIs --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-sale">
            <div class="stat-icon"><i class="bi bi-cart-check-fill"></i></div>
            <div>
                <div class="stat-val">$184,320</div>
                <div class="stat-lbl">Ventas del mes</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-client">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="stat-val">1,248</div>
                <div class="stat-lbl">Clientes atendidos</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-ticket">
            <div class="stat-icon"><i class="bi bi-receipt"></i></div>
            <div>
                <div class="stat-val">$147.70</div>
                <div class="stat-lbl">Ticket promedio</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-return">
            <div class="stat-icon"><i class="bi bi-arrow-repeat"></i></div>
            <div>
                <div class="stat-val">3.2%</div>
                <div class="stat-lbl">Tasa de devolución</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Tabla ventas recientes --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-table me-2"></i>Ventas Recientes</span>
                <span class="badge bg-primary">Últimas 10</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#Folio</th>
                                <th>Cliente</th>
                                <th>Producto</th>
                                <th>Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $ventas = [
                                ['F-1091','García López','Laptop Pro 15"','$23,500','completado'],
                                ['F-1090','Martínez R.','Teclado Mecánico','$1,200','completado'],
                                ['F-1089','Hernández A.','Monitor 27"','$8,900','pendiente'],
                                ['F-1088','Rodríguez M.','Mouse Inalámbrico','$650','completado'],
                                ['F-1087','López Torres','Silla Ergonómica','$5,400','cancelado'],
                                ['F-1086','Pérez Díaz','Auriculares BT','$2,300','completado'],
                                ['F-1085','Sánchez V.','Webcam HD','$1,800','pendiente'],
                                ['F-1084','Flores R.','Hub USB-C','$890','completado'],
                                ['F-1083','Cruz Medina','Disco SSD 1TB','$3,200','completado'],
                                ['F-1082','Torres G.','Impresora Láser','$4,750','pendiente'],
                            ]; @endphp
                            @foreach($ventas as $v)
                            <tr>
                                <td class="ps-3 fw-semibold text-primary">{{ $v[0] }}</td>
                                <td>{{ $v[1] }}</td>
                                <td>{{ $v[2] }}</td>
                                <td class="fw-semibold">{{ $v[3] }}</td>
                                <td><span class="badge-status badge-{{ $v[4] }}">{{ ucfirst($v[4]) }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Top productos --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-trophy me-2"></i>Top Productos</div>
            <div class="card-body">
                @php $productos = [
                    ['Laptop Pro 15"', 84, '#2979ff'],
                    ['Monitor 27"', 67, '#00c853'],
                    ['Silla Ergonómica', 53, '#ff6d00'],
                    ['Teclado Mecánico', 41, '#d500f9'],
                    ['Auriculares BT', 35, '#f44336'],
                ]; @endphp
                @foreach($productos as $p)
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="fw-semibold">{{ $p[0] }}</small>
                        <small class="text-muted">{{ $p[1] }} uds</small>
                    </div>
                    <div class="progress" style="height:7px;border-radius:10px">
                        <div class="progress-bar" style="width:{{ round($p[1]/84*100) }}%;background:{{ $p[2] }};border-radius:10px"></div>
                    </div>
                </div>
                @endforeach
            </div>
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
            const sub = menu.submenus.find(s => s.id === 5);
            if (sub) { permisos = sub; break; }
        }
        if (!permisos) return;
        const btns = document.getElementById('accionesBtns');
        if (permisos.bitAgregar)  btns.innerHTML += '<button class="btn btn-success btn-sm"><i class="bi bi-plus-lg me-1"></i>Nueva Venta</button>';
        if (permisos.bitEditar)   btns.innerHTML += '<button class="btn btn-warning btn-sm"><i class="bi bi-pencil me-1"></i>Editar</button>';
        if (permisos.bitEliminar) btns.innerHTML += '<button class="btn btn-danger btn-sm"><i class="bi bi-trash me-1"></i>Eliminar</button>';
        if (permisos.bitConsulta) btns.innerHTML += '<button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Consultar</button>';
        if (permisos.bitDetalle)  btns.innerHTML += '<button class="btn btn-info btn-sm"><i class="bi bi-eye me-1"></i>Detalle</button>';
    } catch(e) {}
})();
</script>
@endpush

{{-- resources/views/main/principal1-sub2.blade.php --}}
@extends('layouts.app')
@section('title', 'Clientes')
@section('page-title', 'Principal 1 — Clientes')

@push('styles')
<style>
.client-avatar { width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#2979ff,#1565c0);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;flex-shrink:0; }
.stat-mini { border-radius:10px;padding:16px 20px;border-left:4px solid; }
.stat-mini.blue  { border-color:#2979ff;background:#eff6ff; }
.stat-mini.green { border-color:#16a34a;background:#f0fdf4; }
.stat-mini.orange{ border-color:#ea580c;background:#fff7ed; }
.stat-mini .val  { font-size:1.5rem;font-weight:700;color:#1a2a4a; }
.stat-mini .lbl  { font-size:.78rem;color:#6b7280;margin-top:2px; }
.badge-vip    { background:#fef9c3;color:#854d0e;font-size:.72rem;padding:2px 8px;border-radius:20px;font-weight:600; }
.badge-nuevo  { background:#dcfce7;color:#166534;font-size:.72rem;padding:2px 8px;border-radius:20px;font-weight:600; }
.badge-regular{ background:#f1f5f9;color:#475569;font-size:.72rem;padding:2px 8px;border-radius:20px;font-weight:600; }
</style>
@endpush

@section('content')
<div class="mb-3 d-flex gap-2 flex-wrap" id="accionesBtns"></div>

<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-mini blue">
            <div class="val">3,842</div>
            <div class="lbl"><i class="bi bi-people me-1"></i>Total clientes</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-mini green">
            <div class="val">127</div>
            <div class="lbl"><i class="bi bi-person-plus me-1"></i>Nuevos este mes</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-mini orange">
            <div class="val">94.3%</div>
            <div class="lbl"><i class="bi bi-star me-1"></i>Satisfacción</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-people me-2"></i>Directorio de Clientes</span>
                <input type="text" class="form-control form-control-sm w-auto" placeholder="Buscar...">
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Cliente</th>
                                <th>Correo</th>
                                <th>Ciudad</th>
                                <th>Compras</th>
                                <th>Tipo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $clientes = [
                                ['Ana García','ana@email.com','CDMX','$48,200','VIP'],
                                ['Carlos Martínez','carlos@email.com','Monterrey','$22,400','VIP'],
                                ['Laura Hernández','laura@email.com','Guadalajara','$15,600','Regular'],
                                ['Miguel Rodríguez','miguel@email.com','Puebla','$8,900','Regular'],
                                ['Sofía López','sofia@email.com','CDMX','$3,100','Nuevo'],
                                ['Javier Torres','javier@email.com','Tijuana','$19,750','VIP'],
                                ['María Sánchez','maria@email.com','León','$6,400','Regular'],
                                ['Roberto Flores','roberto@email.com','CDMX','$1,200','Nuevo'],
                            ]; @endphp
                            @foreach($clientes as $c)
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="client-avatar">{{ strtoupper(substr($c[0],0,1)) }}</div>
                                        <span class="fw-semibold">{{ $c[0] }}</span>
                                    </div>
                                </td>
                                <td class="text-muted small">{{ $c[1] }}</td>
                                <td>{{ $c[2] }}</td>
                                <td class="fw-semibold text-success">{{ $c[3] }}</td>
                                <td>
                                    @if($c[4]==='VIP') <span class="badge-vip">⭐ VIP</span>
                                    @elseif($c[4]==='Nuevo') <span class="badge-nuevo">🌱 Nuevo</span>
                                    @else <span class="badge-regular">Regular</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-geo-alt me-2"></i>Clientes por Ciudad</div>
            <div class="card-body">
                @php $ciudades = [['CDMX',45,'#2979ff'],['Monterrey',22,'#00c853'],['Guadalajara',18,'#ff6d00'],['Puebla',9,'#d500f9'],['Otros',6,'#9e9e9e']]; @endphp
                @foreach($ciudades as $ci)
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:10px;height:10px;border-radius:50%;background:{{ $ci[2] }}"></div>
                        <small>{{ $ci[0] }}</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="width:80px;height:6px;border-radius:10px">
                            <div class="progress-bar" style="width:{{ $ci[1] }}%;background:{{ $ci[2] }}"></div>
                        </div>
                        <small class="text-muted">{{ $ci[1] }}%</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history me-2"></i>Actividad Reciente</div>
            <div class="card-body p-0">
                @php $actividad = [
                    ['Ana García registró una compra','hace 5 min','text-success'],
                    ['Carlos actualizó sus datos','hace 18 min','text-primary'],
                    ['Sofía López se registró','hace 1h','text-info'],
                    ['Miguel solicitó soporte','hace 2h','text-warning'],
                ]; @endphp
                <ul class="list-group list-group-flush">
                    @foreach($actividad as $a)
                    <li class="list-group-item py-2 px-3">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-circle-fill {{ $a[2] }} mt-1" style="font-size:.45rem"></i>
                            <div>
                                <div class="small">{{ $a[0] }}</div>
                                <div class="text-muted" style="font-size:.72rem">{{ $a[1] }}</div>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
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
            const sub = menu.submenus.find(s => s.id === 6);
            if (sub) { permisos = sub; break; }
        }
        if (!permisos) return;
        const btns = document.getElementById('accionesBtns');
        if (permisos.bitAgregar)  btns.innerHTML += '<button class="btn btn-success btn-sm"><i class="bi bi-person-plus me-1"></i>Nuevo Cliente</button>';
        if (permisos.bitEditar)   btns.innerHTML += '<button class="btn btn-warning btn-sm"><i class="bi bi-pencil me-1"></i>Editar</button>';
        if (permisos.bitEliminar) btns.innerHTML += '<button class="btn btn-danger btn-sm"><i class="bi bi-trash me-1"></i>Eliminar</button>';
        if (permisos.bitConsulta) btns.innerHTML += '<button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Consultar</button>';
        if (permisos.bitDetalle)  btns.innerHTML += '<button class="btn btn-info btn-sm"><i class="bi bi-eye me-1"></i>Detalle</button>';
    } catch(e) {}
})();
</script>
@endpush

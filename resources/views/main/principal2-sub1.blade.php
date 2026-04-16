{{-- resources/views/main/principal2-sub1.blade.php --}}
@extends('layouts.app')
@section('title', 'Inventario')
@section('page-title', 'Principal 2 — Inventario')

@push('styles')
<style>
.inv-kpi { border-radius:12px;padding:18px 22px;background:#fff;box-shadow:0 2px 10px rgba(0,0,0,.06);border-top:4px solid; }
.inv-kpi.blue   { border-color:#2979ff; }
.inv-kpi.green  { border-color:#16a34a; }
.inv-kpi.red    { border-color:#dc2626; }
.inv-kpi.amber  { border-color:#d97706; }
.inv-kpi .val   { font-size:1.7rem;font-weight:700;color:#1a2a4a; }
.inv-kpi .lbl   { font-size:.78rem;color:#6b7280;margin-top:3px; }
.stock-bar { height:8px;border-radius:10px;background:#f1f5f9;overflow:hidden; }
.stock-bar .fill { height:100%;border-radius:10px;transition:width .5s; }
.badge-ok    { background:#dcfce7;color:#166534;font-size:.72rem;padding:2px 9px;border-radius:20px;font-weight:600; }
.badge-low   { background:#fef9c3;color:#854d0e;font-size:.72rem;padding:2px 9px;border-radius:20px;font-weight:600; }
.badge-out   { background:#fee2e2;color:#991b1b;font-size:.72rem;padding:2px 9px;border-radius:20px;font-weight:600; }
</style>
@endpush

@section('content')
<div class="mb-3 d-flex gap-2 flex-wrap" id="accionesBtns"></div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="inv-kpi blue">
            <div class="val">2,847</div>
            <div class="lbl"><i class="bi bi-boxes me-1"></i>Productos en stock</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="inv-kpi green">
            <div class="val">$1.2M</div>
            <div class="lbl"><i class="bi bi-cash-stack me-1"></i>Valor del inventario</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="inv-kpi amber">
            <div class="val">38</div>
            <div class="lbl"><i class="bi bi-exclamation-triangle me-1"></i>Stock bajo</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="inv-kpi red">
            <div class="val">12</div>
            <div class="lbl"><i class="bi bi-x-circle me-1"></i>Agotados</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-box-seam me-2"></i>Control de Inventario</span>
                <span class="badge bg-secondary">Actualizado hoy</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">SKU</th>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Stock</th>
                                <th>Nivel</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $items = [
                                ['SKU-001','Laptop Pro 15"','Electrónica',42,84,'ok'],
                                ['SKU-002','Monitor 27" 4K','Electrónica',18,60,'ok'],
                                ['SKU-003','Teclado Mecánico','Periféricos',7,25,'low'],
                                ['SKU-004','Mouse Inalámbrico','Periféricos',65,100,'ok'],
                                ['SKU-005','Silla Ergonómica','Mobiliario',3,15,'low'],
                                ['SKU-006','Auriculares BT','Audio',0,0,'out'],
                                ['SKU-007','Webcam HD 1080p','Periféricos',29,70,'ok'],
                                ['SKU-008','Hub USB-C 7 puertos','Accesorios',5,20,'low'],
                                ['SKU-009','Disco SSD 1TB','Almacenamiento',33,80,'ok'],
                                ['SKU-010','Impresora Láser','Impresión',0,0,'out'],
                            ]; @endphp
                            @foreach($items as $item)
                            <tr>
                                <td class="ps-3"><code class="text-primary">{{ $item[0] }}</code></td>
                                <td class="fw-semibold">{{ $item[1] }}</td>
                                <td class="text-muted small">{{ $item[2] }}</td>
                                <td>{{ $item[3] }} uds</td>
                                <td style="min-width:80px">
                                    <div class="stock-bar">
                                        <div class="fill" style="width:{{ $item[4] }}%;background:{{ $item[5]==='out'?'#dc2626':($item[5]==='low'?'#d97706':'#16a34a') }}"></div>
                                    </div>
                                </td>
                                <td>
                                    @if($item[5]==='ok') <span class="badge-ok">✓ Normal</span>
                                    @elseif($item[5]==='low') <span class="badge-low">⚠ Bajo</span>
                                    @else <span class="badge-out">✕ Agotado</span>
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
            <div class="card-header"><i class="bi bi-diagram-3 me-2"></i>Por Categoría</div>
            <div class="card-body">
                @php $cats = [['Electrónica',38,'#2979ff'],['Periféricos',27,'#00c853'],['Mobiliario',14,'#ff6d00'],['Audio',11,'#d500f9'],['Otros',10,'#9e9e9e']]; @endphp
                @foreach($cats as $cat)
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="fw-semibold">{{ $cat[0] }}</small>
                        <small class="text-muted">{{ $cat[1] }}%</small>
                    </div>
                    <div class="stock-bar">
                        <div class="fill" style="width:{{ $cat[1] }}%;background:{{ $cat[2] }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="card">
            <div class="card-header text-danger"><i class="bi bi-bell me-2"></i>Alertas de Stock</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item py-2 px-3 d-flex justify-content-between align-items-center">
                        <div><div class="small fw-semibold">Auriculares BT</div><div class="text-muted" style="font-size:.72rem">Agotado — reorden urgente</div></div>
                        <span class="badge-out">0 uds</span>
                    </li>
                    <li class="list-group-item py-2 px-3 d-flex justify-content-between align-items-center">
                        <div><div class="small fw-semibold">Impresora Láser</div><div class="text-muted" style="font-size:.72rem">Agotado — reorden urgente</div></div>
                        <span class="badge-out">0 uds</span>
                    </li>
                    <li class="list-group-item py-2 px-3 d-flex justify-content-between align-items-center">
                        <div><div class="small fw-semibold">Silla Ergonómica</div><div class="text-muted" style="font-size:.72rem">Stock crítico</div></div>
                        <span class="badge-low">3 uds</span>
                    </li>
                    <li class="list-group-item py-2 px-3 d-flex justify-content-between align-items-center">
                        <div><div class="small fw-semibold">Teclado Mecánico</div><div class="text-muted" style="font-size:.72rem">Stock bajo</div></div>
                        <span class="badge-low">7 uds</span>
                    </li>
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
            const sub = menu.submenus.find(s => s.id === 7);
            if (sub) { permisos = sub; break; }
        }
        if (!permisos) return;
        const btns = document.getElementById('accionesBtns');
        if (permisos.bitAgregar)  btns.innerHTML += '<button class="btn btn-success btn-sm"><i class="bi bi-plus-lg me-1"></i>Nuevo Producto</button>';
        if (permisos.bitEditar)   btns.innerHTML += '<button class="btn btn-warning btn-sm"><i class="bi bi-pencil me-1"></i>Editar</button>';
        if (permisos.bitEliminar) btns.innerHTML += '<button class="btn btn-danger btn-sm"><i class="bi bi-trash me-1"></i>Eliminar</button>';
        if (permisos.bitConsulta) btns.innerHTML += '<button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Consultar</button>';
        if (permisos.bitDetalle)  btns.innerHTML += '<button class="btn btn-info btn-sm"><i class="bi bi-eye me-1"></i>Detalle</button>';
    } catch(e) {}
})();
</script>
@endpush

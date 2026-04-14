{{-- resources/views/main/principal2-sub1.blade.php --}}
@extends('layouts.app')
@section('title', 'Inventario')
@section('page-title', 'Principal 2 — Inventario')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-box-seam me-2"></i>Control de Inventario</span>
    </div>
    <div class="card-body">
        <div class="mb-3 d-flex gap-2 flex-wrap" id="accionesBtns">
            {{-- Botones renderizados por JS según permisos --}}
        </div>
        <div class="alert alert-secondary d-flex align-items-center gap-2 mb-0">
            <i class="bi bi-info-circle-fill fs-5"></i>
            <div>Módulo <strong>Inventario</strong> — pantalla estática de demostración. Los botones visibles corresponden a los permisos asignados a tu perfil.</div>
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
        if (permisos.bitAgregar)  btns.innerHTML += '<button class="btn btn-success btn-sm"><i class="bi bi-plus-lg me-1"></i>Agregar</button>';
        if (permisos.bitEditar)   btns.innerHTML += '<button class="btn btn-warning btn-sm"><i class="bi bi-pencil me-1"></i>Editar</button>';
        if (permisos.bitEliminar) btns.innerHTML += '<button class="btn btn-danger btn-sm"><i class="bi bi-trash me-1"></i>Eliminar</button>';
        if (permisos.bitConsulta) btns.innerHTML += '<button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Consultar</button>';
        if (permisos.bitDetalle)  btns.innerHTML += '<button class="btn btn-info btn-sm"><i class="bi bi-eye me-1"></i>Detalle</button>';
    } catch(e) { console.error('Error cargando permisos:', e); }
})();
</script>
@endpush

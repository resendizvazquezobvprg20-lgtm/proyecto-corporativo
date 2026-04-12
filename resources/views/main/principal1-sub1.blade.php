{{-- resources/views/main/principal1-sub1.blade.php --}}
@extends('layouts.app')
@section('title','Principal 1.1')
@section('page-title','Principal 1 — Submenú 1')

@section('content')
<div class="card">
    <div class="card-header"><i class="bi bi-grid me-2"></i>Principal 1 — Sub 1</div>
    <div class="card-body">
        <div class="mb-3 d-flex gap-2 flex-wrap" id="accionesBtns">
            {{-- Los botones se renderizan por JS según permisos --}}
        </div>
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>Pantalla estática — Principal 1.1.
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(async () => {
    try {
        const res   = await apiFetch('/api/menu');
        if (!res || !res.ok) return;
        const menus = await res.json();
        let permisos = null;
        for (const menu of menus) {
            const sub = menu.submenus.find(s => s.id === 5);
            if (sub) { permisos = sub; break; }
        }
        if (!permisos) return;
        const btns = document.getElementById('accionesBtns');
        if (permisos.bitAgregar)  btns.innerHTML += '<button class="btn btn-success btn-sm"><i class="bi bi-plus-lg me-1"></i>Agregar</button>';
        if (permisos.bitEditar)   btns.innerHTML += '<button class="btn btn-warning btn-sm"><i class="bi bi-pencil me-1"></i>Editar</button>';
        if (permisos.bitEliminar) btns.innerHTML += '<button class="btn btn-danger btn-sm"><i class="bi bi-trash me-1"></i>Eliminar</button>';
        if (permisos.bitConsulta) btns.innerHTML += '<button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Consultar</button>';
        if (permisos.bitDetalle)  btns.innerHTML += '<button class="btn btn-info btn-sm"><i class="bi bi-eye me-1"></i>Detalle</button>';
    } catch(e) { console.error(e); }
})();
</script>
@endpush

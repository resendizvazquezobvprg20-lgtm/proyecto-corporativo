@extends('layouts.app')
@section('title','Editar Módulo')
@section('page-title','Editar Módulo')

@section('content')
<div class="card" style="max-width:560px">
    <div class="card-header"><i class="bi bi-puzzle me-2"></i>Editar Módulo</div>
    <div class="card-body">
        <div id="formErrors" class="alert alert-danger d-none"></div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Nombre del Módulo <span class="text-danger">*</span></label>
            <input type="text" id="strNombreModulo" class="form-control" maxlength="100">
            <div class="invalid-feedback">El nombre es obligatorio.</div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Ruta <span class="text-muted fw-normal">(URL del módulo)</span></label>
            <input type="text" id="strRuta" class="form-control" maxlength="150" placeholder="Ej: /reportes/ventas">
            <div class="form-text">Ruta que aparecerá en el menú lateral al hacer clic.</div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Menú padre <span class="text-muted fw-normal">(grupo en el sidebar)</span></label>
            <select id="idMenu" class="form-select">
                <option value="">— Sin menú —</option>
                @foreach($menus as $menu)
                    <option value="{{ $menu->id }}">{{ $menu->strNombreMenu }}</option>
                @endforeach
            </select>
            <div class="form-text">Selecciona a qué sección del menú pertenece este módulo.</div>
        </div>
    </div>
    <div class="card-footer d-flex gap-2 justify-content-end">
        <a href="{{ route('modulo.index') }}" class="btn btn-secondary btn-sm">Cancelar</a>
        <button class="btn btn-primary btn-sm" id="btnSave" onclick="guardar()">
            <i class="bi bi-floppy me-1"></i>Guardar Cambios
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
const moduloId = {{ $id }};

(async () => {
    const res  = await apiFetch(`{{ url('api/modulo') }}/${moduloId}`);
    const data = await res.json();
    document.getElementById('strNombreModulo').value = data.strNombreModulo || '';
    document.getElementById('strRuta').value         = data.strRuta || '';
    if (data.idMenu) {
        document.getElementById('idMenu').value = data.idMenu;
    }
})();

async function guardar() {
    const nombre = document.getElementById('strNombreModulo');
    const ruta   = document.getElementById('strRuta');
    const idMenu = document.getElementById('idMenu');
    const errDiv = document.getElementById('formErrors');
    const btn    = document.getElementById('btnSave');

    errDiv.classList.add('d-none');
    if (!nombre.value.trim()) { nombre.classList.add('is-invalid'); return; }
    nombre.classList.remove('is-invalid');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

    const payload = {
        strNombreModulo: nombre.value.trim(),
        strRuta:         ruta.value.trim() || null,
        idMenu:          idMenu.value || null,
    };

    const res  = await apiFetch(`{{ url('api/modulo') }}/${moduloId}`, {
        method: 'PUT',
        body: JSON.stringify(payload)
    });
    const data = await res.json();

    if (data.success) {
        window.location.href = '{{ route("modulo.index") }}';
    } else {
        const msgs = Object.values(data.errors || {}).flat();
        errDiv.innerHTML = msgs.join('<br>') || data.message;
        errDiv.classList.remove('d-none');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-floppy me-1"></i>Guardar Cambios';
    }
}
</script>
@endpush
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
            <div class="form-text text-end"><span id="cntNombre">0</span>/100 caracteres</div>
            <div class="invalid-feedback" id="strNombreModulo-err">El nombre es obligatorio.</div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Ruta <span class="text-muted fw-normal">(URL del módulo)</span></label>
            <input type="text" id="strRuta" class="form-control" maxlength="150" placeholder="Ej: /reportes/ventas">
            <div class="form-text d-flex justify-content-between">
                <span>Ruta que aparecerá en el menú lateral al hacer clic.</span>
                <span><span id="cntRuta">0</span>/150</span>
            </div>
            <div class="invalid-feedback" id="strRuta-err">La ruta no puede superar 150 caracteres.</div>
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

// Contadores de caracteres
document.getElementById('strNombreModulo').addEventListener('input', function () {
    document.getElementById('cntNombre').textContent = this.value.length;
});
document.getElementById('strRuta').addEventListener('input', function () {
    document.getElementById('cntRuta').textContent = this.value.length;
});

(async () => {
    const res  = await apiFetch(`{{ url('api/modulo') }}/${moduloId}`);
    const data = await res.json();
    const nombre = data.strNombreModulo || '';
    const ruta   = data.strRuta || '';
    document.getElementById('strNombreModulo').value = nombre;
    document.getElementById('strRuta').value         = ruta;
    document.getElementById('cntNombre').textContent = nombre.length;
    document.getElementById('cntRuta').textContent   = ruta.length;
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
    let ok = true;

    errDiv.classList.add('d-none');
    [nombre, ruta].forEach(f => { f.classList.remove('is-invalid'); });

    // Nombre: obligatorio, mínimo 3, máximo 100
    const nombreVal = nombre.value.trim();
    if (!nombreVal) {
        nombre.classList.add('is-invalid');
        document.getElementById('strNombreModulo-err').textContent = 'El nombre es obligatorio.';
        ok = false;
    } else if (nombreVal.length < 3) {
        nombre.classList.add('is-invalid');
        document.getElementById('strNombreModulo-err').textContent = 'El nombre debe tener al menos 3 caracteres.';
        ok = false;
    } else if (nombreVal.length > 100) {
        nombre.classList.add('is-invalid');
        document.getElementById('strNombreModulo-err').textContent = 'El nombre no puede superar 100 caracteres.';
        ok = false;
    }

    // Ruta: opcional, máx 150
    const rutaVal = ruta.value.trim();
    if (rutaVal.length > 150) {
        ruta.classList.add('is-invalid');
        document.getElementById('strRuta-err').textContent = 'La ruta no puede superar 150 caracteres.';
        ok = false;
    }

    if (!ok) return;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

    const payload = {
        strNombreModulo: nombreVal,
        strRuta:         rutaVal || null,
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
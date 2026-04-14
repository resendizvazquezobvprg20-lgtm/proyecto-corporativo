@extends('layouts.app')
@section('title','Nuevo Perfil')
@section('page-title','Nuevo Perfil')

@section('content')
<div class="card" style="max-width:520px">
    <div class="card-header"><i class="bi bi-people me-2"></i>Nuevo Perfil</div>
    <div class="card-body">
        <div id="formErrors" class="alert alert-danger d-none"></div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Nombre del Perfil <span class="text-danger">*</span></label>
            <input type="text" id="strNombrePerfil" class="form-control" maxlength="100" placeholder="Ej: Supervisor">
            <div class="invalid-feedback">El nombre es obligatorio.</div>
        </div>
        <div class="mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="bitAdministrador" role="switch">
                <label class="form-check-label fw-semibold" for="bitAdministrador">¿Es Administrador?</label>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex gap-2 justify-content-end">
        <a href="{{ route('perfil.index') }}" class="btn btn-secondary btn-sm">Cancelar</a>
        <button class="btn btn-primary btn-sm" id="btnSave" onclick="guardar()">
            <i class="bi bi-floppy me-1"></i>Guardar
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function guardar() {
    const nombre = document.getElementById('strNombrePerfil');
    const isAdmin = document.getElementById('bitAdministrador').checked;
    const errDiv = document.getElementById('formErrors');
    const btn = document.getElementById('btnSave');

    errDiv.classList.add('d-none');
    if (!nombre.value.trim()) { nombre.classList.add('is-invalid'); return; }
    nombre.classList.remove('is-invalid');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

    const res  = await apiFetch('{{ url("api/perfil") }}', {
        method: 'POST',
        body: JSON.stringify({ strNombrePerfil: nombre.value.trim(), bitAdministrador: isAdmin })
    });
    const data = await res.json();

    if (data.success) {
        window.location.href = '{{ route("perfil.index") }}';
    } else {
        const msgs = Object.values(data.errors || {}).flat();
        errDiv.innerHTML = msgs.join('<br>') || data.message;
        errDiv.classList.remove('d-none');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-floppy me-1"></i>Guardar';
    }
}
</script>
@endpush

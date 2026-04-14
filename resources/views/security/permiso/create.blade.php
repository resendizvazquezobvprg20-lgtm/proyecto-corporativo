@extends('layouts.app')
@section('title','Nuevo Permiso')
@section('page-title','Nuevo Permiso')

@section('content')
<div class="card" style="max-width:600px">
    <div class="card-header"><i class="bi bi-shield-check me-2"></i>Nuevo Permiso</div>
    <div class="card-body">
        <div id="formErrors" class="alert alert-danger d-none"></div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Perfil <span class="text-danger">*</span></label>
                <select id="idPerfil" class="form-select">
                    <option value="">-- Selecciona --</option>
                    @foreach($perfiles as $p)
                        <option value="{{ $p->id }}">{{ $p->strNombrePerfil }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback">Selecciona un perfil.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Módulo <span class="text-danger">*</span></label>
                <select id="idModulo" class="form-select">
                    <option value="">-- Selecciona --</option>
                    @foreach($modulos as $m)
                        <option value="{{ $m->id }}">{{ $m->strNombreModulo }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback">Selecciona un módulo.</div>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Permisos</label>
                <div class="d-flex gap-4 flex-wrap mt-1">
                    @foreach(['bitAgregar'=>'Agregar','bitEditar'=>'Editar','bitConsulta'=>'Consulta','bitEliminar'=>'Eliminar','bitDetalle'=>'Detalle'] as $key=>$label)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="{{ $key }}" value="1">
                        <label class="form-check-label" for="{{ $key }}">{{ $label }}</label>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex gap-2 justify-content-end">
        <a href="{{ route('permiso.index') }}" class="btn btn-secondary btn-sm">Cancelar</a>
        <button class="btn btn-primary btn-sm" id="btnSave" onclick="guardar()">
            <i class="bi bi-floppy me-1"></i>Guardar
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function guardar() {
    const perfil = document.getElementById('idPerfil');
    const modulo = document.getElementById('idModulo');
    const errDiv = document.getElementById('formErrors');
    const btn    = document.getElementById('btnSave');
    let ok = true;

    errDiv.classList.add('d-none');
    [perfil, modulo].forEach(f => f.classList.remove('is-invalid'));

    if (!perfil.value) { perfil.classList.add('is-invalid'); ok = false; }
    if (!modulo.value) { modulo.classList.add('is-invalid'); ok = false; }
    if (!ok) return;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

    const body = {
        idPerfil:    parseInt(perfil.value),
        idModulo:    parseInt(modulo.value),
        bitAgregar:  document.getElementById('bitAgregar').checked,
        bitEditar:   document.getElementById('bitEditar').checked,
        bitConsulta: document.getElementById('bitConsulta').checked,
        bitEliminar: document.getElementById('bitEliminar').checked,
        bitDetalle:  document.getElementById('bitDetalle').checked,
    };

    const res  = await apiFetch('{{ url("api/permiso") }}', { method: 'POST', body: JSON.stringify(body) });
    const data = await res.json();

    if (data.success) {
        window.location.href = '{{ route("permiso.index") }}';
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

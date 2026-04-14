@extends('layouts.app')
@section('title','Editar Permiso')
@section('page-title','Editar Permiso')

@section('content')
<div class="card" style="max-width:600px">
    <div class="card-header"><i class="bi bi-shield-check me-2"></i>Editar Permiso</div>
    <div class="card-body">
        <div id="formErrors" class="alert alert-danger d-none"></div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Perfil</label>
                <input type="text" id="perfilNombre" class="form-control" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Módulo</label>
                <input type="text" id="moduloNombre" class="form-control" disabled>
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
            <i class="bi bi-floppy me-1"></i>Guardar Cambios
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
const permisoId = {{ $id }};

(async () => {
    const res = await apiFetch(`{{ url('api/permiso') }}/${permisoId}`);
    const d   = await res.json();
    document.getElementById('perfilNombre').value  = d.perfil?.strNombrePerfil ?? d.idPerfil;
    document.getElementById('moduloNombre').value  = d.modulo?.strNombreModulo ?? d.idModulo;
    document.getElementById('bitAgregar').checked  = !!d.bitAgregar;
    document.getElementById('bitEditar').checked   = !!d.bitEditar;
    document.getElementById('bitConsulta').checked = !!d.bitConsulta;
    document.getElementById('bitEliminar').checked = !!d.bitEliminar;
    document.getElementById('bitDetalle').checked  = !!d.bitDetalle;
})();

async function guardar() {
    const errDiv = document.getElementById('formErrors');
    const btn    = document.getElementById('btnSave');
    errDiv.classList.add('d-none');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

    const body = {
        bitAgregar:  document.getElementById('bitAgregar').checked,
        bitEditar:   document.getElementById('bitEditar').checked,
        bitConsulta: document.getElementById('bitConsulta').checked,
        bitEliminar: document.getElementById('bitEliminar').checked,
        bitDetalle:  document.getElementById('bitDetalle').checked,
    };

    const res  = await apiFetch(`{{ url('api/permiso') }}/${permisoId}`, {
        method: 'PUT', body: JSON.stringify(body)
    });
    const data = await res.json();

    if (data.success) {
        window.location.href = '{{ route("permiso.index") }}';
    } else {
        errDiv.innerHTML = data.message || 'Error al guardar.';
        errDiv.classList.remove('d-none');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-floppy me-1"></i>Guardar Cambios';
    }
}
</script>
@endpush

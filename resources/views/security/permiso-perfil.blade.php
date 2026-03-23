{{-- ============================================================ --}}
{{-- ARCHIVO: resources/views/security/permiso-perfil.blade.php --}}
{{-- Sin paginado, sin filtro. Checkboxes por módulo/perfil     --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'Permisos Perfil')
@section('page-title', 'Permisos por Perfil')

@section('content')
@php
    use Tymon\JWTAuth\Facades\JWTAuth;
    use App\Models\{PermisoPerfil, Usuario};

    try {
        $currentUser  = JWTAuth::parseToken()->authenticate();
        $currentModel = Usuario::with('perfil')->find($currentUser->id);
        $esAdmin      = $currentModel?->perfil?->bitAdministrador ?? false;
    } catch (\Exception $e) {
        $currentModel = null;
        $esAdmin      = false;
    }

    $permisos = $esAdmin
        ? ['bitAgregar'=>true,'bitEditar'=>true,'bitEliminar'=>true,'bitConsulta'=>true,'bitDetalle'=>true]
        : ($currentModel
            ? (PermisoPerfil::where('idPerfil', $currentModel->idPerfil)->where('idModulo', 3)->first()?->toArray() ?? [])
            : []);
@endphp

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-shield-check me-2"></i>Permisos por Perfil</span>
    </div>
    <div class="card-body">
        {{-- Selector de perfil --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Seleccionar Perfil</label>
                <select id="selectPerfil" class="form-select" onchange="loadPermisos()">
                    <option value="">-- Selecciona un perfil --</option>
                    @foreach($perfiles as $perfil)
                        <option value="{{ $perfil->id }}">{{ $perfil->strNombrePerfil }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div id="permisosContainer" class="d-none">
            <div id="formErrors" class="alert alert-danger d-none mb-3"></div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Módulo</th>
                            <th class="text-center">Agregar</th>
                            <th class="text-center">Editar</th>
                            <th class="text-center">Consulta</th>
                            <th class="text-center">Eliminar</th>
                            <th class="text-center">Detalle</th>
                            @if(!empty($permisos['bitEliminar']))
                            <th class="text-center">Eliminar Registro</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="permisosBody"></tbody>
                </table>
            </div>

            @if(!empty($permisos['bitAgregar']) || !empty($permisos['bitEditar']))
            <div class="text-end mt-3">
                <button class="btn btn-primary" onclick="savePermisos()">
                    <i class="bi bi-floppy me-2"></i>Guardar Permisos
                </button>
            </div>
            @endif
        </div>

        <div id="noSeleccion" class="text-center text-muted py-5">
            <i class="bi bi-hand-index" style="font-size:3rem"></i>
            <p class="mt-2">Selecciona un perfil para gestionar sus permisos.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const allModulos = @json($modulos);
const State = {
    permisos: @json($permisos),
    permisosActuales: {},
};

async function loadPermisos() {
    const idPerfil = document.getElementById('selectPerfil').value;
    if (!idPerfil) {
        document.getElementById('permisosContainer').classList.add('d-none');
        document.getElementById('noSeleccion').classList.remove('d-none');
        return;
    }

    document.getElementById('noSeleccion').classList.add('d-none');
    document.getElementById('permisosContainer').classList.remove('d-none');

    const res  = await apiFetch(`{{ route('permiso.list') }}?idPerfil=${idPerfil}`);
    const data = await res.json();

    // Indexar permisos actuales por idModulo
    State.permisosActuales = {};
    data.forEach(p => { State.permisosActuales[p.idModulo] = p; });

    const tbody = document.getElementById('permisosBody');
    const canEdit = State.permisos.bitAgregar || State.permisos.bitEditar;
    const canDel  = State.permisos.bitEliminar;

    tbody.innerHTML = allModulos.map(m => {
        const p = State.permisosActuales[m.id] || {};
        const chk = (name, val) => `<input type="checkbox" class="form-check-input" 
            data-modulo="${m.id}" data-perm="${name}" 
            ${val ? 'checked' : ''} ${!canEdit ? 'disabled' : ''}>`;

        return `<tr>
            <td><strong>${escHtml(m.strNombreModulo)}</strong></td>
            <td class="text-center">${chk('bitAgregar',  p.bitAgregar)}</td>
            <td class="text-center">${chk('bitEditar',   p.bitEditar)}</td>
            <td class="text-center">${chk('bitConsulta', p.bitConsulta)}</td>
            <td class="text-center">${chk('bitEliminar', p.bitEliminar)}</td>
            <td class="text-center">${chk('bitDetalle',  p.bitDetalle)}</td>
            ${canDel ? `<td class="text-center">
                ${p.id ? `<button class="btn btn-danger btn-sm" onclick="deletePermiso(${p.id})">
                    <i class="bi bi-trash"></i></button>` : '—'}
            </td>` : ''}
        </tr>`;
    }).join('');
}

async function savePermisos() {
    const idPerfil = document.getElementById('selectPerfil').value;
    if (!idPerfil) return;

    const rows   = document.querySelectorAll('#permisosBody tr');
    const btnSave = document.querySelector('[onclick="savePermisos()"]');
    if (btnSave) { btnSave.disabled = true; btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...'; }

    let errores = 0;

    for (let idx = 0; idx < rows.length; idx++) {
        const idModulo = allModulos[idx]?.id;
        if (!idModulo) continue;

        const get = name => rows[idx].querySelector(`[data-perm="${name}"]`)?.checked ?? false;

        try {
            const res  = await apiFetch(`{{ route('permiso.store') }}`, {
                method: 'POST',
                body: JSON.stringify({
                    idPerfil,
                    idModulo,
                    bitAgregar:  get('bitAgregar'),
                    bitEditar:   get('bitEditar'),
                    bitConsulta: get('bitConsulta'),
                    bitEliminar: get('bitEliminar'),
                    bitDetalle:  get('bitDetalle'),
                })
            });
            if (!res || !res.ok) errores++;
        } catch {
            errores++;
        }
    }

    if (btnSave) { btnSave.disabled = false; btnSave.innerHTML = '<i class="bi bi-floppy me-2"></i>Guardar Permisos'; }

    if (errores === 0) {
        Notif.show('Permisos guardados correctamente.', 'success');
    } else {
        Notif.show(`Se guardaron con ${errores} error(es). Revisa la consola.`, 'warning');
    }
    loadPermisos();
}

async function deletePermiso(id) {
    if (!confirm('¿Eliminar este registro de permiso?')) return;
    const res  = await apiFetch(`{{ url('seguridad/permisos-perfil') }}/${id}`, { method: 'DELETE' });
    const data = await res.json();
    Notif.show(data.message, data.success ? 'success' : 'danger');
    if (data.success) loadPermisos();
}

const Notif = {
    show(msg, type='success') {
        const id = 'toast-'+Date.now();
        document.body.insertAdjacentHTML('beforeend',
            `<div id="${id}" class="toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3" style="z-index:9999">
                <div class="d-flex"><div class="toast-body fw-semibold">${msg}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div></div>`);
        const el = document.getElementById(id);
        new bootstrap.Toast(el,{delay:3500}).show();
        el.addEventListener('hidden.bs.toast',()=>el.remove());
    }
};

function escHtml(s){return String(s||'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
</script>
@endpush

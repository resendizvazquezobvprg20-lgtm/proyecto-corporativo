{{-- ============================================================ --}}
{{-- ARCHIVO: resources/views/security/perfil.blade.php          --}}
{{-- CRUD completo con Fetch API, paginado 5 filas, DOM objects  --}}
{{-- ============================================================ --}}
@extends('layouts.app')

@section('title', 'Perfiles')
@section('page-title', 'Gestión de Perfiles')

@push('styles')
<style>
    .action-btn { font-size: .8rem; padding: 4px 10px; }
    .badge-admin { background: #2979ff; }
    .badge-normal { background: #6b7280; }
    .table-actions { white-space: nowrap; }
    /* Ocultar botones según permisos se maneja con JS */
    [data-perm].d-none { display: none !important; }
</style>
@endpush

@section('content')

{{-- Permisos del usuario para este módulo (inyectados desde blade) --}}
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
            ? (PermisoPerfil::where('idPerfil', $currentModel->idPerfil)->where('idModulo', 1)->first()?->toArray() ?? [])
            : []);
@endphp

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people me-2"></i>Perfiles del Sistema</span>
        @if(!empty($permisos['bitAgregar']))
        <button class="btn btn-sm btn-light fw-semibold" onclick="UI.openModal('create')">
            <i class="bi bi-plus-lg me-1"></i>Nuevo Perfil
        </button>
        @endif
    </div>

    <div class="card-body">
        {{-- Buscador --}}
        @if(!empty($permisos['bitConsulta']))
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" id="searchInput" class="form-control form-control-sm"
                           placeholder="Buscar perfil..." oninput="debounceSearch()">
                    <button class="btn btn-outline-secondary btn-sm" onclick="Table.load(1)">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- Tabla --}}
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle" id="perfilTable">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Nombre del Perfil</th>
                        <th style="width:120px">Administrador</th>
                        <th style="width:160px">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr><td colspan="4" class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-primary"></div>
                        Cargando...
                    </td></tr>
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div id="paginationContainer" class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted" id="paginationInfo"></small>
            <nav><ul class="pagination pagination-sm mb-0" id="paginationLinks"></ul></nav>
        </div>
    </div>
</div>

{{-- ── Modal Crear / Editar ─────────────────────────── --}}
<div class="modal fade" id="perfilModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalTitle">Nuevo Perfil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="formErrors" class="alert alert-danger d-none"></div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre del Perfil <span class="text-danger">*</span></label>
                    <input type="text" id="strNombrePerfil" class="form-control"
                           placeholder="Ej: Supervisor" maxlength="100">
                    <div class="invalid-feedback" id="errNombre">El nombre es obligatorio.</div>
                </div>
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="bitAdministrador" role="switch">
                        <label class="form-check-label fw-semibold" for="bitAdministrador">
                            ¿Es Administrador?
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="Form.submit()" id="btnSave">
                    <i class="bi bi-floppy me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Modal Detalle ────────────────────────────────── --}}
<div class="modal fade" id="detalleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Detalle del Perfil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleContent">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Estado global ─────────────────────────────────────────
const State = {
    currentPage: 1,
    editingId:   null,
    permisos: @json($permisos),
};

// ── Tabla + Paginación ────────────────────────────────────
const Table = {
    async load(page = 1) {
        State.currentPage = page;
        const search = document.getElementById('searchInput')?.value ?? '';
        const tbody  = document.getElementById('tableBody');

        tbody.innerHTML = `<tr><td colspan="4" class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-primary me-2"></div>Cargando...
        </td></tr>`;

        try {
            const res  = await apiFetch(`{{ route('perfil.list') }}?page=${page}&search=${encodeURIComponent(search)}`);
            const data = await res.json();
            Table.render(data);
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-danger py-3">
                <i class="bi bi-exclamation-triangle me-2"></i>Error al cargar datos.
            </td></tr>`;
        }
    },

    render(data) {
        const tbody = document.getElementById('tableBody');

        if (!data.data || data.data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4">
                <i class="bi bi-inbox me-2"></i>No hay perfiles registrados.
            </td></tr>`;
        } else {
            tbody.innerHTML = data.data.map((p, i) => `
                <tr>
                    <td>${(data.from || 0) + i}</td>
                    <td>${escHtml(p.strNombrePerfil)}</td>
                    <td class="text-center">
                        <span class="badge ${p.bitAdministrador ? 'badge-admin' : 'badge-normal'}">
                            ${p.bitAdministrador ? 'Sí' : 'No'}
                        </span>
                    </td>
                    <td class="table-actions">
                        ${State.permisos.bitDetalle  ? `<button class="btn btn-info btn-sm action-btn me-1" onclick="UI.showDetail(${p.id})"><i class="bi bi-eye"></i></button>` : ''}
                        ${State.permisos.bitEditar   ? `<button class="btn btn-warning btn-sm action-btn me-1" onclick="UI.openModal('edit', ${p.id})"><i class="bi bi-pencil"></i></button>` : ''}
                        ${State.permisos.bitEliminar ? `<button class="btn btn-danger btn-sm action-btn" onclick="Actions.delete(${p.id}, '${escHtml(p.strNombrePerfil)}')"><i class="bi bi-trash"></i></button>` : ''}
                    </td>
                </tr>
            `).join('');
        }

        // Info de paginación
        document.getElementById('paginationInfo').textContent =
            `Mostrando ${data.from || 0}–${data.to || 0} de ${data.total || 0} registros`;

        // Links de paginación
        Table.renderPagination(data);
    },

    renderPagination(data) {
        const container = document.getElementById('paginationLinks');
        const { current_page, last_page } = data;
        let html = '';

        html += `<li class="page-item ${current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="Table.load(${current_page - 1})">‹</a></li>`;

        for (let p = 1; p <= last_page; p++) {
            html += `<li class="page-item ${p === current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="Table.load(${p})">${p}</a></li>`;
        }

        html += `<li class="page-item ${current_page === last_page ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="Table.load(${current_page + 1})">›</a></li>`;

        container.innerHTML = html;
    }
};

// ── UI (modales) ──────────────────────────────────────────
const UI = {
    modal: null,

    openModal(mode, id = null) {
        State.editingId = id;
        const title  = document.getElementById('modalTitle');
        const nombre = document.getElementById('strNombrePerfil');
        const admin  = document.getElementById('bitAdministrador');
        const errors = document.getElementById('formErrors');

        // Reset
        nombre.value = '';
        admin.checked = false;
        nombre.classList.remove('is-invalid');
        errors.classList.add('d-none');

        if (mode === 'create') {
            title.textContent = 'Nuevo Perfil';
        } else {
            title.textContent = 'Editar Perfil';
            Actions.loadForEdit(id);
        }

        UI.modal = new bootstrap.Modal(document.getElementById('perfilModal'));
        UI.modal.show();
    },

    async showDetail(id) {
        const content = document.getElementById('detalleContent');
        content.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>';

        const modal = new bootstrap.Modal(document.getElementById('detalleModal'));
        modal.show();

        try {
            const res = await apiFetch(`{{ url('seguridad/perfil') }}/${id}`);
            const p   = await res.json();
            content.innerHTML = `
                <table class="table table-sm">
                    <tr><th>ID</th><td>${p.id}</td></tr>
                    <tr><th>Nombre</th><td>${escHtml(p.strNombrePerfil)}</td></tr>
                    <tr><th>Administrador</th><td>${p.bitAdministrador ? '<span class="badge bg-primary">Sí</span>' : '<span class="badge bg-secondary">No</span>'}</td></tr>
                    <tr><th>Creado</th><td>${p.created_at?.substring(0,10) || '—'}</td></tr>
                    <tr><th>Actualizado</th><td>${p.updated_at?.substring(0,10) || '—'}</td></tr>
                </table>`;
        } catch {
            content.innerHTML = '<p class="text-danger text-center">Error al cargar detalle.</p>';
        }
    }
};

// ── Acciones CRUD ─────────────────────────────────────────
const Actions = {
    async loadForEdit(id) {
        try {
            const res = await apiFetch(`{{ url('seguridad/perfil') }}/${id}`);
            const p   = await res.json();
            document.getElementById('strNombrePerfil').value = p.strNombrePerfil;
            document.getElementById('bitAdministrador').checked = p.bitAdministrador;
        } catch {
            alert('Error al cargar datos del perfil.');
        }
    },

    async delete(id, nombre) {
        if (!confirm(`¿Eliminar el perfil "${nombre}"?\nEsta acción no se puede deshacer.`)) return;

        try {
            const res  = await apiFetch(`{{ url('seguridad/perfil') }}/${id}`, { method: 'DELETE' });
            const data = await res.json();

            if (data.success) {
                Notif.show(data.message, 'success');
                Table.load(State.currentPage);
            } else {
                Notif.show(data.message, 'danger');
            }
        } catch {
            Notif.show('Error al eliminar el perfil.', 'danger');
        }
    }
};

// ── Formulario ────────────────────────────────────────────
const Form = {
    validate() {
        const nombre = document.getElementById('strNombrePerfil');
        let valid = true;

        if (!nombre.value.trim()) {
            nombre.classList.add('is-invalid');
            valid = false;
        } else {
            nombre.classList.remove('is-invalid');
        }

        return valid;
    },

    async submit() {
        if (!Form.validate()) return;

        const id    = State.editingId;
        const body  = {
            strNombrePerfil:  document.getElementById('strNombrePerfil').value.trim(),
            bitAdministrador: document.getElementById('bitAdministrador').checked,
        };

        const url    = id ? `{{ url('seguridad/perfil') }}/${id}` : `{{ route('perfil.store') }}`;
        const method = id ? 'PUT' : 'POST';

        document.getElementById('btnSave').disabled = true;

        try {
            const res  = await apiFetch(url, { method, body: JSON.stringify(body) });
            const data = await res.json();

            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('perfilModal')).hide();
                Notif.show(data.message, 'success');
                Table.load(State.currentPage);
            } else {
                // Mostrar errores de validación Laravel
                const errDiv = document.getElementById('formErrors');
                errDiv.innerHTML = Object.values(data.errors || {data: [data.message]}).flat().join('<br>');
                errDiv.classList.remove('d-none');
            }
        } catch {
            Notif.show('Error al guardar el perfil.', 'danger');
        } finally {
            document.getElementById('btnSave').disabled = false;
        }
    }
};

// ── Notificaciones Toast ──────────────────────────────────
const Notif = {
    show(message, type = 'success') {
        const id   = 'toast-' + Date.now();
        const html = `
            <div id="${id}" class="toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3"
                 role="alert" style="z-index:9999">
                <div class="d-flex">
                    <div class="toast-body fw-semibold">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`;
        document.body.insertAdjacentHTML('beforeend', html);
        const el = document.getElementById(id);
        new bootstrap.Toast(el, { delay: 3500 }).show();
        el.addEventListener('hidden.bs.toast', () => el.remove());
    }
};

// ── Utilidades ────────────────────────────────────────────
function escHtml(str) {
    return String(str).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

let searchTimer;
function debounceSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => Table.load(1), 400);
}

// ── Init ──────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    if (State.permisos.bitConsulta) {
        Table.load(1);
    } else {
        document.getElementById('tableBody').innerHTML =
            '<tr><td colspan="4" class="text-center text-muted py-4"><i class="bi bi-lock me-2"></i>Sin permisos de consulta.</td></tr>';
    }
});
</script>
@endpush

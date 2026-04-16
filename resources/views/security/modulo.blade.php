{{-- ============================================================ --}}
{{-- ARCHIVO: resources/views/security/modulo.blade.php         --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'Módulos')
@section('page-title', 'Gestión de Módulos')

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-puzzle me-2"></i>Módulos del Sistema</span>
        <button id="btnNuevoModulo" class="btn d-none btn-sm btn-light fw-semibold"
                onclick="window.location.href='/seguridad/modulo/create'">
            <i class="bi bi-plus-lg me-1"></i>Nuevo Módulo
        </button>
    </div>
    <div class="card-body">

        <div class="row mb-3">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" id="searchInput" class="form-control form-control-sm"
                           placeholder="Buscar módulo..." oninput="debounceSearch()">
                    <button class="btn btn-outline-secondary btn-sm" onclick="Table.load(1)">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Nombre del Módulo</th>
                        <th style="width:160px">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr><td colspan="3" class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-primary"></div> Cargando...
                    </td></tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted" id="paginationInfo"></small>
            <nav><ul class="pagination pagination-sm mb-0" id="paginationLinks"></ul></nav>
        </div>
    </div>
</div>

{{-- Modal Crear / Editar --}}
<div class="modal fade" id="moduloModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalTitle">Nuevo Módulo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="formErrors" class="alert alert-danger d-none"></div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre del Módulo <span class="text-danger">*</span></label>
                    <input type="text" id="strNombreModulo" class="form-control"
                           placeholder="Ej: Reportes" maxlength="100">
                    <div class="invalid-feedback">El nombre es obligatorio.</div>
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

{{-- Modal Detalle --}}
<div class="modal fade" id="detalleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Detalle del Módulo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleContent"></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const State = {
    currentPage: 1,
    editingId: null,
    permisos: { bitAgregar:false, bitEditar:false, bitEliminar:false, bitConsulta:false, bitDetalle:false }
};

(async () => {
    try {
        const res = await apiFetch('/api/menu');
        if (!res || !res.ok) return;
        const menus = await res.json();
        for (const menu of menus) {
            const sub = menu.submenus.find(s => s.id === 2);
            if (sub) { State.permisos = sub; break; }
        }
    } catch(e) { console.error('Error cargando permisos:', e); }

    if (State.permisos.bitAgregar) document.getElementById('btnNuevoModulo')?.classList.remove('d-none');
    Table.load(1);
})();

const Table = {
    async load(page = 1) {
        State.currentPage = page;
        const s = document.getElementById('searchInput')?.value ?? '';
        document.getElementById('tableBody').innerHTML =
            `<tr><td colspan="3" class="text-center py-4">
                <div class="spinner-border spinner-border-sm text-primary"></div> Cargando...
             </td></tr>`;
        try {
            const res  = await apiFetch(`{{ url('api/modulo') }}?page=${page}&search=${encodeURIComponent(s)}`);
            if (!res) return;
            const data = await res.json();
            Table.render(data);
        } catch(e) {
            document.getElementById('tableBody').innerHTML =
                `<tr><td colspan="3" class="text-center text-danger py-4"><i class="bi bi-exclamation-triangle me-2"></i>Error al cargar datos.</td></tr>`;
        }
    },

    render(data) {
        const tbody = document.getElementById('tableBody');
        if (!data.data?.length) {
            tbody.innerHTML = `<tr><td colspan="3" class="text-center text-muted py-4">
                <i class="bi bi-inbox me-2"></i>No hay módulos registrados.</td></tr>`;
        } else {
            tbody.innerHTML = data.data.map((m, i) => `<tr>
                <td>${(data.from || 0) + i}</td>
                <td>${escHtml(m.strNombreModulo)}</td>
                <td>
                    ${State.permisos.bitDetalle  ? `<button class="btn btn-info btn-sm me-1" onclick="UI.showDetail(${m.id})"><i class="bi bi-eye"></i></button>` : ''}
                    ${State.permisos.bitEditar   ? `<button class="btn btn-warning btn-sm me-1" onclick="window.location.href='/seguridad/modulo/'+m.id+'/edit'"><i class="bi bi-pencil"></i></button>` : ''}
                    ${State.permisos.bitEliminar ? `<button class="btn btn-danger btn-sm" onclick="Actions.delete(${m.id},'${escHtml(m.strNombreModulo)}')"><i class="bi bi-trash"></i></button>` : ''}
                </td>
            </tr>`).join('');
        }
        document.getElementById('paginationInfo').textContent =
            `Mostrando ${data.from || 0}–${data.to || 0} de ${data.total || 0} registros`;
        let html = `<li class="page-item ${data.current_page===1?'disabled':''}">
            <a class="page-link" href="#" onclick="event.preventDefault();Table.load(${data.current_page-1})">‹</a></li>`;
        for (let i = 1; i <= data.last_page; i++) {
            html += `<li class="page-item ${i===data.current_page?'active':''}">
                <a class="page-link" href="#" onclick="event.preventDefault();Table.load(${i})">${i}</a></li>`;
        }
        html += `<li class="page-item ${data.current_page===data.last_page?'disabled':''}">
            <a class="page-link" href="#" onclick="event.preventDefault();Table.load(${data.current_page+1})">›</a></li>`;
        document.getElementById('paginationLinks').innerHTML = html;
    }
};

const UI = {
    openModal(mode, id = null) {
        State.editingId = id;
        const el = document.getElementById('strNombreModulo');
        el.value = ''; el.classList.remove('is-invalid');
        document.getElementById('formErrors').classList.add('d-none');
        document.getElementById('modalTitle').textContent = mode === 'create' ? 'Nuevo Módulo' : 'Editar Módulo';
        if (mode === 'edit') Actions.loadForEdit(id);
        new bootstrap.Modal(document.getElementById('moduloModal')).show();
    },

    async showDetail(id) {
        const c = document.getElementById('detalleContent');
        c.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>';
        new bootstrap.Modal(document.getElementById('detalleModal')).show();
        try {
            const res = await apiFetch(`{{ url('api/modulo') }}/${id}`);
            if (!res) return;
            const m = await res.json();
            c.innerHTML = `<table class="table table-sm">
                <tr><th style="width:120px">ID</th><td>${m.id}</td></tr>
                <tr><th>Nombre</th><td>${escHtml(m.strNombreModulo)}</td></tr>
                <tr><th>Creado</th><td>${m.created_at?.substring(0,10) || '—'}</td></tr>
                <tr><th>Actualizado</th><td>${m.updated_at?.substring(0,10) || '—'}</td></tr>
            </table>`;
        } catch(e) {
            c.innerHTML = '<p class="text-danger">Error al cargar el detalle.</p>';
        }
    }
};

const Actions = {
    async loadForEdit(id) {
        try {
            const res = await apiFetch(`{{ url('api/modulo') }}/${id}`);
            if (!res) return;
            const m = await res.json();
            document.getElementById('strNombreModulo').value = m.strNombreModulo;
        } catch(e) {}
    },

    async delete(id, nombre) {
        if (!confirm(`¿Eliminar el módulo "${nombre}"?`)) return;
        try {
            const res  = await apiFetch(`{{ url('api/modulo') }}/${id}`, { method: 'DELETE' });
            if (!res) return;
            const data = await res.json();
            showToast(data.message, data.success ? 'success' : 'danger');
            if (data.success) Table.load(State.currentPage);
        } catch(e) {
            showToast('Error al eliminar.', 'danger');
        }
    }
};

const Form = {
    async submit() {
        const el = document.getElementById('strNombreModulo');
        if (!el.value.trim()) { el.classList.add('is-invalid'); return; }
        el.classList.remove('is-invalid');
        document.getElementById('formErrors').classList.add('d-none');

        const id  = State.editingId;
        const url = id ? `{{ url('api/modulo') }}/${id}` : `{{ url('api/modulo') }}`;
        document.getElementById('btnSave').disabled = true;

        try {
            const res  = await apiFetch(url, {
                method: id ? 'PUT' : 'POST',
                body: JSON.stringify({ strNombreModulo: el.value.trim() })
            });
            if (!res) return;
            const data = await res.json();
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('moduloModal')).hide();
                showToast(data.message, 'success');
                Table.load(State.currentPage);
            } else {
                const errDiv = document.getElementById('formErrors');
                errDiv.innerHTML = Object.values(data.errors || {}).flat().join('<br>') || data.message;
                errDiv.classList.remove('d-none');
            }
        } catch(e) {
            showToast('Error al guardar.', 'danger');
        } finally {
            document.getElementById('btnSave').disabled = false;
        }
    }
};

let st;
function debounceSearch() { clearTimeout(st); st = setTimeout(() => Table.load(1), 400); }
</script>
@endpush

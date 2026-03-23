{{-- ============================================================ --}}
{{-- ARCHIVO: resources/views/security/modulo.blade.php         --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'Módulos')
@section('page-title', 'Gestión de Módulos')

@section('content')
@php
    use Tymon\JWTAuth\Facades\JWTAuth;
    use App\Models\{PermisoPerfil, Usuario};

    try {
        $cu = JWTAuth::parseToken()->authenticate();
        $um = Usuario::with('perfil')->find($cu->id);
        $ea = $um?->perfil?->bitAdministrador ?? false;
    } catch (\Exception $e) {
        $um = null;
        $ea = false;
    }

    $p = $ea
        ? ['bitAgregar'=>true,'bitEditar'=>true,'bitEliminar'=>true,'bitConsulta'=>true,'bitDetalle'=>true]
        : ($um
            ? (PermisoPerfil::where('idPerfil',$um->idPerfil)->where('idModulo',2)->first()?->toArray() ?? [])
            : []);
@endphp

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-puzzle me-2"></i>Módulos del Sistema</span>
        @if(!empty($p['bitAgregar']))
        <button class="btn btn-sm btn-light fw-semibold" onclick="UI.openModal('create')">
            <i class="bi bi-plus-lg me-1"></i>Nuevo Módulo
        </button>
        @endif
    </div>
    <div class="card-body">
        @if(!empty($p['bitConsulta']))
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
        @endif

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

{{-- Modal --}}
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
                    <input type="text" id="strNombreModulo" class="form-control" placeholder="Ej: Reportes" maxlength="100">
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
                <h5 class="modal-title">Detalle del Módulo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleContent"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const State = { currentPage:1, editingId:null, permisos: @json($p) };

const Table = {
    async load(page=1) {
        State.currentPage = page;
        const s = document.getElementById('searchInput')?.value ?? '';
        document.getElementById('tableBody').innerHTML =
            `<tr><td colspan="3" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Cargando...</td></tr>`;
        const res  = await apiFetch(`{{ route('modulo.list') }}?page=${page}&search=${encodeURIComponent(s)}`);
        const data = await res.json();
        Table.render(data);
    },
    render(data) {
        const tbody = document.getElementById('tableBody');
        if (!data.data?.length) {
            tbody.innerHTML = `<tr><td colspan="3" class="text-center text-muted py-4"><i class="bi bi-inbox me-2"></i>No hay módulos registrados.</td></tr>`;
        } else {
            tbody.innerHTML = data.data.map((m,i) => `<tr>
                <td>${(data.from||0)+i}</td>
                <td>${escHtml(m.strNombreModulo)}</td>
                <td>
                    ${State.permisos.bitDetalle  ? `<button class="btn btn-info btn-sm me-1" onclick="UI.showDetail(${m.id})"><i class="bi bi-eye"></i></button>` : ''}
                    ${State.permisos.bitEditar   ? `<button class="btn btn-warning btn-sm me-1" onclick="UI.openModal('edit',${m.id})"><i class="bi bi-pencil"></i></button>` : ''}
                    ${State.permisos.bitEliminar ? `<button class="btn btn-danger btn-sm" onclick="Actions.delete(${m.id},'${escHtml(m.strNombreModulo)}')"><i class="bi bi-trash"></i></button>` : ''}
                </td>
            </tr>`).join('');
        }
        document.getElementById('paginationInfo').textContent = `Mostrando ${data.from||0}–${data.to||0} de ${data.total||0} registros`;
        let html = `<li class="page-item ${data.current_page===1?'disabled':''}"><a class="page-link" href="#" onclick="Table.load(${data.current_page-1})">‹</a></li>`;
        for(let i=1;i<=data.last_page;i++) html+=`<li class="page-item ${i===data.current_page?'active':''}"><a class="page-link" href="#" onclick="Table.load(${i})">${i}</a></li>`;
        html+=`<li class="page-item ${data.current_page===data.last_page?'disabled':''}"><a class="page-link" href="#" onclick="Table.load(${data.current_page+1})">›</a></li>`;
        document.getElementById('paginationLinks').innerHTML = html;
    }
};

const UI = {
    openModal(mode, id=null) {
        State.editingId = id;
        const el = document.getElementById('strNombreModulo');
        el.value=''; el.classList.remove('is-invalid');
        document.getElementById('formErrors').classList.add('d-none');
        document.getElementById('modalTitle').textContent = mode==='create' ? 'Nuevo Módulo' : 'Editar Módulo';
        if(mode==='edit') Actions.loadForEdit(id);
        new bootstrap.Modal(document.getElementById('moduloModal')).show();
    },
    async showDetail(id) {
        const c = document.getElementById('detalleContent');
        c.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>';
        new bootstrap.Modal(document.getElementById('detalleModal')).show();
        const res = await apiFetch(`{{ url('seguridad/modulo') }}/${id}`);
        const m   = await res.json();
        c.innerHTML = `<table class="table table-sm">
            <tr><th>ID</th><td>${m.id}</td></tr>
            <tr><th>Nombre</th><td>${escHtml(m.strNombreModulo)}</td></tr>
            <tr><th>Creado</th><td>${m.created_at?.substring(0,10)||'—'}</td></tr>
        </table>`;
    }
};

const Actions = {
    async loadForEdit(id) {
        const res = await apiFetch(`{{ url('seguridad/modulo') }}/${id}`);
        const m   = await res.json();
        document.getElementById('strNombreModulo').value = m.strNombreModulo;
    },
    async delete(id, nombre) {
        if(!confirm(`¿Eliminar el módulo "${nombre}"?`)) return;
        const res  = await apiFetch(`{{ url('seguridad/modulo') }}/${id}`, {method:'DELETE'});
        const data = await res.json();
        Notif.show(data.message, data.success?'success':'danger');
        if(data.success) Table.load(State.currentPage);
    }
};

const Form = {
    async submit() {
        const el = document.getElementById('strNombreModulo');
        if(!el.value.trim()){ el.classList.add('is-invalid'); return; }
        el.classList.remove('is-invalid');
        const id = State.editingId;
        const url = id ? `{{ url('seguridad/modulo') }}/${id}` : `{{ route('modulo.store') }}`;
        document.getElementById('btnSave').disabled = true;
        const res  = await apiFetch(url, {method: id?'PUT':'POST', body: JSON.stringify({strNombreModulo: el.value.trim()})});
        const data = await res.json();
        document.getElementById('btnSave').disabled = false;
        if(data.success){ bootstrap.Modal.getInstance(document.getElementById('moduloModal')).hide(); Notif.show(data.message,'success'); Table.load(State.currentPage); }
        else { const e=document.getElementById('formErrors'); e.innerHTML=Object.values(data.errors||{}).flat().join('<br>'); e.classList.remove('d-none'); }
    }
};

const Notif={show(msg,type='success'){const id='toast-'+Date.now();document.body.insertAdjacentHTML('beforeend',`<div id="${id}" class="toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3" style="z-index:9999"><div class="d-flex"><div class="toast-body fw-semibold">${msg}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`);const el=document.getElementById(id);new bootstrap.Toast(el,{delay:3500}).show();el.addEventListener('hidden.bs.toast',()=>el.remove());}};
function escHtml(s){return String(s||'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
let st; function debounceSearch(){clearTimeout(st);st=setTimeout(()=>Table.load(1),400);}

document.addEventListener('DOMContentLoaded', () => { if(State.permisos.bitConsulta) Table.load(1); });
</script>
@endpush

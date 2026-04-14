{{-- ============================================================ --}}
{{-- ARCHIVO: resources/views/security/usuario.blade.php        --}}
{{-- CRUD completo con subida de imagen                         --}}
{{-- ============================================================ --}}
@extends('layouts.app')

@section('title', 'Usuarios')
@section('page-title', 'Gestión de Usuarios')

@push('styles')
<style>
    .avatar-preview {
        width: 80px; height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #2979ff;
    }
    .avatar-default {
        width: 80px; height: 80px;
        border-radius: 50%;
        background: #1a2a4a;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 2rem; font-weight: 700;
        border: 3px solid #2979ff;
    }
    .badge-activo   { background:#16a34a; }
    .badge-inactivo { background:#dc2626; }
</style>
@endpush

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-person-badge me-2"></i>Usuarios del Sistema</span>
        <button id="btnNuevoUsuario" class="btn btn-sm btn-light fw-semibold d-none" onclick="window.location.href='/seguridad/usuario/create'">
            <i class="bi bi-plus-lg me-1"></i>Nuevo Usuario
        </button>
    </div>

    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" id="searchInput" class="form-control form-control-sm"
                           placeholder="Buscar por nombre o correo..." oninput="debounceSearch()">
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
                        <th style="width:50px">#</th>
                        <th style="width:70px">Foto</th>
                        <th>Usuario</th>
                        <th>Perfil</th>
                        <th>Correo</th>
                        <th style="width:100px">Estado</th>
                        <th style="width:160px">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr><td colspan="7" class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-primary"></div> Cargando...
                    </td></tr>
                </tbody>
            </table>
        </div>

        <div id="paginationContainer" class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted" id="paginationInfo"></small>
            <nav><ul class="pagination pagination-sm mb-0" id="paginationLinks"></ul></nav>
        </div>
    </div>
</div>

{{-- ── Modal Crear / Editar ──────────────────────── --}}
<div class="modal fade" id="usuarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalTitle">Nuevo Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="formErrors" class="alert alert-danger d-none"></div>

                <div class="row g-3">
                    {{-- Imagen --}}
                    <div class="col-12 text-center">
                        <div id="avatarPreview" class="avatar-default mx-auto mb-2">U</div>
                        <label class="btn btn-outline-secondary btn-sm" for="imgInput">
                            <i class="bi bi-camera me-1"></i>Cambiar Foto
                        </label>
                        <input type="file" id="imgInput" accept="image/jpg,image/jpeg,image/png"
                               class="d-none" onchange="previewImg(this)">
                        <small class="d-block text-muted mt-1">JPG/PNG, máx. 2MB</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre de Usuario <span class="text-danger">*</span></label>
                        <input type="text" id="strNombreUsuario" class="form-control" maxlength="100" placeholder="usuario123">
                        <div class="invalid-feedback">Campo obligatorio.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" id="strCorreo" class="form-control" maxlength="150" placeholder="correo@ejemplo.com">
                        <div class="invalid-feedback">Ingresa un correo válido.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contraseña <span class="text-danger" id="pwdRequired">*</span></label>
                        <input type="password" id="strPwd" class="form-control" placeholder="Mínimo 8 caracteres">
                        <div class="invalid-feedback">Mínimo 8 caracteres.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Confirmar Contraseña <span class="text-danger" id="pwdConfRequired">*</span></label>
                        <input type="password" id="strPwdConfirmation" class="form-control" placeholder="Repetir contraseña">
                        <div class="invalid-feedback">Las contraseñas no coinciden.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Perfil <span class="text-danger">*</span></label>
                        <select id="idPerfil" class="form-select">
                            <option value="">-- Selecciona --</option>
                            @foreach($perfiles as $perfil)
                                <option value="{{ $perfil->id }}">{{ $perfil->strNombrePerfil }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Selecciona un perfil.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
                        <select id="idEstadoUsuario" class="form-select">
                            @foreach($estados as $estado)
                                <option value="{{ $estado->id }}">{{ $estado->strNombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Número Celular</label>
                        <input type="text" id="strNumeroCelular" class="form-control" maxlength="20" placeholder="55 1234 5678">
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

{{-- Modal Detalle --}}
<div class="modal fade" id="detalleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Detalle del Usuario</h5>
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
    editingId:   null,
    permisos: {bitAgregar:false,bitEditar:false,bitEliminar:false,bitConsulta:false,bitDetalle:false},
    imgFile:     null,
};

(async () => {
    try {
        const res = await apiFetch('/api/menu');
        if (!res || !res.ok) return;
        const menus = await res.json();
        for (const menu of menus) {
            const sub = menu.submenus.find(s => s.id === 4);
            if (sub) { State.permisos = sub; break; }
        }
    } catch(e) {}
    if (State.permisos.bitAgregar) document.getElementById('btnNuevoUsuario')?.classList.remove('d-none');
    Table.load(1);
})();


const Table = {
    async load(page = 1) {
        State.currentPage = page;
        const search = document.getElementById('searchInput')?.value ?? '';
        const tbody  = document.getElementById('tableBody');
        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-primary me-2"></div>Cargando...</td></tr>`;

        const res  = await apiFetch(`{{ url('api/usuario') }}?page=${page}&search=${encodeURIComponent(search)}`);
        const data = await res.json();
        Table.render(data);
    },

    render(data) {
        const tbody = document.getElementById('tableBody');
        if (!data.data?.length) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-4">
                <i class="bi bi-inbox me-2"></i>Sin usuarios registrados.</td></tr>`;
            return;
        }
        tbody.innerHTML = data.data.map((u, i) => `
            <tr>
                <td>${(data.from || 0) + i}</td>
                <td class="text-center">
                    ${u.strImagen
                        ? `<img src="/storage/${u.strImagen}" class="avatar-preview" style="width:40px;height:40px">`
                        : `<div class="avatar-default mx-auto" style="width:40px;height:40px;font-size:1rem">${u.strNombreUsuario[0].toUpperCase()}</div>`
                    }
                </td>
                <td><strong>${escHtml(u.strNombreUsuario)}</strong></td>
                <td>${escHtml(u.perfil?.strNombrePerfil ?? '—')}</td>
                <td>${escHtml(u.strCorreo)}</td>
                <td class="text-center">
                    <span class="badge ${u.idEstadoUsuario === 1 ? 'badge-activo' : 'badge-inactivo'}">
                        ${u.estado_usuario?.strNombre ?? '—'}
                    </span>
                </td>
                <td class="table-actions">
                    ${State.permisos.bitDetalle  ? `<button class="btn btn-info btn-sm me-1" onclick="UI.showDetail(${u.id})"><i class="bi bi-eye"></i></button>` : ''}
                  ${State.permisos.bitEditar ? `<button class="btn btn-warning btn-sm me-1" onclick="window.location.href='/seguridad/usuario/${u.id}/edit'"><i class="bi bi-pencil"></i></button>` : ''}
                    ${State.permisos.bitEliminar ? `<button class="btn btn-danger btn-sm" onclick="Actions.delete(${u.id},'${escHtml(u.strNombreUsuario)}')"><i class="bi bi-trash"></i></button>` : ''}
                </td>
            </tr>`).join('');

        document.getElementById('paginationInfo').textContent =
            `Mostrando ${data.from || 0}–${data.to || 0} de ${data.total || 0} registros`;
        Table.renderPagination(data);
    },

    renderPagination(data) {
        const { current_page, last_page } = data;
        let html = `<li class="page-item ${current_page===1?'disabled':''}">
            <a class="page-link" href="#" onclick="Table.load(${current_page-1})">‹</a></li>`;
        for (let p = 1; p <= last_page; p++) {
            html += `<li class="page-item ${p===current_page?'active':''}">
                <a class="page-link" href="#" onclick="Table.load(${p})">${p}</a></li>`;
        }
        html += `<li class="page-item ${current_page===last_page?'disabled':''}">
            <a class="page-link" href="#" onclick="Table.load(${current_page+1})">›</a></li>`;
        document.getElementById('paginationLinks').innerHTML = html;
    }
};

const UI = {
    openModal(mode, id = null) {
        State.editingId = id;
        State.imgFile   = null;
        ['strNombreUsuario','strCorreo','strPwd','strPwdConfirmation','strNumeroCelular']
            .forEach(f => { const el = document.getElementById(f); if(el) { el.value=''; el.classList.remove('is-invalid'); } });
        document.getElementById('idPerfil').value = '';
        document.getElementById('idEstadoUsuario').value = 1;
        document.getElementById('avatarPreview').className = 'avatar-default mx-auto mb-2';
        document.getElementById('avatarPreview').innerHTML = 'U';
        document.getElementById('formErrors').classList.add('d-none');
        document.getElementById('modalTitle').textContent = mode === 'create' ? 'Nuevo Usuario' : 'Editar Usuario';

        if (mode === 'edit') Actions.loadForEdit(id);

        new bootstrap.Modal(document.getElementById('usuarioModal')).show();
    },

    async showDetail(id) {
        const content = document.getElementById('detalleContent');
        content.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>';
        new bootstrap.Modal(document.getElementById('detalleModal')).show();

        const res = await apiFetch(`{{ url('api/usuario') }}/${id}`);
        const u   = await res.json();

        const foto = u.strImagen
            ? `<img src="/storage/${u.strImagen}" class="avatar-preview" style="width:80px;height:80px">`
            : `<div class="avatar-default mx-auto" style="width:80px;height:80px;font-size:2rem">${u.strNombreUsuario[0].toUpperCase()}</div>`;

        content.innerHTML = `
            <div class="text-center mb-3">${foto}</div>
            <table class="table table-sm">
                <tr><th>ID</th><td>${u.id}</td></tr>
                <tr><th>Usuario</th><td>${escHtml(u.strNombreUsuario)}</td></tr>
                <tr><th>Perfil</th><td>${escHtml(u.perfil?.strNombrePerfil ?? '—')}</td></tr>
                <tr><th>Correo</th><td>${escHtml(u.strCorreo)}</td></tr>
                <tr><th>Celular</th><td>${escHtml(u.strNumeroCelular ?? '—')}</td></tr>
                <tr><th>Estado</th><td>${u.estado_usuario?.strNombre ?? '—'}</td></tr>
                <tr><th>Creado</th><td>${u.created_at?.substring(0,10) ?? '—'}</td></tr>
            </table>`;
    }
};

const Actions = {
    async loadForEdit(id) {
        const res = await apiFetch(`{{ url('api/usuario') }}/${id}`);
        const u   = await res.json();
        document.getElementById('strNombreUsuario').value   = u.strNombreUsuario;
        document.getElementById('strCorreo').value          = u.strCorreo;
        document.getElementById('strNumeroCelular').value   = u.strNumeroCelular ?? '';
        document.getElementById('idPerfil').value           = u.idPerfil;
        document.getElementById('idEstadoUsuario').value    = u.idEstadoUsuario;

        if (u.strImagen) {
            const prev = document.getElementById('avatarPreview');
            prev.className = 'avatar-preview mx-auto mb-2';
            prev.outerHTML = `<img id="avatarPreview" src="/storage/${u.strImagen}" class="avatar-preview mx-auto mb-2" style="display:block">`;
        }
    },

    async delete(id, nombre) {
        if (!confirm(`¿Eliminar el usuario "${nombre}"?`)) return;
        const res  = await apiFetch(`{{ url('api/usuario') }}/${id}`, { method: 'DELETE' });
        const data = await res.json();
        Notif.show(data.message, data.success ? 'success' : 'danger');
        if (data.success) Table.load(State.currentPage);
    }
};

const Form = {
    validate() {
        let ok = true;
        const nombre = document.getElementById('strNombreUsuario');
        const correo = document.getElementById('strCorreo');
        const pwd    = document.getElementById('strPwd');
        const pwdC   = document.getElementById('strPwdConfirmation');
        const perfil = document.getElementById('idPerfil');

        if (!nombre.value.trim()) { nombre.classList.add('is-invalid'); ok = false; } else nombre.classList.remove('is-invalid');
        if (!correo.value.includes('@')) { correo.classList.add('is-invalid'); ok = false; } else correo.classList.remove('is-invalid');
        if (!perfil.value) { perfil.classList.add('is-invalid'); ok = false; } else perfil.classList.remove('is-invalid');

        // Password: obligatorio en creación, opcional en edición
        if (!State.editingId && pwd.value.length < 8) {
            pwd.classList.add('is-invalid'); ok = false;
        } else pwd.classList.remove('is-invalid');

        if (pwd.value && pwd.value !== pwdC.value) {
            pwdC.classList.add('is-invalid'); ok = false;
        } else pwdC.classList.remove('is-invalid');

        return ok;
    },

    async submit() {
        if (!Form.validate()) return;

        const fd = new FormData();
        fd.append('strNombreUsuario', document.getElementById('strNombreUsuario').value.trim());
        fd.append('strCorreo',        document.getElementById('strCorreo').value.trim());
        fd.append('idPerfil',         document.getElementById('idPerfil').value);
        fd.append('idEstadoUsuario',  document.getElementById('idEstadoUsuario').value);
        fd.append('strNumeroCelular', document.getElementById('strNumeroCelular').value.trim());

        const pwd = document.getElementById('strPwd').value;
        if (pwd) {
            fd.append('strPwd', pwd);
            fd.append('strPwd_confirmation', document.getElementById('strPwdConfirmation').value);
        }

        if (State.imgFile) fd.append('strImagen', State.imgFile);

        const id   = State.editingId;
        // Siempre POST — para edición usamos _method=PUT (Laravel method spoofing)
        // Esto es obligatorio con FormData/multipart ya que PHP no parsea PUT multipart
        const url    = id ? `{{ url('api/usuario') }}/${id}` : `{{ url('api/usuario') }}`;
        const method = 'POST';

        if (id) fd.append('_method', 'PUT');

        document.getElementById('btnSave').disabled = true;

        try {
            const res  = await apiFetch(url, { method, body: fd });
            const data = await res.json();

            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('usuarioModal')).hide();
                Notif.show(data.message, 'success');
                Table.load(State.currentPage);
            } else {
                const errDiv = document.getElementById('formErrors');
                errDiv.innerHTML = '<ul class="mb-0">' +
                    Object.values(data.errors || {}).flat().map(e => `<li>${e}</li>`).join('') +
                    '</ul>';
                errDiv.classList.remove('d-none');
            }
        } catch {
            Notif.show('Error al guardar el usuario.', 'danger');
        } finally {
            document.getElementById('btnSave').disabled = false;
        }
    }
};

function previewImg(input) {
    if (!input.files[0]) return;
    State.imgFile = input.files[0];
    const reader = new FileReader();
    reader.onload = e => {
        const prev = document.getElementById('avatarPreview');
        prev.outerHTML = `<img id="avatarPreview" src="${e.target.result}" class="avatar-preview mx-auto mb-2" style="display:block;margin:0 auto">`;
    };
    reader.readAsDataURL(State.imgFile);
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
let st; function debounceSearch(){clearTimeout(st);st=setTimeout(()=>Table.load(1),400);}

// Init via async IIFE above
</script>
@endpush

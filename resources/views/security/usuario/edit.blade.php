@extends('layouts.app')
@section('title','Editar Usuario')
@section('page-title','Editar Usuario')

@section('content')
<div class="card" style="max-width:700px">
    <div class="card-header"><i class="bi bi-person-badge me-2"></i>Editar Usuario</div>
    <div class="card-body">
        <div id="formErrors" class="alert alert-danger d-none"></div>

        <div class="text-center mb-4">
            <div id="avatarWrap">
                <div id="avatarPreview" style="width:80px;height:80px;border-radius:50%;background:#1a2a4a;display:flex;align-items:center;justify-content:center;color:#fff;font-size:2rem;font-weight:700;border:3px solid #2979ff;margin:0 auto 8px">?</div>
            </div>
            <label class="btn btn-outline-secondary btn-sm" for="imgInput">
                <i class="bi bi-camera me-1"></i>Cambiar Foto
            </label>
            <input type="file" id="imgInput" accept="image/jpg,image/jpeg,image/png" class="d-none" onchange="previewImg(this)">
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Nombre de Usuario <span class="text-danger">*</span></label>
                <input type="text" id="strNombreUsuario" class="form-control" maxlength="100">
                <div class="invalid-feedback">Campo obligatorio.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Correo <span class="text-danger">*</span></label>
                <input type="email" id="strCorreo" class="form-control">
                <div class="invalid-feedback">Ingresa un correo válido.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Nueva Contraseña <small class="text-muted">(dejar vacío para no cambiar)</small></label>
                <div class="input-group">
                    <input type="password" id="strPwd" class="form-control" placeholder="Mínimo 8 caracteres">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('strPwd',this)" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div class="invalid-feedback" id="strPwd-err" style="display:none">Mínimo 8 caracteres.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Confirmar Nueva Contraseña</label>
                <div class="input-group">
                    <input type="password" id="strPwdConf" class="form-control" placeholder="Repetir contraseña">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('strPwdConf',this)" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div class="invalid-feedback" id="strPwdConf-err" style="display:none">Las contraseñas no coinciden.</div>
            </div>
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
                <label class="form-label fw-semibold">Estado</label>
                <select id="idEstadoUsuario" class="form-select">
                    @foreach($estados as $e)
                        <option value="{{ $e->id }}">{{ $e->strNombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Celular</label>
                <input type="text" id="strNumeroCelular" class="form-control" maxlength="20">
            </div>
        </div>
    </div>
    <div class="card-footer d-flex gap-2 justify-content-end">
        <a href="{{ route('usuario.index') }}" class="btn btn-secondary btn-sm">Cancelar</a>
        <button class="btn btn-primary btn-sm" id="btnSave" onclick="guardar()">
            <i class="bi bi-floppy me-1"></i>Guardar Cambios
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
const usuarioId = {{ $id }};
let imgFile = null;

function togglePwd(id, btn) {
    const el = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (el.type === 'password') {
        el.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        el.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

// Precargar datos
(async () => {
    const res = await apiFetch(`{{ url('api/usuario') }}/${usuarioId}`);
    const u   = await res.json();

    document.getElementById('strNombreUsuario').value = u.strNombreUsuario;
    document.getElementById('strCorreo').value        = u.strCorreo;
    document.getElementById('strNumeroCelular').value = u.strNumeroCelular ?? '';
    document.getElementById('idPerfil').value         = u.idPerfil;
    document.getElementById('idEstadoUsuario').value  = u.idEstadoUsuario;

    if (u.imagen_url) {
        document.getElementById('avatarPreview').outerHTML =
            `<img id="avatarPreview" src="${u.imagen_url}" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #2979ff;display:block;margin:0 auto 8px">`;
    } else {
        document.getElementById('avatarPreview').textContent = u.strNombreUsuario.charAt(0).toUpperCase();
    }
})();

function previewImg(input) {
    if (!input.files[0]) return;
    imgFile = input.files[0];
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('avatarPreview').outerHTML =
            `<img id="avatarPreview" src="${e.target.result}" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #2979ff;display:block;margin:0 auto 8px">`;
    };
    reader.readAsDataURL(imgFile);
}

async function guardar() {
    const nombre = document.getElementById('strNombreUsuario');
    const correo = document.getElementById('strCorreo');
    const pwd    = document.getElementById('strPwd');
    const pwdC   = document.getElementById('strPwdConf');
    const perfil = document.getElementById('idPerfil');
    const errDiv = document.getElementById('formErrors');
    const btn    = document.getElementById('btnSave');
    let ok = true;

    errDiv.classList.add('d-none');
    [nombre, correo, pwd, pwdC, perfil].forEach(f => f.classList.remove('is-invalid'));

    if (!nombre.value.trim()) { nombre.classList.add('is-invalid'); ok = false; }
    if (!correo.value.includes('@')) { correo.classList.add('is-invalid'); ok = false; }
    if (!perfil.value) { perfil.classList.add('is-invalid'); ok = false; }
    if (pwd.value && pwd.value.length < 8) { pwd.classList.add('is-invalid'); ok = false; }
    if (pwd.value && pwd.value !== pwdC.value) { pwdC.classList.add('is-invalid'); ok = false; }
    if (!ok) return;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

    const fd = new FormData();
    fd.append('strNombreUsuario', nombre.value.trim());
    fd.append('strCorreo', correo.value.trim());
    fd.append('idPerfil', perfil.value);
    fd.append('idEstadoUsuario', document.getElementById('idEstadoUsuario').value);
    fd.append('strNumeroCelular', document.getElementById('strNumeroCelular').value.trim());
    if (pwd.value) fd.append('strPwd', pwd.value);
    if (imgFile) fd.append('strImagen', imgFile);

    const res  = await apiFetch(`{{ url('api/usuario') }}/${usuarioId}`, { method: 'POST', body: fd });
    const data = await res.json();

    if (data.success) {
        // Si editó su propio usuario, actualizar imagen en localStorage
        const myName = localStorage.getItem('user_name');
        if (data.data && data.data.strNombreUsuario === myName && data.data.imagen_url) {
            localStorage.setItem('user_img', data.data.imagen_url);
        }
        window.location.href = '{{ route("usuario.index") }}';
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
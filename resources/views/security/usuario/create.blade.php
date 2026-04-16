@extends('layouts.app')
@section('title','Nuevo Usuario')
@section('page-title','Nuevo Usuario')

@section('content')
<div class="card" style="max-width:700px">
    <div class="card-header"><i class="bi bi-person-badge me-2"></i>Nuevo Usuario</div>
    <div class="card-body">
        <div id="formErrors" class="alert alert-danger d-none"></div>

        {{-- Foto --}}
        <div class="text-center mb-4">
            <div id="avatarPreview" style="width:80px;height:80px;border-radius:50%;background:#1a2a4a;display:flex;align-items:center;justify-content:center;color:#fff;font-size:2rem;font-weight:700;border:3px solid #2979ff;margin:0 auto 8px">U</div>
            <label class="btn btn-outline-secondary btn-sm" for="imgInput">
                <i class="bi bi-camera me-1"></i>Subir Foto
            </label>
            <input type="file" id="imgInput" accept="image/jpg,image/jpeg,image/png" class="d-none" onchange="previewImg(this)">
            <small class="d-block text-muted mt-1">JPG/PNG, máx. 2MB</small>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Nombre de Usuario <span class="text-danger">*</span></label>
                <input type="text" id="strNombreUsuario" class="form-control" maxlength="100" placeholder="usuario123">
                <div class="invalid-feedback">Campo obligatorio.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Correo <span class="text-danger">*</span></label>
                <input type="email" id="strCorreo" class="form-control" placeholder="correo@ejemplo.com">
                <div class="invalid-feedback">Ingresa un correo válido.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Contraseña <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" id="strPwd" class="form-control" placeholder="Mínimo 8 caracteres">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('strPwd',this)" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div class="invalid-feedback" id="strPwd-err" style="display:none">Mínimo 8 caracteres.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Confirmar Contraseña <span class="text-danger">*</span></label>
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
                <input type="text" id="strNumeroCelular" class="form-control" maxlength="20" placeholder="55 1234 5678">
            </div>
        </div>
    </div>
    <div class="card-footer d-flex gap-2 justify-content-end">
        <a href="{{ route('usuario.index') }}" class="btn btn-secondary btn-sm">Cancelar</a>
        <button class="btn btn-primary btn-sm" id="btnSave" onclick="guardar()">
            <i class="bi bi-floppy me-1"></i>Guardar
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
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

    if (!nombre.value.trim()) { nombre.classList.add('is-invalid'); ok = false; } else nombre.classList.remove('is-invalid');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(correo.value.trim())) { correo.classList.add('is-invalid'); ok = false; } else correo.classList.remove('is-invalid');
    if (pwd.value.length < 8) { pwd.classList.add('is-invalid'); document.getElementById('strPwd-err').style.display='block'; ok = false; }
    else { pwd.classList.remove('is-invalid'); document.getElementById('strPwd-err').style.display='none'; }
    if (pwd.value !== pwdC.value) { pwdC.classList.add('is-invalid'); document.getElementById('strPwdConf-err').style.display='block'; ok = false; }
    else { pwdC.classList.remove('is-invalid'); document.getElementById('strPwdConf-err').style.display='none'; }
    if (!perfil.value) { perfil.classList.add('is-invalid'); ok = false; } else perfil.classList.remove('is-invalid');
    if (!ok) return;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

    const fd = new FormData();
    fd.append('strNombreUsuario', nombre.value.trim());
    fd.append('strCorreo', correo.value.trim());
    fd.append('strPwd', pwd.value);
    fd.append('idPerfil', perfil.value);
    fd.append('idEstadoUsuario', document.getElementById('idEstadoUsuario').value);
    fd.append('strNumeroCelular', document.getElementById('strNumeroCelular').value.trim());
    if (imgFile) fd.append('strImagen', imgFile);

    const res  = await apiFetch('{{ url("api/usuario") }}', { method: 'POST', body: fd });
    const data = await res.json();

    if (data.success) {
        window.location.href = '{{ route("usuario.index") }}';
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

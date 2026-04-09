<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Corporativo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a2a4a 0%, #0d47a1 100%);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        .login-card {
            background: #fff; border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,.35);
            width: 100%; max-width: 420px; overflow: hidden;
        }
        .login-header {
            background: #1a2a4a; color: #fff;
            padding: 28px 32px 22px; text-align: center;
        }
        .login-header .icon {
            width: 60px; height: 60px; background: #2979ff;
            border-radius: 50%; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 12px; font-size: 1.6rem;
        }
        .login-body { padding: 28px 32px 32px; }
        .btn-login { background: #2979ff; border: none; width: 100%;
            padding: 12px; border-radius: 8px; color: #fff;
            font-weight: 700; font-size: 1rem; cursor: pointer; transition: .2s; }
        .btn-login:hover { background: #1565c0; }
        .btn-login:disabled { opacity: .7; cursor: not-allowed; }
        .g-recaptcha { margin: 16px 0; display: flex; justify-content: center; }
        #msgError { min-height: 1.4rem; font-size: .875rem; font-weight: 600; color: #dc2626; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-header">
        <div class="icon"><i class="bi bi-building"></i></div>
        <h4 class="fw-bold mb-1">Sistema Corporativo</h4>
        <small class="opacity-75">Inicia sesión para continuar</small>
    </div>
    <div class="login-body">

        <div class="mb-3">
            <label class="form-label fw-semibold">Usuario</label>
            <input type="text" id="strNombreUsuario" class="form-control"
                   placeholder="Nombre de usuario" autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Contraseña</label>
            <input type="password" id="strPwd" class="form-control"
                   placeholder="••••••••">
        </div>

        <div class="g-recaptcha" data-sitekey="{{ env('NOCAPTCHA_SITEKEY') }}"></div>

        <button id="btnLogin" class="btn-login" onclick="ejecutarLogin()">
            <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
        </button>

        <div id="msgError" class="mt-3 text-center"></div>
    </div>
</div>

<script>
    // Si ya tiene token válido, ir directo al dashboard
    if (localStorage.getItem('jwt_token')) {
        window.location.href = '/dashboard';
    }

    async function ejecutarLogin() {
        const usuario  = document.getElementById('strNombreUsuario').value.trim();
        const password = document.getElementById('strPwd').value;
        const captcha  = grecaptcha.getResponse();
        const btn      = document.getElementById('btnLogin');
        const errorDiv = document.getElementById('msgError');

        errorDiv.textContent = '';

        if (!usuario || !password) {
            errorDiv.textContent = 'Completa todos los campos.';
            return;
        }
        if (!captcha) {
            errorDiv.textContent = 'Por favor completa el captcha.';
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Validando...';

        try {
            const res = await fetch('/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    strNombreUsuario: usuario,
                    strPwd: password,
                    'g-recaptcha-response': captcha
                })
            });

            const data = await res.json();

            if (res.ok) {
                // Guardar token en localStorage (igual que proyecto Rust)
                localStorage.setItem('jwt_token', data.token);
                localStorage.setItem('user_name', data.nombre);
                localStorage.setItem('perfil_id', data.perfil_id);
                localStorage.setItem('user_img', data.imagen || '');
                window.location.href = '/dashboard';
            } else {
                errorDiv.textContent = data.error || data.message || 'Error al iniciar sesión.';
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión';
                grecaptcha.reset();
            }
        } catch (err) {
            errorDiv.textContent = 'Error de conexión. Intenta de nuevo.';
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión';
        }
    }

    // Enter para enviar
    document.addEventListener('keydown', e => {
        if (e.key === 'Enter') ejecutarLogin();
    });
</script>
</body>
</html>

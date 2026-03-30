{{-- ============================================================ --}}
{{-- ARCHIVO: resources/views/auth/login.blade.php               --}}
{{-- ============================================================ --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Iniciar Sesión | Corporativo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #1a2a4a;
            --accent:  #2979ff;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a2a4a 0%, #0d47a1 50%, #1565c0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,.35);
            overflow: hidden;
            width: 100%;
            max-width: 440px;
        }

        .login-header {
            background: var(--primary);
            color: #fff;
            padding: 28px 32px 22px;
            text-align: center;
        }

        .login-header .logo-icon {
            width: 64px; height: 64px;
            background: var(--accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 1.8rem;
        }


        .login-body { padding: 32px; }

        .form-label { font-weight: 600; font-size: .875rem; color: #374151; }

        .form-control {
            border-radius: 8px;
            border: 1.5px solid #d1d5db;
            padding: .6rem 1rem;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(41,121,255,.15);
        }

        .input-group-text {
            border-radius: 8px 0 0 8px;
            background: #f3f4f6;
            border: 1.5px solid #d1d5db;
            color: #6b7280;
        }

        .btn-login {
            background: var(--accent);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-weight: 600;
            padding: .7rem;
            width: 100%;
            font-size: 1rem;
            transition: background .2s, transform .1s;
        }

        .btn-login:hover { background: #1565c0; }
        .btn-login:active { transform: scale(.98); }

        .captcha-wrapper {
            display: flex;
            justify-content: center;
            margin: 12px 0;
        }

        .alert-login {
            border-radius: 8px;
            font-size: .875rem;
        }

        @media (max-width: 480px) {
            .login-card { border-radius: 0; min-height: 100vh; }
            .login-body  { padding: 24px 20px; }
        }
    </style>
</head>
<body>

<div class="login-card">
    {{-- Header --}}
    <div class="login-header">
        <div class="logo-icon"><i class="bi bi-building"></i></div>
        <h4 class="mb-1 fw-bold">Sistema Corporativo</h4>
        <small class="opacity-75">Ingresa tus credenciales para continuar</small>
    </div>

    {{-- Body --}}
    <div class="login-body">

        {{-- Errores --}}
        @if($errors->has('login'))
            <div class="alert alert-danger alert-login" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ $errors->first('login') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-login" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
            </div>
        @endif

       <form action="{{ secure_url(route('login.post')) }}" method="POST" id="loginForm" novalidate>
            @csrf

            {{-- Usuario --}}
            <div class="mb-3">
                <label for="strNombreUsuario" class="form-label">Usuario</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input
                        type="text"
                        class="form-control @error('strNombreUsuario') is-invalid @enderror"
                        id="strNombreUsuario"
                        name="strNombreUsuario"
                        value="{{ old('strNombreUsuario') }}"
                        placeholder="Nombre de usuario"
                        autocomplete="username"
                        required
                    >
                    @error('strNombreUsuario')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Contraseña --}}
            <div class="mb-3">
                <label for="strPwd" class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input
                        type="password"
                        class="form-control @error('strPwd') is-invalid @enderror"
                        id="strPwd"
                        name="strPwd"
                        placeholder="Contraseña"
                        autocomplete="current-password"
                        required
                    >
                    <button class="btn btn-outline-secondary" type="button" id="togglePwd">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                    @error('strPwd')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- CAPTCHA --}}
            <div class="mb-3">
                <div class="captcha-wrapper">
                    {!! NoCaptcha::renderJs() !!}
                    {!! NoCaptcha::display() !!}
                </div>
                @error('g-recaptcha-response')
                    <div class="text-danger small text-center mt-1">
                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-login mt-2">
                <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle mostrar/ocultar contraseña
    document.getElementById('togglePwd').addEventListener('click', function () {
        const input   = document.getElementById('strPwd');
        const icon    = document.getElementById('eyeIcon');
        const isText  = input.type === 'text';
        input.type    = isText ? 'password' : 'text';
        icon.className = isText ? 'bi bi-eye' : 'bi bi-eye-slash';
    });

    // Validación básica en cliente antes de enviar
    document.getElementById('loginForm').addEventListener('submit', function (e) {
        const user = document.getElementById('strNombreUsuario').value.trim();
        const pwd  = document.getElementById('strPwd').value.trim();
        if (!user || !pwd) {
            e.preventDefault();
            alert('Por favor completa todos los campos.');
        }
    });
</script>
</body>
</html>

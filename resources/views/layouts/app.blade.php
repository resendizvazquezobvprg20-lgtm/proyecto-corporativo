{{-- ============================================================ --}}
{{-- ARCHIVO: resources/views/layouts/app.blade.php             --}}
{{-- ============================================================ --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema Corporativo') | Corporativo</title>

    {{-- Bootstrap 5 + Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary:      #1a2a4a;
            --primary-dark: #0f1e38;
            --accent:       #2979ff;
            --accent-light: #5599ff;
            --sidebar-w:    260px;
            --topbar-h:     60px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #f0f2f5;
            color: #1e293b;
        }

        /* ── Sidebar ── */
        #sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--primary);
            display: flex;
            flex-direction: column;
            z-index: 1040;
            transition: transform .3s ease;
            overflow-y: auto;
        }

        #sidebar .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 18px 20px;
            background: var(--primary-dark);
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            border-bottom: 2px solid var(--accent);
            min-height: var(--topbar-h);
        }

        #sidebar .brand i { font-size: 1.5rem; color: var(--accent-light); }

        /* Nav menú */
        #sidebar .nav-label {
            padding: 12px 20px 4px;
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255,255,255,.4);
            font-weight: 600;
        }

        #sidebar .nav-item-parent {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: rgba(255,255,255,.8);
            font-size: .9rem;
            cursor: pointer;
            transition: background .2s;
            user-select: none;
        }

        #sidebar .nav-item-parent:hover { background: rgba(255,255,255,.08); }
        #sidebar .nav-item-parent .bi-chevron-down { margin-left: auto; transition: transform .2s; }
        #sidebar .nav-item-parent.open .bi-chevron-down { transform: rotate(180deg); }

        #sidebar .submenu { display: none; overflow: hidden; }
        #sidebar .submenu.show { display: block; }

        #sidebar .submenu a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 20px 8px 44px;
            color: rgba(255,255,255,.65);
            text-decoration: none;
            font-size: .85rem;
            transition: all .2s;
        }

        #sidebar .submenu a:hover,
        #sidebar .submenu a.active {
            color: #fff;
            background: rgba(41,121,255,.3);
            border-right: 3px solid var(--accent);
        }

        /* ── Topbar ── */
        #topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: #fff;
            display: flex;
            align-items: center;
            padding: 0 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
            z-index: 1030;
        }

        #topbar .toggle-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.4rem;
            color: var(--primary);
            cursor: pointer;
            margin-right: 12px;
        }

        #topbar .user-info {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #topbar .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--accent);
        }

        /* ── Main content ── */
        #main-content {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            padding: 24px;
            min-height: calc(100vh - var(--topbar-h));
        }

        /* ── Breadcrumb ── */
        .breadcrumb-section {
            background: #fff;
            border-radius: 10px;
            padding: 12px 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }

        /* ── Cards ── */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,.07);
        }

        .card-header {
            background: var(--primary);
            color: #fff;
            border-radius: 12px 12px 0 0 !important;
            padding: 14px 20px;
            font-weight: 600;
        }

        /* ── Tables ── */
        .table thead th {
            background: var(--primary);
            color: #fff;
            font-weight: 600;
            font-size: .85rem;
            border: none;
        }

        .table-hover tbody tr:hover { background: rgba(41,121,255,.06); }

        /* ── Responsive ── */
        #sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 1039;
        }

        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.mobile-open { transform: translateX(0); }
            #topbar { left: 0; }
            #main-content { margin-left: 0; }
            #topbar .toggle-btn { display: block; }
            #sidebar-overlay.active { display: block; }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- Overlay móvil --}}
<div id="sidebar-overlay" onclick="closeSidebar()"></div>

{{-- ── Sidebar ─────────────────────────────────────── --}}
<nav id="sidebar">
    <div class="brand">
        <i class="bi bi-building"></i>
        <span>Corporativo</span>
    </div>

    @php
        try {
            $jwtUser         = \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->authenticate();
            $usuarioCompleto = \App\Models\Usuario::with('perfil')->find($jwtUser->id);
            $esAdmin         = $usuarioCompleto?->perfil?->bitAdministrador ?? false;
            $menus           = \App\Http\Controllers\DashboardController::buildMenu(
                                   $usuarioCompleto->idPerfil, $esAdmin);
        } catch (\Exception $e) {
            // Token inválido o expirado — el middleware JwtMiddleware ya redirigirá,
            // pero dejamos valores vacíos para evitar errores en la vista.
            $usuarioCompleto = null;
            $esAdmin         = false;
            $menus           = [];
        }
    @endphp
    @if(!$usuarioCompleto)
        <script>window.location.href = '/login';</script>
    @endif

    @foreach($menus as $menu)
        <div class="nav-label">{{ $menu['nombre'] }}</div>

        <div class="nav-item-parent" onclick="toggleSubmenu('menu-{{ $menu['id'] }}', this)">
            <i class="bi {{ $menu['icono'] ?? 'bi-folder' }}"></i>
            <span>{{ $menu['nombre'] }}</span>
            <i class="bi bi-chevron-down"></i>
        </div>

        <div class="submenu" id="menu-{{ $menu['id'] }}">
            @foreach($menu['submenus'] as $sub)
                @php
                    $route = match($sub['id']) {
                        1 => 'perfil.index',
                        2 => 'modulo.index',
                        3 => 'permiso.index',
                        4 => 'usuario.index',
                        5 => 'p1.sub1',
                        6 => 'p1.sub2',
                        7 => 'p2.sub1',
                        8 => 'p2.sub2',
                        default => 'dashboard',
                    };
                @endphp
                <a href="{{ route($route) }}" class="{{ request()->routeIs($route) ? 'active' : '' }}">
                    <i class="bi bi-dot"></i>
                    {{ $sub['nombre'] }}
                </a>
            @endforeach
        </div>
    @endforeach
</nav>

{{-- ── Topbar ─────────────────────────────────────── --}}
<header id="topbar">
    <button class="toggle-btn" onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
    </button>

    <span class="fw-semibold text-secondary" style="font-size:.9rem;">
        @yield('page-title', 'Dashboard')
    </span>

    <div class="user-info">
        @if($usuarioCompleto->strImagen)
            <img src="{{ asset('storage/' . $usuarioCompleto->strImagen) }}" alt="Avatar" class="user-avatar">
        @else
            <div class="user-avatar d-flex align-items-center justify-content-center"
                 style="background:var(--accent);color:#fff;font-weight:700;">
                {{ strtoupper(substr($usuarioCompleto->strNombreUsuario, 0, 1)) }}
            </div>
        @endif

        <div class="dropdown">
            <a href="#" class="dropdown-toggle text-decoration-none text-dark fw-semibold"
               data-bs-toggle="dropdown" style="font-size:.9rem;">
                {{ $usuarioCompleto->strNombreUsuario }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item-text text-muted small">{{ $usuarioCompleto->strCorreo }}</span></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

{{-- ── Contenido principal ─────────────────────────── --}}
<main id="main-content">
    {{-- Breadcrumbs --}}
    @if(isset($breadcrumbs))
    <section class="breadcrumb-section">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                @foreach($breadcrumbs as $crumb)
                    @if($loop->last)
                        <li class="breadcrumb-item active fw-semibold" aria-current="page">
                            {{ $crumb['label'] }}
                        </li>
                    @else
                        <li class="breadcrumb-item">
                            @if($crumb['url'])
                                <a href="{{ $crumb['url'] }}" class="text-decoration-none">{{ $crumb['label'] }}</a>
                            @else
                                <span class="text-muted">{{ $crumb['label'] }}</span>
                            @endif
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
    </section>
    @endif

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Toggle sidebar móvil
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('mobile-open');
        document.getElementById('sidebar-overlay').classList.toggle('active');
    }

    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('mobile-open');
        document.getElementById('sidebar-overlay').classList.remove('active');
    }

    // Toggle submenú
    function toggleSubmenu(id, parent) {
        const submenu = document.getElementById(id);
        submenu.classList.toggle('show');
        parent.classList.toggle('open');
    }

    // Abrir submenú activo automáticamente
    document.querySelectorAll('.submenu a.active').forEach(link => {
        const submenu = link.closest('.submenu');
        if (submenu) {
            submenu.classList.add('show');
            const parent = submenu.previousElementSibling;
            if (parent) parent.classList.add('open');
        }
    });

    // Helper: obtener cookie JWT para Fetch API
    function getJwtToken() {
        return document.cookie.split(';')
            .find(c => c.trim().startsWith('jwt_token='))
            ?.split('=')[1] || '';
    }

    // Helper global fetch con JWT y CSRF
    async function apiFetch(url, options = {}) {
        const defaults = {
            headers: {
                'Authorization': 'Bearer ' + getJwtToken(),
                'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept':        'application/json',
            }
        };

        // No sobrescribir Content-Type si es FormData
        if (!(options.body instanceof FormData)) {
            defaults.headers['Content-Type'] = 'application/json';
        }

        const response = await fetch(url, { ...defaults, ...options, headers: { ...defaults.headers, ...(options.headers || {}) } });

        if (response.status === 401 || response.status === 403) {
            window.location.href = '/login';
            return null;
        }

        return response;
    }
</script>

@stack('scripts')
</body>
</html>

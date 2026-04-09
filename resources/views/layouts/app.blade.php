<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema Corporativo') | Corporativo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #1a2a4a; --accent: #2979ff;
            --sidebar-w: 260px; --topbar-h: 60px;
        }
        /* Ocultar body hasta verificar auth (igual que Rust: display:none) */
        body { display: none; font-family: 'Segoe UI', system-ui, sans-serif; background: #f0f2f5; margin: 0; }

        #sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w); height: 100vh;
            background: var(--primary); display: flex;
            flex-direction: column; z-index: 1040;
            transition: transform .3s ease; overflow-y: auto;
        }
        .brand {
            display: flex; align-items: center; gap: 10px;
            padding: 18px 20px; background: #0f1e38;
            color: #fff; font-weight: 700; font-size: 1.05rem;
            border-bottom: 2px solid var(--accent); min-height: var(--topbar-h);
        }
        .brand i { font-size: 1.4rem; color: #5599ff; }
        .nav-label {
            padding: 12px 20px 4px; font-size: .7rem;
            text-transform: uppercase; letter-spacing: .08em;
            color: rgba(255,255,255,.4); font-weight: 600;
        }
        .nav-parent {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 20px; color: rgba(255,255,255,.8);
            font-size: .9rem; cursor: pointer; transition: background .2s; user-select: none;
        }
        .nav-parent:hover { background: rgba(255,255,255,.08); }
        .nav-parent .arrow { margin-left: auto; transition: transform .2s; }
        .nav-parent.open .arrow { transform: rotate(180deg); }
        .submenu { display: none; }
        .submenu.show { display: block; }
        .submenu a {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 20px 8px 44px; color: rgba(255,255,255,.65);
            text-decoration: none; font-size: .85rem; transition: all .2s;
        }
        .submenu a:hover, .submenu a.active {
            color: #fff; background: rgba(41,121,255,.25);
            border-left: 3px solid var(--accent);
        }
        .sidebar-footer {
            margin-top: auto; padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,.1);
        }
        .btn-logout {
            display: flex; align-items: center; gap: 8px;
            color: rgba(255,255,255,.6); cursor: pointer;
            font-size: .85rem; background: none; border: none;
            padding: 8px 0; width: 100%; transition: color .2s;
        }
        .btn-logout:hover { color: #fff; }

        /* Topbar */
        #topbar {
            position: fixed; top: 0; left: var(--sidebar-w);
            right: 0; height: var(--topbar-h);
            background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,.1);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px; z-index: 1030;
        }
        #topbar .page-title { font-weight: 700; color: var(--primary); font-size: 1rem; }
        .user-badge {
            display: flex; align-items: center; gap: 10px;
            font-size: .875rem; color: #374151;
        }
        .user-badge img, .user-avatar-text {
            width: 36px; height: 36px; border-radius: 50%;
            object-fit: cover; border: 2px solid var(--accent);
        }
        .user-avatar-text {
            background: var(--primary); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 1rem;
        }

        /* Content */
        #content {
            margin-left: var(--sidebar-w);
            padding: calc(var(--topbar-h) + 24px) 24px 24px;
            min-height: 100vh;
        }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
        .card-header {
            background: #fff; border-bottom: 1px solid #e5e7eb;
            border-radius: 12px 12px 0 0 !important;
            padding: 14px 20px; font-weight: 600; color: var(--primary);
        }
        .breadcrumb { background: none; padding: 0; margin-bottom: 16px; font-size: .85rem; }
        .breadcrumb-item a { color: var(--accent); text-decoration: none; }
        .breadcrumb-item.active { color: #6b7280; }

        /* Responsive */
        #sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.5); z-index: 1039;
        }
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            #sidebar-overlay.active { display: block; }
            #topbar { left: 0; }
            #content { margin-left: 0; }
        }

        @stack('styles')
    </style>
    @stack('styles')
</head>
<body>

<div id="sidebar-overlay" onclick="closeSidebar()"></div>

<nav id="sidebar">
    <div class="brand">
        <i class="bi bi-building"></i>
        <span>Corporativo</span>
    </div>
    <div id="sidebarMenu"></div>
    <div class="sidebar-footer">
        <div class="user-badge mb-3">
            <div class="user-avatar-text" id="avatarInitial">?</div>
            <div>
                <div id="sidebarUserName" style="font-weight:600;color:#fff;font-size:.85rem;">Cargando...</div>
                <div style="color:rgba(255,255,255,.5);font-size:.75rem;">Usuario activo</div>
            </div>
        </div>
        <button class="btn-logout" onclick="doLogout()">
            <i class="bi bi-box-arrow-left"></i> Cerrar Sesión
        </button>
    </div>
</nav>

<div id="topbar">
    <div>
        <div class="page-title">@yield('page-title', 'Inicio')</div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                @foreach($breadcrumbs ?? [] as $crumb)
                    @if(!$loop->last)
                        <li class="breadcrumb-item">
                            @if($crumb['url'])
                                <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                            @else
                                {{ $crumb['label'] }}
                            @endif
                        </li>
                    @else
                        <li class="breadcrumb-item active">{{ $crumb['label'] }}</li>
                    @endif
                @endforeach
            </ol>
        </nav>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="user-badge">
            <div class="user-avatar-text" id="topbarInitial">?</div>
            <span id="topbarUserName">Cargando...</span>
        </div>
        <button class="btn btn-sm btn-outline-danger" onclick="doLogout()">
            <i class="bi bi-box-arrow-right"></i>
        </button>
        <button class="btn btn-sm btn-outline-secondary d-md-none" onclick="openSidebar()">
            <i class="bi bi-list"></i>
        </button>
    </div>
</div>

<main id="content">
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── Auth: verificar token al cargar (igual que proyecto Rust) ─────────
const TOKEN = localStorage.getItem('jwt_token');
const USER_NAME = localStorage.getItem('user_name') || 'Usuario';

if (!TOKEN) {
    window.location.href = '/login';
} else {
    // Mostrar body solo si hay token
    document.body.style.display = 'block';

    // Mostrar nombre de usuario
    document.getElementById('sidebarUserName').textContent = USER_NAME;
    document.getElementById('topbarUserName').textContent = USER_NAME;
    const initial = USER_NAME.charAt(0).toUpperCase();
    document.getElementById('avatarInitial').textContent = initial;
    document.getElementById('topbarInitial').textContent = initial;
}

// ── Logout ────────────────────────────────────────────────────────────
function doLogout() {
    localStorage.clear();
    window.location.href = '/login';
}

// ── Cargar menú dinámico con JWT en header ────────────────────────────
async function cargarMenu() {
    try {
        const res = await apiFetch('/api/menu');
        if (!res || !res.ok) return;
        const menus = await res.json();

        const container = document.getElementById('sidebarMenu');
        container.innerHTML = '';

        menus.forEach(menu => {
            const menuId = 'menu-' + menu.id;
            const subsHtml = menu.submenus.map(sub => {
                const isActive = window.location.pathname.includes(sub.ruta) ? 'active' : '';
                return `<a href="${sub.ruta}" class="${isActive}">
                    <i class="bi bi-dot"></i>${sub.nombre}
                </a>`;
            }).join('');

            const isOpen = menu.submenus.some(s => window.location.pathname.includes(s.ruta));

            container.innerHTML += `
                <div class="nav-label">${menu.nombre}</div>
                <div class="nav-parent ${isOpen ? 'open' : ''}" onclick="toggleSub('${menuId}', this)">
                    <i class="bi ${menu.icono || 'bi-folder'}"></i>
                    <span>${menu.nombre}</span>
                    <i class="bi bi-chevron-down arrow"></i>
                </div>
                <div class="submenu ${isOpen ? 'show' : ''}" id="${menuId}">${subsHtml}</div>
            `;
        });
    } catch(e) {
        console.error('Error cargando menú:', e);
    }
}

function toggleSub(id, el) {
    document.getElementById(id).classList.toggle('show');
    el.classList.toggle('open');
}
function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('sidebar-overlay').classList.add('active');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebar-overlay').classList.remove('active');
}

// ── Fetch API con Authorization header (igual que Rust) ───────────────
async function apiFetch(url, options = {}) {
    const token = localStorage.getItem('jwt_token');
    const isFormData = options.body instanceof FormData;

    const headers = {
        'Authorization': 'Bearer ' + (token || ''),
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        ...(isFormData ? {} : { 'Content-Type': 'application/json' }),
        ...(options.headers || {})
    };

    const response = await fetch(url, { ...options, headers });

    if (response.status === 401) {
        localStorage.clear();
        window.location.href = '/login';
        return null;
    }
    if (response.status === 403) {
        window.location.href = '/error-403';
        return null;
    }

    return response;
}

function escHtml(s) {
    return String(s || '').replace(/[&<>"']/g, c =>
        ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

// ── Toast notifications ───────────────────────────────────────────────
function showToast(msg, type = 'success') {
    const id = 'toast-' + Date.now();
    document.body.insertAdjacentHTML('beforeend', `
        <div id="${id}" class="toast align-items-center text-white bg-${type} border-0
             position-fixed bottom-0 end-0 m-3" style="z-index:9999" role="alert">
            <div class="d-flex">
                <div class="toast-body fw-semibold">${msg}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
            </div>
        </div>`);
    const el = document.getElementById(id);
    new bootstrap.Toast(el, { delay: 3500 }).show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
}

// ── Init ──────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    if (TOKEN) cargarMenu();
});
</script>

@stack('scripts')
</body>
</html>

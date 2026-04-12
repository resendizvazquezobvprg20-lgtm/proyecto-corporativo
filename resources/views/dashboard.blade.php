{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Inicio')

@section('content')
<div class="row g-4">
    {{-- Tarjeta de bienvenida — datos cargados por JS desde localStorage --}}
    <div class="col-12">
        <div class="card" style="background: linear-gradient(135deg,#1a2a4a,#2979ff); color:#fff;">
            <div class="card-body py-4">
                <div class="d-flex align-items-center gap-3">
                    <div id="dashAvatar" style="width:60px;height:60px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.8rem;font-weight:700;border:3px solid rgba(255,255,255,.5)">
                        ?
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold">¡Bienvenido, <span id="dashUserName">...</span>!</h4>
                        <small class="opacity-75" id="dashPerfil">Cargando...</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tarjetas de menú — generadas por JS desde /api/menu --}}
    <div id="dashMenuCards" class="row g-4 w-100 ms-0">
        <div class="col-12 text-center py-4">
            <div class="spinner-border text-primary"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async () => {
    // Datos básicos desde localStorage
    const userName = localStorage.getItem('user_name') || 'Usuario';
    document.getElementById('dashUserName').textContent = userName;
    document.getElementById('dashAvatar').textContent = userName.charAt(0).toUpperCase();

    // Cargar menú desde API para construir las tarjetas
    try {
        const res = await apiFetch('/api/menu');
        if (!res || !res.ok) return;
        const menus = await res.json();

        const rutaMap = {
            1: '/seguridad/perfil',
            2: '/seguridad/modulo',
            3: '/seguridad/permisos-perfil',
            4: '/seguridad/usuario',
            5: '/principal1/sub1',
            6: '/principal1/sub2',
            7: '/principal2/sub1',
            8: '/principal2/sub2',
        };

        const iconos = {
            'bi-shield-lock': 'bi-shield-lock',
            'bi-grid': 'bi-grid',
            'bi-layers': 'bi-layers',
        };

        const container = document.getElementById('dashMenuCards');
        if (!menus.length) {
            container.innerHTML = '<div class="col-12 text-center text-muted py-4"><i class="bi bi-inbox me-2"></i>Sin módulos asignados.</div>';
            return;
        }

        container.innerHTML = menus.map(menu => `
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="width:40px;height:40px;background:#1a2a4a;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff">
                                <i class="bi ${menu.icono || 'bi-folder'}"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">${escHtml(menu.nombre)}</h6>
                        </div>
                        <ul class="list-unstyled mb-0">
                            ${menu.submenus.map(sub => `
                                <li class="mb-1">
                                    <a href="${rutaMap[sub.id] || '#'}" class="text-decoration-none text-primary small">
                                        <i class="bi bi-chevron-right me-1" style="font-size:.7rem"></i>
                                        ${escHtml(sub.nombre)}
                                    </a>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                </div>
            </div>
        `).join('');

        // Mostrar perfil en la tarjeta de bienvenida
        document.getElementById('dashPerfil').textContent = '{{ now()->format("d/m/Y H:i") }}';

    } catch(e) {
        console.error('Error cargando dashboard:', e);
    }
});
</script>
@endpush
@endsection

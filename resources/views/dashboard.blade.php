{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Inicio')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card" style="background: linear-gradient(135deg,#1a2a4a,#2979ff); color:#fff;">
            <div class="card-body py-4">
                <div class="d-flex align-items-center gap-3">
                    @if($usuario->strImagen)
                        <img src="{{ asset('storage/'.$usuario->strImagen) }}"
                             style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:3px solid rgba(255,255,255,.5)">
                    @else
                        <div style="width:60px;height:60px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.8rem;font-weight:700;border:3px solid rgba(255,255,255,.5)">
                            {{ strtoupper(substr($usuario->strNombreUsuario,0,1)) }}
                        </div>
                    @endif
                    <div>
                        <h4 class="mb-1 fw-bold">¡Bienvenido, {{ $usuario->strNombreUsuario }}!</h4>
                        <small class="opacity-75">{{ $usuario->perfil->strNombrePerfil }} • {{ now()->format('d/m/Y H:i') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach($menus as $menu)
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div style="width:40px;height:40px;background:#1a2a4a;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff">
                        <i class="bi {{ $menu['icono'] ?? 'bi-folder' }}"></i>
                    </div>
                    <h6 class="mb-0 fw-bold">{{ $menu['nombre'] }}</h6>
                </div>
                <ul class="list-unstyled mb-0">
                    @foreach($menu['submenus'] as $sub)
                    @php
                        $route = match($sub['id']){
                            1=>'perfil.index', 2=>'modulo.index', 3=>'permiso.index',
                            4=>'usuario.index', 5=>'p1.sub1', 6=>'p1.sub2',
                            7=>'p2.sub1', 8=>'p2.sub2', default=>'dashboard'
                        };
                    @endphp
                    <li class="mb-1">
                        <a href="{{ route($route) }}" class="text-decoration-none text-primary small">
                            <i class="bi bi-chevron-right me-1" style="font-size:.7rem"></i>
                            {{ $sub['nombre'] }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

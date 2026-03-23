{{-- ============================================================ --}}
{{-- ARCHIVO: resources/views/errors/404.blade.php              --}}
{{-- ============================================================ --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Página no encontrada</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body { background:#1a2a4a; display:flex; align-items:center; justify-content:center; min-height:100vh; color:#fff; }
        .error-code { font-size:8rem; font-weight:900; color:rgba(255,255,255,.15); line-height:1; }
        .error-title { font-size:1.8rem; font-weight:700; margin-top:-1rem; }
    </style>
</head>
<body>
<div class="text-center px-4">
    <div class="error-code">404</div>
    <div class="error-title">Página no encontrada</div>
    <p class="text-white-50 mt-2 mb-4">La ruta que buscas no existe o no tienes acceso.</p>
    <a href="/login" class="btn btn-primary px-4">
        <i class="bi bi-house me-2"></i>Ir al inicio
    </a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"></script>
</body>
</html>


{{-- ============================================================ --}}
{{-- ARCHIVO: resources/views/errors/500.blade.php              --}}
{{-- ============================================================ --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Error del servidor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body { background:#7f1d1d; display:flex; align-items:center; justify-content:center; min-height:100vh; color:#fff; }
        .error-code { font-size:8rem; font-weight:900; color:rgba(255,255,255,.15); line-height:1; }
    </style>
</head>
<body>
<div class="text-center px-4">
    <div class="error-code">500</div>
    <h2 class="fw-bold mt-n3">Error interno del servidor</h2>
    <p class="text-white-50 mt-2 mb-4">Ocurrió un error inesperado. Contacta al administrador.</p>
    <a href="/login" class="btn btn-danger px-4">Regresar</a>
</div>
</body>
</html>


{{-- ============================================================ --}}
{{-- ARCHIVO: resources/views/main/principal1-sub1.blade.php    --}}
{{-- Pantalla estática con botones CRUD sin funcionalidad        --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'Principal 1.1')
@section('page-title', 'Principal 1 — Submenú 1')

@section('content')
@php
    use Tymon\JWTAuth\Facades\JWTAuth;
    use App\Models\{PermisoPerfil, Usuario};
    $cu = JWTAuth::parseToken()->authenticate();
    $um = Usuario::with('perfil')->find($cu->id);
    $ea = $um->perfil->bitAdministrador;
    $p  = $ea ? ['bitAgregar'=>true,'bitEditar'=>true,'bitEliminar'=>true,'bitConsulta'=>true,'bitDetalle'=>true]
              : (PermisoPerfil::where('idPerfil',$um->idPerfil)->where('idModulo',5)->first()?->toArray() ?? []);
@endphp

<div class="card">
    <div class="card-header">
        <i class="bi bi-grid me-2"></i>Principal 1 — Sub 1
    </div>
    <div class="card-body">
        {{-- Botones visibles según permisos --}}
        <div class="mb-3 d-flex gap-2 flex-wrap">
            @if(!empty($p['bitAgregar']))
            <button class="btn btn-success btn-sm" onclick="alert('Función Agregar (pantalla estática)')">
                <i class="bi bi-plus-lg me-1"></i>Agregar
            </button>
            @endif
            @if(!empty($p['bitEditar']))
            <button class="btn btn-warning btn-sm">
                <i class="bi bi-pencil me-1"></i>Editar
            </button>
            @endif
            @if(!empty($p['bitEliminar']))
            <button class="btn btn-danger btn-sm">
                <i class="bi bi-trash me-1"></i>Eliminar
            </button>
            @endif
            @if(!empty($p['bitConsulta']))
            <button class="btn btn-primary btn-sm">
                <i class="bi bi-search me-1"></i>Consultar
            </button>
            @endif
            @if(!empty($p['bitDetalle']))
            <button class="btn btn-info btn-sm">
                <i class="bi bi-eye me-1"></i>Detalle
            </button>
            @endif
        </div>

        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Este módulo es una <strong>pantalla estática</strong>. Los botones de CRUD están
            visibles según los permisos asignados al perfil, pero no tienen funcionalidad de base de datos.
        </div>

        {{-- Tabla estática de ejemplo --}}
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>#</th><th>Campo 1</th><th>Campo 2</th><th>Campo 3</th><th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 1; $i <= 5; $i++)
                    <tr>
                        <td>{{ $i }}</td>
                        <td>Dato {{ $i }}-A</td>
                        <td>Dato {{ $i }}-B</td>
                        <td>Dato {{ $i }}-C</td>
                        <td>
                            @if(!empty($p['bitDetalle']))  <button class="btn btn-info btn-sm me-1"><i class="bi bi-eye"></i></button> @endif
                            @if(!empty($p['bitEditar']))   <button class="btn btn-warning btn-sm me-1"><i class="bi bi-pencil"></i></button> @endif
                            @if(!empty($p['bitEliminar'])) <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button> @endif
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

{{-- resources/views/main/principal1-sub1.blade.php --}}
@extends('layouts.app')
@section('title', 'Principal 1.1')
@section('page-title', 'Principal 1 — Submenú 1')

@section('content')
@php
    use Tymon\JWTAuth\Facades\JWTAuth;
    use App\Models\{PermisoPerfil, Usuario};
    try {
        $cu = JWTAuth::parseToken()->authenticate();
        $um = Usuario::with('perfil')->find($cu->id);
        $ea = $um?->perfil?->bitAdministrador ?? false;
        $p  = $ea
            ? ['bitAgregar'=>true,'bitEditar'=>true,'bitEliminar'=>true,'bitConsulta'=>true,'bitDetalle'=>true]
            : (PermisoPerfil::where('idPerfil',$um->idPerfil)->where('idModulo',5)->first()?->toArray() ?? []);
    } catch(\Exception $e) {
        $p = [];
    }
@endphp

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-grid me-2"></i>Principal 1 — Sub 1</span>
        @if(!empty($p['bitAgregar']))
        <button class="btn btn-sm btn-light fw-semibold">
            <i class="bi bi-plus-lg me-1"></i>Nuevo
        </button>
        @endif
    </div>
    <div class="card-body">

        {{-- Barra de botones CRUD según permisos --}}
        <div class="mb-3 d-flex gap-2 flex-wrap">
            @if(!empty($p['bitConsulta']))
            <button class="btn btn-primary btn-sm">
                <i class="bi bi-search me-1"></i>Consultar
            </button>
            @endif
            @if(!empty($p['bitAgregar']))
            <button class="btn btn-success btn-sm">
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
            @if(!empty($p['bitDetalle']))
            <button class="btn btn-info btn-sm">
                <i class="bi bi-eye me-1"></i>Detalle
            </button>
            @endif
        </div>

        <div class="alert alert-info mb-3">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Pantalla estática.</strong> Los botones muestran las acciones según permisos del perfil,
            pero no tienen funcionalidad de base de datos.
        </div>

        {{-- Tabla estática de ejemplo --}}
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Campo 1</th>
                        <th>Campo 2</th>
                        <th>Campo 3</th>
                        <th style="width:150px">Acciones</th>
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
                            @if(!empty($p['bitDetalle']))
                            <button class="btn btn-info btn-sm me-1" title="Detalle">
                                <i class="bi bi-eye"></i>
                            </button>
                            @endif
                            @if(!empty($p['bitEditar']))
                            <button class="btn btn-warning btn-sm me-1" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @endif
                            @if(!empty($p['bitEliminar']))
                            <button class="btn btn-danger btn-sm" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        {{-- Paginación estática de ejemplo --}}
        <div class="d-flex justify-content-between align-items-center mt-2">
            <small class="text-muted">Mostrando 1–5 de 5 registros</small>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled"><a class="page-link" href="#">‹</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item disabled"><a class="page-link" href="#">›</a></li>
                </ul>
            </nav>
        </div>
    </div>
</div>
@endsection
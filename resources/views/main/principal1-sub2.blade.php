{{-- resources/views/main/principal1-sub2.blade.php --}}
@extends('layouts.app')
@section('title','Principal 1.2')
@section('page-title','Principal 1 — Submenú 2')

@section('content')
@php
    use Tymon\JWTAuth\Facades\JWTAuth;
    use App\Models\{PermisoPerfil, Usuario};
    $cu = JWTAuth::parseToken()->authenticate();
    $um = Usuario::with('perfil')->find($cu->id);
    $ea = $um->perfil->bitAdministrador;
    $p  = $ea
        ? ['bitAgregar'=>true,'bitEditar'=>true,'bitEliminar'=>true,'bitConsulta'=>true,'bitDetalle'=>true]
        : (PermisoPerfil::where('idPerfil',$um->idPerfil)->where('idModulo',6)->first()?->toArray() ?? []);
@endphp

<div class="card ">
    <div class="card-header"><i class="bi bi-grid me-2"></i>Principal 1 — Sub 2</div>
    <div class="card-body">
        <div class="mb-3 d-flex gap-2 flex-wrap">
            @if(!empty($p['bitAgregar']))  <button class="btn btn-success btn-sm"><i class="bi bi-plus-lg me-1"></i>Agregar</button>  @endif
            @if(!empty($p['bitEditar']))   <button class="btn btn-warning btn-sm"><i class="bi bi-pencil me-1"></i>Editar</button>    @endif
            @if(!empty($p['bitEliminar'])) <button class="btn btn-danger btn-sm"><i class="bi bi-trash me-1"></i>Eliminar</button>   @endif
            @if(!empty($p['bitConsulta'])) <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Consultar</button> @endif
            @if(!empty($p['bitDetalle']))  <button class="btn btn-info btn-sm"><i class="bi bi-eye me-1"></i>Detalle</button>        @endif
        </div>
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>Pantalla estática — Sub 2 del menú Principal 1.
        </div>
    </div>
</div>
@endsection

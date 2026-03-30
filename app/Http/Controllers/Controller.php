<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth; // Verifica que esta línea exista
use App\Models\Usuario;           // Verifica que apunte a Models
use App\Models\PermisoPerfil;
abstract class Controller
{
 

    // app/Http/Controllers/Controller.php
protected function getPermisos(int $idModulo): array
{
    try {
        $user    = JWTAuth::parseToken()->authenticate();
        $usuario = Usuario::with('perfil')->find($user->id);
        $esAdmin = $usuario?->perfil?->bitAdministrador ?? false;
        if ($esAdmin) return array_fill_keys(['bitAgregar','bitEditar','bitEliminar','bitConsulta','bitDetalle'], true);
        return PermisoPerfil::where('idPerfil', $usuario->idPerfil)
            ->where('idModulo', $idModulo)->first()?->toArray() ?? [];
    } catch (\Exception) { return []; }
}
//
}

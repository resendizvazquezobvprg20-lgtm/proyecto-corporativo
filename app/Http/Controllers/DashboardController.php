<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Usuario;
use App\Models\Menu;
use App\Models\PermisoPerfil;

class DashboardController extends Controller
{
    public static function buildMenu(int $idPerfil, bool $esAdmin): array
    {
        $menus  = Menu::with(['modulos'])->orderBy('intOrden')->get();
        $result = [];

        foreach ($menus as $menu) {
            $submenus = [];

            foreach ($menu->modulos as $modulo) {
                // ✅ La ruta viene del propio modelo, no de un array hardcodeado
                $ruta = $modulo->strRuta ?: '#';

                if ($esAdmin) {
                    $submenus[] = [
                        'id'          => $modulo->id,
                        'nombre'      => $modulo->strNombreModulo,
                        'ruta'        => $ruta,
                        'bitAgregar'  => true,
                        'bitEditar'   => true,
                        'bitConsulta' => true,
                        'bitEliminar' => true,
                        'bitDetalle'  => true,
                    ];
                } else {
                    $permiso = PermisoPerfil::where('idPerfil', $idPerfil)
                        ->where('idModulo', $modulo->id)
                        ->first();

                    if ($permiso && (
                        $permiso->bitAgregar  ||
                        $permiso->bitEditar   ||
                        $permiso->bitConsulta ||
                        $permiso->bitEliminar ||
                        $permiso->bitDetalle
                    )) {
                        $submenus[] = [
                            'id'          => $modulo->id,
                            'nombre'      => $modulo->strNombreModulo,
                            'ruta'        => $ruta,
                            'bitAgregar'  => $permiso->bitAgregar,
                            'bitEditar'   => $permiso->bitEditar,
                            'bitConsulta' => $permiso->bitConsulta,
                            'bitEliminar' => $permiso->bitEliminar,
                            'bitDetalle'  => $permiso->bitDetalle,
                        ];
                    }
                }
            }

            if (!empty($submenus)) {
                $result[] = [
                    'id'       => $menu->id,
                    'nombre'   => $menu->strNombreMenu,
                    'icono'    => $menu->strIcono,
                    'submenus' => $submenus,
                ];
            }
        }

        return $result;
    }

    public function menuJson()
    {
        try {
            $jwtUser = JWTAuth::parseToken()->authenticate();
            $usuario = Usuario::with('perfil')->find($jwtUser->id);
            $esAdmin = $usuario?->perfil?->bitAdministrador ?? false;
            $menus   = self::buildMenu($usuario->idPerfil, $esAdmin);
            return response()->json($menus);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No autenticado.'], 401);
        }
    }

    public function index()
    {
        return view('dashboard', [
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => null],
            ],
        ]);
    }
}
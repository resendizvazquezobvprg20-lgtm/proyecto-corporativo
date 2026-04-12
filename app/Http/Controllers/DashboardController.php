<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Usuario;
use App\Models\Menu;
use App\Models\PermisoPerfil;

class DashboardController extends Controller
{
    private static array $rutasModulo = [
        1 => '/seguridad/perfil',
        2 => '/seguridad/modulo',
        3 => '/seguridad/permisos-perfil',
        4 => '/seguridad/usuario',
        5 => '/principal1/sub1',
        6 => '/principal1/sub2',
        7 => '/principal2/sub1',
        8 => '/principal2/sub2',
    ];

    public static function buildMenu(int $idPerfil, bool $esAdmin): array
    {
        $menus  = Menu::with(['modulos'])->orderBy('intOrden')->get();
        $result = [];

        foreach ($menus as $menu) {
            $submenus = [];

            foreach ($menu->modulos as $modulo) {
                $ruta = self::$rutasModulo[$modulo->id] ?? '#';

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

    /**
     * API JSON — protegida por middleware jwt.
     * El JS del sidebar llama esto con Bearer token en el header.
     */
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

    /**
     * Vista del dashboard — NO valida JWT en servidor.
     * La autenticación la maneja el JS del layout (localStorage + /api/menu).
     * Si el token es inválido, el JS redirige al login automáticamente.
     */
    public function index()
    {
        return view('dashboard', [
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => null],
            ],
        ]);
    }
}

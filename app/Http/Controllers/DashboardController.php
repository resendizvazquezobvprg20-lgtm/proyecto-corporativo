<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Usuario;
use App\Models\Menu;
use App\Models\PermisoPerfil;

class DashboardController extends Controller
{
    // Mapa de id de módulo → ruta URL
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

    /**
     * Construye el menú dinámico basado en los permisos del perfil del usuario.
     * Solo muestra menús/submenús donde el usuario tiene AL MENOS una acción activa.
     */
    public static function buildMenu(int $idPerfil, bool $esAdmin): array
    {
        $menus  = Menu::with(['modulos'])->orderBy('intOrden')->get();
        $result = [];

        foreach ($menus as $menu) {
            $submenus = [];

            foreach ($menu->modulos as $modulo) {
                $ruta = self::$rutasModulo[$modulo->id] ?? '#';

                if ($esAdmin) {
                    // Administrador ve todo
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

                    // Solo incluir si tiene al menos una acción activa
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

            // Solo agregar el menú padre si tiene al menos un submódulo visible
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
     * API: devuelve el menú en JSON para el sidebar dinámico.
     * Protegida por middleware jwt (requiere Bearer token).
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
     * Muestra el dashboard principal
     */
    public function index()
    {
        try {
            $jwtUser = JWTAuth::parseToken()->authenticate();
            $usuario = Usuario::with('perfil')->find($jwtUser->id);
            $esAdmin = $usuario?->perfil?->bitAdministrador ?? false;
            $menus   = self::buildMenu($usuario->idPerfil, $esAdmin);
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Sesión inválida.');
        }

        return view('dashboard', [
            'usuario'     => $usuario,
            'menus'       => $menus,
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => null],
            ],
        ]);
    }
}

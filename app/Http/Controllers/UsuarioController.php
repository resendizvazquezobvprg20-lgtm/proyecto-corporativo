<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Perfil;
use App\Models\EstadoUsuario;
use App\Models\PermisoPerfil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsuarioController extends Controller
{
    public function index()
    {
        try {
            $cu      = JWTAuth::parseToken()->authenticate();
            $um      = Usuario::with('perfil')->find($cu->id);
            $esAdmin = $um?->perfil?->bitAdministrador ?? false;
        } catch (\Exception $e) {
            $um = null; $esAdmin = false;
        }

        $permisos = $esAdmin
            ? ['bitAgregar'=>true,'bitEditar'=>true,'bitEliminar'=>true,'bitConsulta'=>true,'bitDetalle'=>true]
            : ($um
                ? (PermisoPerfil::where('idPerfil', $um->idPerfil)->where('idModulo', 4)->first()?->toArray() ?? [])
                : []);

        return view('security.usuario', [
            'permisos'    => $permisos,
            'perfiles'    => Perfil::orderBy('strNombrePerfil')->get(),
            'estados'     => EstadoUsuario::orderBy('id')->get(),
            'breadcrumbs' => [
                ['label' => 'Inicio',    'url' => route('dashboard')],
                ['label' => 'Seguridad', 'url' => '#'],
                ['label' => 'Usuario',   'url' => null],
            ]
        ]);
    }

    public function list(Request $request)
    {
        $query = Usuario::with(['perfil', 'estadoUsuario']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('strNombreUsuario', 'like', "%$s%")
                  ->orWhere('strCorreo', 'like', "%$s%");
            });
        }

        $usuarios = $query->orderBy('id', 'desc')->paginate(5);

        // Adjuntar URL de imagen a cada usuario
        $usuarios->getCollection()->transform(function ($u) {
            $u->imagen_url = $u->strImagen ? asset('storage/' . $u->strImagen) : null;
            return $u;
        });

        return response()->json($usuarios);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'strNombreUsuario' => 'required|string|max:100|unique:usuarios,strNombreUsuario',
            'idPerfil'         => 'required|exists:perfils,id',
            'strPwd'           => 'required|string|min:8',
            'idEstadoUsuario'  => 'required|exists:estado_usuarios,id',
            'strCorreo'        => 'required|email|max:150|unique:usuarios,strCorreo',
            'strNumeroCelular' => 'nullable|string|max:20',
            'strImagen'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'strNombreUsuario.required' => 'El nombre de usuario es obligatorio.',
            'strNombreUsuario.unique'   => 'Ya existe un usuario con ese nombre.',
            'idPerfil.required'         => 'Debe seleccionar un perfil.',
            'strPwd.required'           => 'La contraseña es obligatoria.',
            'strPwd.min'                => 'La contraseña debe tener al menos 8 caracteres.',
            'strCorreo.required'        => 'El correo es obligatorio.',
            'strCorreo.email'           => 'El correo no tiene un formato válido.',
            'strCorreo.unique'          => 'Ya existe un usuario con ese correo.',
            'strImagen.image'           => 'El archivo debe ser una imagen.',
            'strImagen.mimes'           => 'Solo se permiten imágenes JPEG, PNG o JPG.',
            'strImagen.max'             => 'La imagen no debe superar 2MB.',
        ]);

        // Hash de contraseña
        $validated['strPwd'] = Hash::make($request->strPwd);

        // Subir imagen
        if ($request->hasFile('strImagen')) {
            $validated['strImagen'] = $request->file('strImagen')->store('usuarios', 'public');
        }

        $usuario = Usuario::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado correctamente.',
            'data'    => $usuario->load(['perfil', 'estadoUsuario']),
        ], 201);
    }

    public function show($id)
    {
        $usuario = Usuario::with(['perfil', 'estadoUsuario'])->findOrFail($id);
        $usuario->imagen_url = $usuario->strImagen ? asset('storage/' . $usuario->strImagen) : null;
        return response()->json($usuario);
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $validated = $request->validate([
            'strNombreUsuario' => 'required|string|max:100|unique:usuarios,strNombreUsuario,' . $id,
            'idPerfil'         => 'required|exists:perfils,id',
            'strPwd'           => 'nullable|string|min:8',
            'idEstadoUsuario'  => 'required|exists:estado_usuarios,id',
            'strCorreo'        => 'required|email|max:150|unique:usuarios,strCorreo,' . $id,
            'strNumeroCelular' => 'nullable|string|max:20',
            'strImagen'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'strNombreUsuario.required' => 'El nombre de usuario es obligatorio.',
            'strNombreUsuario.unique'   => 'Ya existe un usuario con ese nombre.',
            'strPwd.min'                => 'La contraseña debe tener al menos 8 caracteres.',
            'strCorreo.required'        => 'El correo es obligatorio.',
            'strCorreo.email'           => 'El correo no tiene un formato válido.',
            'strCorreo.unique'          => 'Ya existe un usuario con ese correo.',
        ]);

        // Actualizar contraseña solo si se envió
        if (!empty($request->strPwd)) {
            $validated['strPwd'] = Hash::make($request->strPwd);
        } else {
            unset($validated['strPwd']);
        }

        // Nueva imagen: eliminar la anterior y guardar la nueva
        if ($request->hasFile('strImagen')) {
            if ($usuario->strImagen && Storage::disk('public')->exists($usuario->strImagen)) {
                Storage::disk('public')->delete($usuario->strImagen);
            }
            $validated['strImagen'] = $request->file('strImagen')->store('usuarios', 'public');
        }

        $usuario->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado correctamente.',
            'data'    => $usuario->load(['perfil', 'estadoUsuario']),
        ]);
    }

    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);

        if ($usuario->strImagen && Storage::disk('public')->exists($usuario->strImagen)) {
            Storage::disk('public')->delete($usuario->strImagen);
        }

        $usuario->delete();
        return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente.']);
    }
}
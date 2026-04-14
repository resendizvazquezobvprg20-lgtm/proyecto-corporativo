<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermisoPerfil;
use App\Models\Perfil;
use App\Models\Modulo;

class PermisoPerfilController extends Controller
{
    public function index()
    {
        return view('security.permiso-perfil', [
            'perfiles' => Perfil::orderBy('strNombrePerfil')->get(),
            'modulos'  => Modulo::orderBy('strNombreModulo')->get(),
            'breadcrumbs' => [
                ['label' => 'Inicio',          'url' => route('dashboard')],
                ['label' => 'Seguridad',       'url' => '#'],
                ['label' => 'Permisos Perfil', 'url' => null],
            ]
        ]);
    }

    public function create()
    {
        return view('security.permiso.create', [
            'perfiles' => Perfil::orderBy('strNombrePerfil')->get(),
            'modulos'  => Modulo::orderBy('strNombreModulo')->get(),
            'breadcrumbs' => [
                ['label' => 'Inicio',          'url' => route('dashboard')],
                ['label' => 'Seguridad',        'url' => '#'],
                ['label' => 'Permisos Perfil',  'url' => route('permiso.index')],
                ['label' => 'Nuevo',            'url' => null],
            ]
        ]);
    }

    public function edit($id)
    {
        return view('security.permiso.edit', [
            'id' => $id,
            'breadcrumbs' => [
                ['label' => 'Inicio',          'url' => route('dashboard')],
                ['label' => 'Seguridad',        'url' => '#'],
                ['label' => 'Permisos Perfil',  'url' => route('permiso.index')],
                ['label' => 'Editar',           'url' => null],
            ]
        ]);
    }

    public function list(Request $request)
    {
        $query = PermisoPerfil::with(['perfil', 'modulo']);
        if ($request->filled('idPerfil')) {
            $query->where('idPerfil', $request->idPerfil);
        }
        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'idPerfil'    => 'required|exists:perfils,id',
            'idModulo'    => 'required|exists:modulos,id',
            'bitAgregar'  => 'boolean',
            'bitEditar'   => 'boolean',
            'bitConsulta' => 'boolean',
            'bitEliminar' => 'boolean',
            'bitDetalle'  => 'boolean',
        ]);

        $permiso = PermisoPerfil::updateOrCreate(
            ['idPerfil' => $request->idPerfil, 'idModulo' => $request->idModulo],
            [
                'bitAgregar'  => $request->boolean('bitAgregar'),
                'bitEditar'   => $request->boolean('bitEditar'),
                'bitConsulta' => $request->boolean('bitConsulta'),
                'bitEliminar' => $request->boolean('bitEliminar'),
                'bitDetalle'  => $request->boolean('bitDetalle'),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Permisos guardados.', 'data' => $permiso->load(['perfil', 'modulo'])], 201);
    }

    public function show($id)
    {
        return response()->json(PermisoPerfil::with(['perfil', 'modulo'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $permiso = PermisoPerfil::findOrFail($id);
        $permiso->update([
            'bitAgregar'  => $request->boolean('bitAgregar'),
            'bitEditar'   => $request->boolean('bitEditar'),
            'bitConsulta' => $request->boolean('bitConsulta'),
            'bitEliminar' => $request->boolean('bitEliminar'),
            'bitDetalle'  => $request->boolean('bitDetalle'),
        ]);
        return response()->json(['success' => true, 'message' => 'Permisos actualizados.', 'data' => $permiso]);
    }

    public function destroy($id)
    {
        PermisoPerfil::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Permiso eliminado.']);
    }
}

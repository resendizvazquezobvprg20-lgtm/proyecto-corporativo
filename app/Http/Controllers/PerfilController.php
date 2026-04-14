<?php

namespace App\Http\Controllers;

use App\Models\Perfil;
use Illuminate\Http\Request;

class PerfilController extends Controller
{
    public function index()
    {
        return view('security.perfil', [
            'breadcrumbs' => [
                ['label' => 'Inicio',    'url' => route('dashboard')],
                ['label' => 'Seguridad', 'url' => '#'],
                ['label' => 'Perfil',    'url' => null],
            ]
        ]);
    }

    public function create()
    {
        return view('security.perfil.create', [
            'breadcrumbs' => [
                ['label' => 'Inicio',    'url' => route('dashboard')],
                ['label' => 'Seguridad', 'url' => '#'],
                ['label' => 'Perfil',    'url' => route('perfil.index')],
                ['label' => 'Nuevo',     'url' => null],
            ]
        ]);
    }

    public function edit($id)
    {
        return view('security.perfil.edit', [
            'id' => $id,
            'breadcrumbs' => [
                ['label' => 'Inicio',    'url' => route('dashboard')],
                ['label' => 'Seguridad', 'url' => '#'],
                ['label' => 'Perfil',    'url' => route('perfil.index')],
                ['label' => 'Editar',    'url' => null],
            ]
        ]);
    }

    public function list(Request $request)
    {
        $query = Perfil::query();
        if ($request->filled('search')) {
            $query->where('strNombrePerfil', 'like', '%' . $request->search . '%');
        }
        return response()->json($query->orderBy('id', 'desc')->paginate(5));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'strNombrePerfil'  => 'required|string|max:100|unique:perfils,strNombrePerfil',
            'bitAdministrador' => 'required|boolean',
        ], [
            'strNombrePerfil.required' => 'El nombre del perfil es obligatorio.',
            'strNombrePerfil.unique'   => 'Ya existe un perfil con ese nombre.',
        ]);

        $perfil = Perfil::create($validated);
        return response()->json(['success' => true, 'message' => 'Perfil creado correctamente.', 'data' => $perfil], 201);
    }

    public function show($id)
    {
        return response()->json(Perfil::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $perfil    = Perfil::findOrFail($id);
        $validated = $request->validate([
            'strNombrePerfil'  => 'required|string|max:100|unique:perfils,strNombrePerfil,' . $id,
            'bitAdministrador' => 'required|boolean',
        ], [
            'strNombrePerfil.required' => 'El nombre del perfil es obligatorio.',
            'strNombrePerfil.unique'   => 'Ya existe un perfil con ese nombre.',
        ]);

        $perfil->update($validated);
        return response()->json(['success' => true, 'message' => 'Perfil actualizado correctamente.', 'data' => $perfil]);
    }

    public function destroy($id)
    {
        $perfil = Perfil::findOrFail($id);

        if ($perfil->usuarios()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar: el perfil tiene usuarios asignados.',
            ], 409);
        }

        $perfil->delete();
        return response()->json(['success' => true, 'message' => 'Perfil eliminado correctamente.']);
    }
}

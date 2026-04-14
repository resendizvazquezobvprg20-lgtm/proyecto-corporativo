<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Modulo;
use App\Models\MenuModulo;
use Illuminate\Http\Request;

class ModuloController extends Controller
{
    public function index()
    {
        return view('security.modulo', [
            'breadcrumbs' => [
                ['label' => 'Inicio',    'url' => route('dashboard')],
                ['label' => 'Seguridad', 'url' => '#'],
                ['label' => 'Módulo',    'url' => null],
            ]
        ]);
    }

    public function create()
    {
        return view('security.modulo.create', [
            'menus' => Menu::orderBy('intOrden')->get(),
            'breadcrumbs' => [
                ['label' => 'Inicio',    'url' => route('dashboard')],
                ['label' => 'Seguridad', 'url' => '#'],
                ['label' => 'Módulo',    'url' => route('modulo.index')],
                ['label' => 'Nuevo',     'url' => null],
            ]
        ]);
    }

    public function edit($id)
    {
        return view('security.modulo.edit', [
            'id'   => $id,
            'menus' => Menu::orderBy('intOrden')->get(),
            'breadcrumbs' => [
                ['label' => 'Inicio',    'url' => route('dashboard')],
                ['label' => 'Seguridad', 'url' => '#'],
                ['label' => 'Módulo',    'url' => route('modulo.index')],
                ['label' => 'Editar',    'url' => null],
            ]
        ]);
    }

    public function list(Request $request)
    {
        $query = Modulo::with('menu');
        if ($request->filled('search')) {
            $query->where('strNombreModulo', 'like', '%' . $request->search . '%');
        }
        return response()->json($query->orderBy('id', 'desc')->paginate(5));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'strNombreModulo' => 'required|string|max:100|unique:modulos,strNombreModulo',
            'strRuta'         => 'nullable|string|max:150',
            'idMenu'          => 'nullable|exists:menus,id',
        ], [
            'strNombreModulo.required' => 'El nombre del módulo es obligatorio.',
            'strNombreModulo.unique'   => 'Ya existe un módulo con ese nombre.',
        ]);

        $modulo = Modulo::create($validated);

        // ✅ Vincular automáticamente al menú seleccionado en menu_modulos
        if (!empty($validated['idMenu'])) {
            MenuModulo::firstOrCreate([
                'idMenu'   => $validated['idMenu'],
                'idModulo' => $modulo->id,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Módulo creado.', 'data' => $modulo], 201);
    }

    public function show($id)
    {
        return response()->json(Modulo::with('menu')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $modulo    = Modulo::findOrFail($id);
        $validated = $request->validate([
            'strNombreModulo' => 'required|string|max:100|unique:modulos,strNombreModulo,' . $id,
            'strRuta'         => 'nullable|string|max:150',
            'idMenu'          => 'nullable|exists:menus,id',
        ]);

        $modulo->update($validated);

        // ✅ Actualizar vínculo en menu_modulos
        MenuModulo::where('idModulo', $modulo->id)->delete();
        if (!empty($validated['idMenu'])) {
            MenuModulo::create([
                'idMenu'   => $validated['idMenu'],
                'idModulo' => $modulo->id,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Módulo actualizado.', 'data' => $modulo]);
    }

    public function destroy($id)
    {
        $modulo = Modulo::findOrFail($id);
        // Limpiar menu_modulos antes de eliminar
        MenuModulo::where('idModulo', $id)->delete();
        $modulo->delete();
        return response()->json(['success' => true, 'message' => 'Módulo eliminado.']);
    }
}
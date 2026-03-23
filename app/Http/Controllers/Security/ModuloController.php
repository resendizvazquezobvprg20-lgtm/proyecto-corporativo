<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modulo;
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
 
    public function list(Request $request)
    {
        $query = Modulo::query();
 
        if ($request->filled('search')) {
            $query->where('strNombreModulo', 'like', '%' . $request->search . '%');
        }
 
        return response()->json($query->orderBy('id', 'desc')->paginate(5));
    }
 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'strNombreModulo' => 'required|string|max:100|unique:modulo,strNombreModulo',
        ], [
            'strNombreModulo.required' => 'El nombre del módulo es obligatorio.',
            'strNombreModulo.unique'   => 'Ya existe un módulo con ese nombre.',
        ]);
 
        $modulo = Modulo::create($validated);
 
        return response()->json(['success' => true, 'message' => 'Módulo creado.', 'data' => $modulo], 201);
    }
 
    public function show($id)
    {
        return response()->json(Modulo::findOrFail($id));
    }
 
    public function update(Request $request, $id)
    {
        $modulo    = Modulo::findOrFail($id);
        $validated = $request->validate([
            'strNombreModulo' => 'required|string|max:100|unique:modulo,strNombreModulo,' . $id,
        ]);
        $modulo->update($validated);
        return response()->json(['success' => true, 'message' => 'Módulo actualizado.', 'data' => $modulo]);
    }
 
    public function destroy($id)
    {
        Modulo::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Módulo eliminado.']);
    }
}
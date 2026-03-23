<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermisoPerfil extends Model
{
    protected $table = 'permisos_perfil';
    protected $fillable = [
        'idModulo', 'idPerfil',
        'bitAgregar', 'bitEditar', 'bitConsulta', 'bitEliminar', 'bitDetalle'
    ];
 
    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'idModulo');
    }
 
    public function perfil()
    {
        return $this->belongsTo(Perfil::class, 'idPerfil');
    }
}
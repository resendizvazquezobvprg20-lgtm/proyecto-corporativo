<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    protected $table = 'perfils';
    protected $fillable = ['strNombrePerfil', 'bitAdministrador'];
 
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'idPerfil');
    }
 
    public function permisos()
    {
        return $this->hasMany(PermisoPerfil::class, 'idPerfil');
    }
}

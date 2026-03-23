<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    protected $table = 'modulo';
    protected $fillable = ['strNombreModulo'];
 
    public function permisos()
    {
        return $this->hasMany(PermisoPerfil::class, 'idModulo');
    }
 
    public function menuModulos()
    {
        return $this->hasMany(MenuModulo::class, 'idModulo');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    protected $table = 'modulos';
    protected $fillable = ['strNombreModulo', 'strRuta', 'idMenu'];

    public function permisos()
    {
        return $this->hasMany(PermisoPerfil::class, 'idModulo');
    }

    public function menuModulos()
    {
        return $this->hasMany(MenuModulo::class, 'idModulo');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'idMenu');
    }
}
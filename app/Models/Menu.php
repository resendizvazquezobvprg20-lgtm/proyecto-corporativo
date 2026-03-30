<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';
    protected $fillable = ['strNombreMenu', 'strIcono', 'intOrden'];
 
    public function menuModulos()
    {
        return $this->hasMany(MenuModulo::class, 'idMenu');
    }
 
    public function modulos()
    {
        return $this->belongsToMany(Modulo::class, 'menu_modulo', 'idMenu', 'idModulo');
    }
}

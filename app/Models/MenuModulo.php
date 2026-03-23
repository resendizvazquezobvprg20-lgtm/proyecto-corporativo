<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuModulo extends Model
{
    protected $table = 'menu_modulo';
    protected $fillable = ['idMenu', 'idModulo'];
 
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'idMenu');
    }
 
    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'idModulo');
    }
}
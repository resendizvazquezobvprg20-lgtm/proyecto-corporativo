<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoUsuario extends Model
{
    protected $table = 'estado_usuario';
    public $timestamps = false;
    protected $fillable = ['strNombre'];
}
 
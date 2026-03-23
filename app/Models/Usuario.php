<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Usuario extends Authenticatable implements JWTSubject
{
    protected $table = 'usuario';
    protected $fillable = [
        'strNombreUsuario', 'idPerfil', 'strPwd',
        'idEstadoUsuario', 'strCorreo', 'strNumeroCelular', 'strImagen'
    ];
    protected $hidden = ['strPwd'];
 
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
 
    public function getJWTCustomClaims(): array
    {
        return [
            'nombre' => $this->strNombreUsuario,
            'perfil' => $this->idPerfil,
            'correo' => $this->strCorreo,
        ];
    }
 
    public function getAuthPassword()
    {
        return $this->strPwd;
    }
 
    public function perfil()
    {
        return $this->belongsTo(Perfil::class, 'idPerfil');
    }
 
    public function estadoUsuario()
    {
        return $this->belongsTo(EstadoUsuario::class, 'idEstadoUsuario');
    }
}
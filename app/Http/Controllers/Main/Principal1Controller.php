<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;

class Principal1Controller extends Controller
{
    public function sub1()
    {
        return view('main.principal1-sub1', [
            'breadcrumbs' => [
                ['label' => 'Inicio',    'url' => route('dashboard')],
                ['label' => 'Comercial', 'url' => '#'],
                ['label' => 'Ventas',    'url' => null],
            ]
        ]);
    }

    public function sub2()
    {
        return view('main.principal1-sub2', [
            'breadcrumbs' => [
                ['label' => 'Inicio',    'url' => route('dashboard')],
                ['label' => 'Comercial', 'url' => '#'],
                ['label' => 'Clientes',  'url' => null],
            ]
        ]);
    }
}

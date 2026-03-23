<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;

class Principal2Controller extends Controller
{
    public function sub1()
    {
        return view('main.principal2-sub1', [
            'breadcrumbs' => [
                ['label' => 'Inicio',      'url' => route('dashboard')],
                ['label' => 'Principal 2', 'url' => '#'],
                ['label' => 'Sub 1',       'url' => null],
            ]
        ]);
    }

    public function sub2()
    {
        return view('main.principal2-sub2', [
            'breadcrumbs' => [
                ['label' => 'Inicio',      'url' => route('dashboard')],
                ['label' => 'Principal 2', 'url' => '#'],
                ['label' => 'Sub 2',       'url' => null],
            ]
        ]);
    }
}

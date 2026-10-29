<?php

namespace App\Controllers;

class HolaMundo extends BaseController
{
    public function index(): string
    {
        $data = [
            'titulo'  => 'Hola Mundo',
            'mensaje' => '¡Hola Mundo desde CodeIgniter 4!',
            'sistema' => 'Sistema de Registro de Vacaciones',
        ];

        return view('hola_mundo', $data);
    }
}

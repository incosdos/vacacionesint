<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/** Para login y registro: si ya hay sesión, envía al panel. */
class InvitadoFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session('idusuario')) {
            return redirect()->to(site_url('panel'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}

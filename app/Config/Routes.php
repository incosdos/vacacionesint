<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Página principal / presentación de la empresa
$routes->get('/', 'Inicio::index');

// Acceso y registro (solo para quienes no han iniciado sesión)
$routes->group('', ['filter' => 'invitado'], static function ($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::autenticar');

    $routes->get('registro', 'Registro::index');
    $routes->post('registro', 'Registro::guardar');
    $routes->get('registro/verificar/(:segment)', 'Registro::verificar/$1');
    $routes->get('registro/reenviar', 'Registro::reenviarForm');
    $routes->post('registro/reenviar', 'Registro::reenviar');
});

$routes->get('salir', 'Auth::salir');

// Área privada
$routes->group('panel', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Panel::index', ['filter' => 'permiso:panel.ver']);

    // Mi perfil
    $routes->get('perfil', 'Perfil::index', ['filter' => 'permiso:perfil.editar']);
    $routes->post('perfil', 'Perfil::actualizar', ['filter' => 'permiso:perfil.editar']);
    $routes->post('perfil/password', 'Perfil::cambiarPassword', ['filter' => 'permiso:perfil.editar']);

    // Vacaciones propias
    $routes->get('vacaciones', 'Vacaciones::index', ['filter' => 'permiso:vacaciones.ver_propias']);
    $routes->get('vacaciones/solicitar', 'Vacaciones::solicitar', ['filter' => 'permiso:vacaciones.solicitar']);
    $routes->post('vacaciones', 'Vacaciones::guardar', ['filter' => 'permiso:vacaciones.solicitar']);
    $routes->post('vacaciones/(:num)/cancelar', 'Vacaciones::cancelar/$1', ['filter' => 'permiso:vacaciones.solicitar,vacaciones.ver_propias']);

    // Solicitudes del personal (bandeja del coordinador)
    $routes->get('solicitudes', 'Solicitudes::index', ['filter' => 'permiso:vacaciones.ver_todas,vacaciones.autorizar']);
    $routes->post('solicitudes/(:num)/resolver', 'Solicitudes::resolver/$1', ['filter' => 'permiso:vacaciones.autorizar']);

    // Reportes
    $routes->get('reportes', 'Reportes::index', ['filter' => 'permiso:reportes.ver']);
    $routes->get('reportes/exportar', 'Reportes::exportar', ['filter' => 'permiso:reportes.ver']);

    // Usuarios: consulta (usuarios.ver o usuarios.gestionar) y gestión (usuarios.gestionar)
    $routes->group('usuarios', static function ($routes) {
        $ver       = ['filter' => 'permiso:usuarios.ver,usuarios.gestionar'];
        $gestionar = ['filter' => 'permiso:usuarios.gestionar'];

        $routes->get('/', 'Usuarios::index', $ver);
        $routes->get('nuevo', 'Usuarios::nuevo', $gestionar);
        $routes->post('/', 'Usuarios::crear', $gestionar);
        $routes->get('(:num)', 'Usuarios::ver/$1', $ver);
        $routes->get('(:num)/editar', 'Usuarios::editar/$1', $gestionar);
        $routes->post('(:num)', 'Usuarios::actualizar/$1', $gestionar);
        $routes->post('(:num)/rol', 'Usuarios::asignarRol/$1', $gestionar);
        $routes->post('(:num)/estado', 'Usuarios::cambiarEstado/$1', $gestionar);
        $routes->post('(:num)/reenviar', 'Usuarios::reenviarVerificacion/$1', $gestionar);
        $routes->post('(:num)/eliminar', 'Usuarios::eliminar/$1', $gestionar);
    });

    // Cargos (solo Administrador: permiso cargos.gestionar)
    $routes->group('cargos', ['filter' => 'permiso:cargos.gestionar'], static function ($routes) {
        $routes->get('/', 'Cargos::index');
        $routes->get('nuevo', 'Cargos::nuevo');
        $routes->post('/', 'Cargos::crear');
        $routes->get('(:num)/editar', 'Cargos::editar/$1');
        $routes->post('(:num)', 'Cargos::actualizar/$1');
        $routes->post('(:num)/estado', 'Cargos::cambiarEstado/$1');
        $routes->post('(:num)/eliminar', 'Cargos::eliminar/$1');
    });

    // Feriados y días no laborables (solo Administrador: permiso feriados.gestionar)
    $routes->group('feriados', ['filter' => 'permiso:feriados.gestionar'], static function ($routes) {
        $routes->get('/', 'Feriados::index');
        $routes->get('nuevo', 'Feriados::nuevo');
        $routes->post('/', 'Feriados::crear');
        $routes->post('generar', 'Feriados::generar');
        $routes->get('(:num)/editar', 'Feriados::editar/$1');
        $routes->post('(:num)', 'Feriados::actualizar/$1');
        $routes->post('(:num)/eliminar', 'Feriados::eliminar/$1');
    });

    // Roles y permisos (solo Administrador: permiso roles.gestionar)
    $routes->group('roles', ['filter' => 'permiso:roles.gestionar'], static function ($routes) {
        $routes->get('/', 'Roles::index');
        $routes->get('nuevo', 'Roles::nuevo');
        $routes->post('/', 'Roles::crear');
        $routes->get('matriz', 'Roles::matriz');
        $routes->post('matriz', 'Roles::guardarMatriz');
        $routes->get('(:num)/editar', 'Roles::editar/$1');
        $routes->post('(:num)', 'Roles::actualizar/$1');
        $routes->post('(:num)/eliminar', 'Roles::eliminar/$1');
    });
});

// Página de prueba
$routes->get('holamundo', 'HolaMundo::index');

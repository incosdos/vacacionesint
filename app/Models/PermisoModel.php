<?php

namespace App\Models;

use CodeIgniter\Model;

class PermisoModel extends Model
{
    protected $table         = 'permisos';
    protected $primaryKey    = 'idpermiso';
    protected $returnType    = 'array';
    protected $allowedFields = ['clave', 'descripcion'];

    /** Nombre visible de cada módulo (prefijo de la clave antes del punto). */
    public const MODULOS = [
        'panel'      => ['General', 'bi-grid'],
        'perfil'     => ['General', 'bi-grid'],
        'vacaciones' => ['Vacaciones', 'bi-calendar2-week'],
        'reportes'   => ['Reportes', 'bi-bar-chart'],
        'usuarios'   => ['Usuarios', 'bi-people'],
        'roles'      => ['Seguridad', 'bi-shield-lock'],
        'cargos'     => ['Organización', 'bi-building'],
        'feriados'   => ['Organización', 'bi-building'],
    ];

    /**
     * Claves de permiso asignadas a un rol.
     *
     * @return list<string>
     */
    public function clavesPorRol(int $idrol): array
    {
        $filas = $this->select('permisos.clave')
            ->join('rol_permiso rp', 'rp.idpermiso = permisos.idpermiso')
            ->where('rp.idrol', $idrol)
            ->orderBy('permisos.clave')
            ->findAll();

        return array_column($filas, 'clave');
    }

    /**
     * Permisos agrupados por módulo, en un orden estable.
     *
     * @return array<string, array{icono: string, permisos: list<array>}>
     */
    public function agrupados(): array
    {
        $grupos = [];
        foreach ($this->orderBy('idpermiso')->findAll() as $p) {
            $prefijo          = strstr($p['clave'], '.', true) ?: $p['clave'];
            [$nombre, $icono] = self::MODULOS[$prefijo] ?? [ucfirst($prefijo), 'bi-key'];
            $grupos[$nombre]['icono']      = $icono;
            $grupos[$nombre]['permisos'][] = $p;
        }

        return $grupos;
    }
}

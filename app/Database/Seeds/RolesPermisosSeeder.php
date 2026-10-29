<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolesPermisosSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            1 => ['Administrador', 'Control total del sistema: usuarios, roles, permisos y vacaciones.'],
            2 => ['Coordinador',   'Revisa y autoriza o rechaza las vacaciones de los colaboradores.'],
            3 => ['Colaborador',   'Registra y consulta sus propias solicitudes de vacaciones.'],
            4 => ['Invitado',      'Usuario recién registrado. Solo accede a su perfil hasta que se le asigne un rol.'],
        ];
        foreach ($roles as $id => [$nombre, $desc]) {
            $this->db->table('roles')->insert(['idrol' => $id, 'nombre' => $nombre, 'descripcion' => $desc]);
        }

        $permisos = [
            'panel.ver'              => 'Acceder al panel principal',
            'perfil.editar'          => 'Ver y editar su propio perfil',
            'vacaciones.solicitar'   => 'Registrar solicitudes de vacaciones',
            'vacaciones.ver_propias' => 'Ver sus propias solicitudes de vacaciones',
            'vacaciones.ver_todas'   => 'Ver las vacaciones de todos los colaboradores',
            'vacaciones.autorizar'   => 'Autorizar o rechazar solicitudes de vacaciones',
            'reportes.ver'           => 'Ver reportes de vacaciones',
            'usuarios.ver'           => 'Ver la lista de usuarios',
            'usuarios.gestionar'     => 'Crear, editar, activar y desactivar usuarios',
            'roles.gestionar'        => 'Administrar roles y permisos',
            'cargos.gestionar'       => 'Administrar el catálogo de cargos',
            'feriados.gestionar'     => 'Administrar feriados y días no laborables',
        ];
        $ids = [];
        foreach ($permisos as $clave => $desc) {
            $existente = $this->db->table('permisos')->where('clave', $clave)->get()->getRowArray();
            if ($existente) {
                $ids[$clave] = (int) $existente['idpermiso'];
                continue;
            }
            $this->db->table('permisos')->insert(['clave' => $clave, 'descripcion' => $desc]);
            $ids[$clave] = $this->db->insertID();
        }

        $asignacion = [
            1 => array_keys($permisos), // Administrador: todos
            2 => ['panel.ver', 'perfil.editar', 'vacaciones.solicitar', 'vacaciones.ver_propias',
                   'vacaciones.ver_todas', 'vacaciones.autorizar', 'reportes.ver', 'usuarios.ver'],
            3 => ['panel.ver', 'perfil.editar', 'vacaciones.solicitar', 'vacaciones.ver_propias'],
            4 => ['panel.ver', 'perfil.editar'],
        ];
        foreach ($asignacion as $idrol => $claves) {
            foreach ($claves as $c) {
                $this->db->table('rol_permiso')->insert(['idrol' => $idrol, 'idpermiso' => $ids[$c]]);
            }
        }
    }
}

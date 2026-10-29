<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

/**
 * Cargos (puestos de trabajo) y su relación con usuarios.
 * Agrega también el permiso cargos.gestionar (solo Administrador).
 */
class CrearCargos extends Migration
{
    public const CARGOS = [
        'Administrador'             => 'Administración general de la oficina.',
        'Contador'                  => 'Contabilidad, impuestos y estados financieros.',
        'Responsable'               => 'Responsable de un área o proceso.',
        'Supervisor'                => 'Supervisión del trabajo y la calidad de la producción.',
        'Diseñador Gráfico'         => 'Diseño de piezas gráficas, anuncios y material publicitario.',
        'Desarrollador Jr'          => 'Desarrollo de software, nivel junior.',
        'Desarrollador Semi Senior' => 'Desarrollo de software, nivel semi senior.',
        'Desarrollador Senior'      => 'Desarrollo de software, nivel senior.',
        'Mensajero'                 => 'Entrega de documentos y trámites externos.',
        'Asistente de Gerencia'     => 'Apoyo administrativo a la gerencia.',
        'Gerencia'                  => 'Dirección general de la empresa.',
    ];

    public function up()
    {
        // 1. Tabla de cargos
        $this->forge->addField([
            'idcargo'     => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nombre'      => ['type' => 'VARCHAR', 'constraint' => 80],
            'descripcion' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'estado'      => ['type' => 'BOOLEAN', 'default' => 1, 'comment' => '1 = activo, 0 = inactivo'],
            'created_at'  => ['type' => 'DATETIME', 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('idcargo');
        $this->forge->addUniqueKey('nombre', 'uq_cargos_nombre');
        $this->forge->createTable('cargos', true, ['ENGINE' => 'InnoDB', 'COMMENT' => 'Cargos o puestos de trabajo']);

        foreach (self::CARGOS as $nombre => $desc) {
            $this->db->table('cargos')->insert(['nombre' => $nombre, 'descripcion' => $desc]);
        }

        // 2. Relación usuarios -> cargos (opcional: un usuario puede no tener cargo aún)
        $this->forge->addColumn('usuarios', [
            'idcargo' => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'idrol', 'comment' => 'Cargo del usuario'],
        ]);
        $this->forge->addForeignKey('idcargo', 'cargos', 'idcargo', 'CASCADE', 'RESTRICT', 'fk_usuarios_cargo');
        $this->forge->processIndexes('usuarios');

        // 3. Permiso para administrar cargos (si los roles ya existen; en instalación nueva lo crea el seeder)
        if ($this->db->table('roles')->where('idrol', 1)->countAllResults() > 0
            && $this->db->table('permisos')->where('clave', 'cargos.gestionar')->countAllResults() === 0) {
            $this->db->table('permisos')->insert([
                'clave'       => 'cargos.gestionar',
                'descripcion' => 'Administrar el catálogo de cargos',
            ]);
            $this->db->table('rol_permiso')->insert(['idrol' => 1, 'idpermiso' => $this->db->insertID()]);
        }
    }

    public function down()
    {
        $this->forge->dropForeignKey('usuarios', 'fk_usuarios_cargo');
        $this->forge->dropColumn('usuarios', 'idcargo');
        $this->db->table('permisos')->where('clave', 'cargos.gestionar')->delete();
        $this->forge->dropTable('cargos', true);
    }
}

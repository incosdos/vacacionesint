<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearPermisos extends Migration
{
    public function up()
    {
        // Catálogo de permisos
        $this->forge->addField([
            'idpermiso'   => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'clave'       => ['type' => 'VARCHAR', 'constraint' => 60],
            'descripcion' => ['type' => 'VARCHAR', 'constraint' => 255],
        ]);
        $this->forge->addPrimaryKey('idpermiso');
        $this->forge->addUniqueKey('clave');
        $this->forge->createTable('permisos', true, ['ENGINE' => 'InnoDB', 'COMMENT' => 'Permisos del sistema']);

        // Relación muchos a muchos rol <-> permiso
        $this->forge->addField([
            'idrol'     => ['type' => 'INT', 'unsigned' => true],
            'idpermiso' => ['type' => 'INT', 'unsigned' => true],
        ]);
        $this->forge->addPrimaryKey(['idrol', 'idpermiso']);
        $this->forge->addForeignKey('idrol', 'roles', 'idrol', 'CASCADE', 'CASCADE', 'fk_rolpermiso_rol');
        $this->forge->addForeignKey('idpermiso', 'permisos', 'idpermiso', 'CASCADE', 'CASCADE', 'fk_rolpermiso_permiso');
        $this->forge->createTable('rol_permiso', true, ['ENGINE' => 'InnoDB', 'COMMENT' => 'Permisos asignados a cada rol']);
    }

    public function down()
    {
        $this->forge->dropTable('rol_permiso', true);
        $this->forge->dropTable('permisos', true);
    }
}

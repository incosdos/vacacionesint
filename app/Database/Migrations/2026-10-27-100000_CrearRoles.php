<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CrearRoles extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'idrol'       => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nombre'      => ['type' => 'VARCHAR', 'constraint' => 50],
            'descripcion' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'default' => new RawSql('CURRENT_TIMESTAMP')],
        ]);
        $this->forge->addPrimaryKey('idrol');
        $this->forge->addUniqueKey('nombre');
        $this->forge->createTable('roles', true, ['ENGINE' => 'InnoDB', 'COMMENT' => 'Roles del sistema']);
    }

    public function down()
    {
        $this->forge->dropTable('roles', true);
    }
}

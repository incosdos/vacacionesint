<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CrearUsuarios extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'idusuario'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nombres'             => ['type' => 'VARCHAR', 'constraint' => 100],
            'apellidos'           => ['type' => 'VARCHAR', 'constraint' => 100],
            'ci'                  => ['type' => 'VARCHAR', 'constraint' => 20, 'comment' => 'Cédula de identidad'],
            'email'               => ['type' => 'VARCHAR', 'constraint' => 150],
            'celular'             => ['type' => 'BIGINT', 'unsigned' => true],
            'idrol'               => ['type' => 'INT', 'unsigned' => true],
            'password'            => ['type' => 'VARCHAR', 'constraint' => 255, 'comment' => 'Hash bcrypt'],
            'estado'              => ['type' => 'BOOLEAN', 'default' => 0, 'comment' => '1 = activo, 0 = inactivo'],
            'email_verificado_en' => ['type' => 'DATETIME', 'null' => true],
            'token_verificacion'  => ['type' => 'CHAR', 'constraint' => 64, 'null' => true, 'comment' => 'SHA-256 del token enviado por correo'],
            'token_expira'        => ['type' => 'DATETIME', 'null' => true],
            'ultimo_acceso'       => ['type' => 'DATETIME', 'null' => true],
            'fechaderegistro'     => ['type' => 'DATETIME', 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('idusuario');
        $this->forge->addUniqueKey('ci', 'uq_usuarios_ci');
        $this->forge->addUniqueKey('email', 'uq_usuarios_email');
        $this->forge->addKey('token_verificacion', false, false, 'ix_usuarios_token');
        $this->forge->addForeignKey('idrol', 'roles', 'idrol', 'CASCADE', 'RESTRICT', 'fk_usuarios_rol');
        $this->forge->createTable('usuarios', true, ['ENGINE' => 'InnoDB', 'COMMENT' => 'Usuarios del sistema']);
    }

    public function down()
    {
        $this->forge->dropTable('usuarios', true);
    }
}

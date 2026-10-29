<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Crea el usuario Administrador inicial.
 * IMPORTANTE: cambie el correo y la contraseña después del primer ingreso.
 */
class AdminSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('usuarios')->insert([
            'nombres'             => 'Administrador',
            'apellidos'           => 'del Sistema',
            'ci'                  => '0000000',
            'email'               => 'admin@intergraphics.local',
            'celular'             => 70000000,
            'idrol'               => 1,
            'password'            => password_hash('Admin2026*', PASSWORD_DEFAULT),
            'estado'              => 1,
            'email_verificado_en' => date('Y-m-d H:i:s'),
        ]);
    }
}

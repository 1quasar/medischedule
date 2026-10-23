<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Administrador principal
        User::updateOrCreate(
            ['email' => 'admin@medischedule.test'],
            [
                'name' => 'Administrador',
                'password' => 'password',
                'cpf' => '999.999.997-97',
                'phone' => '(24) 98866-7755',
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'paciente@medischedule.test'],
            [
                'name' => 'Paciente de Teste',
                'password' => 'password',
                'cpf' => '888.888.987-87',
                'phone' => '(24) 98866-7575',
                'role' => 'patient',
            ]
        );
    }
}

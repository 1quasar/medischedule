<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Executar os seeders das entidades base na ordem correta
        $this->call([
            SpecialtySeeder::class,
            DoctorSeeder::class,
            UserSeeder::class,
        ]); 
    }
}
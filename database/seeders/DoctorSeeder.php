<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Doctor;
use App\Models\Specialty;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = [
            [
                'specialty_id' => 1,
                'name' => 'Dr. Carlos',
                'crm' => '148920/SP',
                'phone' => '(11) 99999-8888',
                'work_start_time' => '08:00',
                'work_end_time' => '14:00',
                'slot_duration' => 30,
                'is_active' => true,
            ],

            [
                'specialty_id' => 2,
                'name' => 'Dra. Aline',
                'crm' => '78341/SP',
                'phone' => '(11) 99988-8877',
                'work_start_time' => '10:00',
                'work_end_time' => '16:00',
                'slot_duration' => 30,
                'is_active' => true,
            ],

            [
                'specialty_id' => 3,
                'name' => 'Dr. João',
                'crm' => '63412/RJ',
                'phone' => '(24) 98888-7777',
                'work_start_time' => '10:00',
                'work_end_time' => '17:00',
                'slot_duration' => 30,
                'is_active' => true,
            ],

            [
                'specialty_id' => 4,
                'name' => 'Dr. Rodrigo',
                'crm' => '81290/RJ',
                'phone' => '(21) 99988-9898',
                'work_start_time' => '10:00',
                'work_end_time' => '18:00',
                'slot_duration' => 40,
                'is_active' => true,
            ],

            [
                'specialty_id' => 5,
                'name' => 'Dra. Luiza',
                'crm' => '95210/MG',
                'phone' => '(31) 99877-9797',
                'work_start_time' => '11:00',
                'work_end_time' => '18:00',
                'slot_duration' => 30,
                'is_active' => true,
            ],
        ];

        foreach ($doctors as $doctor) {
            Doctor::updateOrCreate(
                ['crm' => $doctor['crm']],
                [
                    'specialty_id'    => $doctor['specialty_id'],
                    'name'            => $doctor['name'],
                    'phone'           => $doctor['phone'],
                    'work_start_time' => $doctor['work_start_time'],
                    'work_end_time'   => $doctor['work_end_time'],
                    'slot_duration'   => $doctor['slot_duration'],
                    'is_active'       => $doctor['is_active'],
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Specialty;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = [
            [
                'name' => 'Cardiologia',
                'description' => 'Especialidade médica que se ocupa do diagnóstico e tratamento das doenças que acometem o coração bem como os outros componentes do sistema circulatório. O médico especialista nessa área é o cardiologista.',
            ],
            [
                'name' => 'Dermatologia',
                'description' => 'Especialidade médica que se ocupa do diagnóstico e tratamento clínico-cirúrgico das enfermidades relacionados à pele e aos anexos cutâneos (unhas, pelos, mucosas, cabelos). Dentro da dermatologia, existe a dermatovenerologia, especialidade que tem importante atuação no contexto das infecções sexualmente transmissíveis.',
            ],
            [
                'name' => 'Ortopedia',
                'description' => 'Especialidade médica responsável pelo diagnóstico, tratamento e prevenção de doenças e lesões que afetam o sistema musculoesquelético, incluindo ossos, músculos, ligamentos e articulações. No Brasil, as especialidades são unificadas, recebendo o nome de "Ortopedia e Traumatologia". A traumatologia é a especialidade que trata especificamente dos traumas e lesões do aparelho musculoesquelético.',
            ],
            [
                'name' => 'Pediatria',
                'description' => 'Especialidade médica dedicada à assistência à criança e ao adolescente (sendo majoritariamente para crianças, com próprios especialistas para indivíduos na puberdade, vide hebiatria), nos seus diversos aspectos, sejam eles preventivos ou curativos.',
            ],
            [
                'name' => 'Ginecologia',
                'description' => 'Área de prática da medicina que envolve o cuidado e tratamento de doenças diretamente relacionadas com a saúde do aparelho reprodutor feminino (vagina, útero e ovários) e mamas.',
            ],
        ];

        foreach ($specialties as $specialty) {
            Specialty::updateOrCreate(
                ['name' => $specialty['name']],
                ['description' => $specialty['description']]
            );
        }
    }
}

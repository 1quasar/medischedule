<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $doctor = $this->route('doctor');
        $userId = $doctor ? $doctor->user_id : null;
        $doctorId = $doctor ? $doctor->id : null;

        return [
            // Dados da tabela 'users'
            'name'            => ['required', 'string', 'max:255'],
            'email'           => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password'        => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'string',
                'min:8',
            ],
            'cpf'             => [
                'required',
                'string',
                Rule::unique('users', 'cpf')->ignore($userId),
            ],

            // Dados da tabela 'doctors'
            'crm'             => [
                'required',
                'string',
                'max:20',
                Rule::unique('doctors', 'crm')->ignore($doctorId),
            ],
            'specialty_id'    => ['required', 'exists:specialties,id'],
            'phone'           => ['required', 'string', 'max:20'],
            'work_start_time' => ['required', 'date_format:H:i'],
            'work_end_time'   => ['required', 'date_format:H:i', 'after:work_start_time'],
            'slot_duration'   => ['required', 'integer', 'min:10', 'max:120'],
            'is_active'       => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'              => 'O nome do médico é obrigatório.',
            'email.required'             => 'O e-mail é obrigatório.',
            'email.unique'               => 'Este e-mail já está cadastrado no sistema.',
            'password.required'          => 'A senha de acesso é obrigatória.',
            'password.min'               => 'A senha deve conter no mínimo 8 caracteres.',
            'cpf.required'               => 'O CPF é obrigatório.',
            'cpf.unique'                 => 'Este CPF já está cadastrado.',
            'crm.required'               => 'O CRM é obrigatório.',
            'crm.unique'                 => 'Este CRM já pertence a outro profissional.',
            'specialty_id.required'      => 'Selecione uma especialidade.',
            'specialty_id.exists'        => 'A especialidade selecionada não existe.',
            'phone.required'             => 'O telefone de contato é obrigatório.',
            'work_start_time.required'   => 'Informe o horário de início do expediente.',
            'work_start_time.date_format'=> 'Horário de início deve estar no formato HH:MM.',
            'work_end_time.required'     => 'Informe o horário de término do expediente.',
            'work_end_time.date_format'  => 'Horário de término deve estar no formato HH:MM.',
            'work_end_time.after'        => 'O horário de término deve ser posterior ao horário de início.',
            'slot_duration.required'     => 'Informe a duração da consulta em minutos.',
            'slot_duration.integer'      => 'A duração da consulta deve ser um número inteiro.',
            'slot_duration.min'          => 'A duração mínima da consulta é de 10 minutos.',
            'slot_duration.max'          => 'A duração máxima da consulta é de 120 minutos.',
        ];
    }
}

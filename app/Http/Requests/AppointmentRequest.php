<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'            => ['nullable', 'string', 'max:255'],
            'patient_id'       => [
                Rule::excludeIf($this->user()?->role !== 'admin'),
                'required',
                'exists:users,id',
            ],
            'doctor_id'        => ['required', 'exists:doctors,id'],
            'appointment_date' => [
                'required',
                'date',
                'after:now',
                // Evita conflito de horário para o mesmo médico
                Rule::unique('appointments', 'appointment_date')->where(function ($query) {
                    return $query->where('doctor_id', $this->doctor_id)
                                 ->where('status', '!=', 'cancelled');
                })->ignore($this->route('appointment')),
            ],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'doctor_id.required'        => 'Selecione um médico para o atendimento.',
            'doctor_id.exists'          => 'O médico selecionado não foi encontrado.',
            'appointment_date.required' => 'Informe a data e o horário da consulta.',
            'appointment_date.date'     => 'Formato de data e horário inválido.',
            'appointment_date.after'    => 'A consulta deve ser agendada para uma data futura.',
            'appointment_date.unique'   => 'Este médico já possui uma consulta marcada neste horário.',
            'patient_id.required'       => 'Selecione o paciente para quem a consulta será agendada.',
        ];
    }
}

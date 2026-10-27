<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AppointmentService
{
    /**
     * Agenda uma nova consulta aplicando as regras de validação.
     * 
     * @param array $data ['doctor_id', 'patient_id', 'scheduled_at', ...]
     * @return Appointment
     * @throws ValidationException
     */
    public function schedule(array $data)
    {
        $scheduledAt = Carbon::parse($data['scheduled_at']);

        // 1. Validação de horário retroativo
        if ($scheduledAt->isPast()) {
            throw ValidationException::withMessages([
                'scheduled_at' => 'Não é possível agendar consultas para horários passados.'
            ]);
        }

        // 2. Choque de horário na agenda do médico
        $doctorBusy = Appointment::where('doctor_id', $data['doctor_id'])
            ->where('scheduled_at', $scheduledAt)
            ->where('status', '!=', 'canceled')
            ->exists();

        if ($doctorBusy) {
            throw ValidationException::withMessages([
                'doctor_id' => 'O médico já possui uma consulta marcada para este horário.'
            ]);
        }

        // 3. Choque de horário na agenda do paciente
        $patientBusy = Appointment::where('patient_id', $data['patient_id'])
            ->where('scheduled_at', $scheduledAt)
            ->where('status', '!=', 'canceled')
            ->exists();

        if ($patientBusy) {
            throw ValidationException::withMessages([
                'patient_id' => 'O paciente já possui uma consulta marcada para este horário.'
            ]);
        }

        // 4. Criação da conceito
        return DB::transaction(function () use ($data, $scheduledAt) {
            return Appointment::create([
                'doctor_id'    => $data['doctor_id'],
                'patient_id'   => $data['patient_id'],
                'scheduled_at' => $scheduledAt,
                'status'       => 'scheduled',
            ]);
        });
    }

    /**
     * Cancela uma consulta existente validando permissão e prazo de 24h.
     * 
     * @param Appointment $appointment
     * @param User $actor (Usuário logado que está realizando a ação)
     * @return Appointment
     * @throws AccessDeniedHttpException|ValidationException
     */
    public function cancel(Appointment $appointment, User $actor): Appointment
    {
        // 1. Valida se a consulta já está cancelada
        if ($appointment->status === 'canceled') {
            throw ValidationException::withMessages([
                'status' => 'Esta consulta já foi cancelada previamente.'
            ]);
        }

        // 2. Valida quem está solicitando (deve ser o paciente, o médico ou um admin)
        $isDoctor = $actor->id === $appointment->doctor_id;
        $isPatient = $actor->id === $appointment->patient_id;
        $isAdmin = method_exists($actor, 'isAdmin') && $actor->isAdmin();

        if (!$isDoctor && !$isPatient && !$isAdmin) {
            throw new AccessDeniedHttpException('Você não tem permissão para cancelar este agendamento.');
        }

        // 3. Valida a regra de antecedência mínima de 24 horas
        // Se a diferença em horas (com sinal) for menor que 24, bloqueia
        $hoursUntilAppointment = now()->diffInHours($appointment->scheduled_at, false);

        if ($hoursUntilAppointment < 24) {
            throw ValidationException::withMessages([
                'cancellation' => 'Cancelamentos só são permitidos com pelo menos 24 horas de antecedência.'
            ]);
        }

        // 4. Atualiza o status
        return DB::transaction(function () use ($appointment) {
            $appointment->update([
                'status' => 'canceled'
            ]);

            return $appointment;
        });
    }
}
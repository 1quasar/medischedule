<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Doctor;
use App\Http\Requests\AppointmentRequest;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $appointments = Appointment::with(['patient', 'doctor'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhereHas('patient', fn($p) => $p->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('doctor', fn ($d) => $d->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderBy('appointment_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.appointments.index', compact('appointments', 'search', 'status'));
    }

    /**
     * Minhas Consultas (Área do Paciente)
     */
    public function myAppointments()
    {
        $appointments = Appointment::with('doctor')
            ->where('patiente_id', Auth::id())
            ->orderBy('appointment_date', 'desc')
            ->paginate(10);

            return view('patient.appointments.index', compact('appointments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AppointmentRequest $request)
    {
        $data = $request->validated();

        if (Auth::user()->role === 'patient') {
            $data['patient_id'] = Auth::id();
            $data['status'] = 'scheduled'; // status inicial
        }

        Appointment::create($data);

        $redirectRoute = Auth::user()-role === 'admin'
            ? 'admin.appointments.index'
            : 'appointments.index';

        return redirect()
            ->route($redirectRoute)
            ->with('success', 'Consulta agendada com sucesso!');
    }

    /**
     * Cancelar consulta (Paciente)
     */
    public function cancel(int $id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('patient_id', Auth::()) // Garante que só o paciente cancela a consulta
            ->firstOrFail();

        $appointment->update([
            'status' => 'cancelled',
            'canceled_at' => now(),
        ]);

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Consulta cancelada com sucesso!');
    }

    /**
     * Alteração rapida de status (Admin)
     */
    public function updateStatus(Request $request, int $id)
    {
        $validated = $request->validated([
            'status' => [
                'required',
                'string',
                'in:scheduled,confirmed,completed,cancelled'
            ],
        ]);

        $appointment = Appointment::findOrFail($id);
        $appointment->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.appointments.index')
            ->with('success', 'Status da consulta atualizado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load([
            'patient',
            'doctor',
        ]);

        return view('appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        $doctors = Doctor::orderBy('name', 'asc')->get();
        $patients = User::where('role', 'patient')
            ->orderBy('name', 'asc')
            ->get();
        
        return view('appointments.edit', compact('appointment', 'doctors', 'patients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AppointmentRequest $request, Appointment $appointment)
    {
        $appointment->update($request->validated());

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Dados da consulta atualizados com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Agendamento de consulta removido do sistema.');
    }
}

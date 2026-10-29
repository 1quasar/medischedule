<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
            ->orderBy('date_time', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.appointments.index', compact('appointments', 'search', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $doctors = Doctor::orderBy('name', 'asc')->get();
        $patients = User::where('role', 'patient')
            ->orderBy('name', 'asc')
            ->get();

        return view('appointment.create', compact('doctors', 'patients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AppointmentRequest $request)
    {
        Appointment::create($request->validated());

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Consulta agendada com sucesso!')
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load([
            'appointment.patient'
            'appointment.doctor'
            'appointment.appointment_date'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

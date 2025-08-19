<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    // Récupérer tous les rendez-vous du patient connecté
    public function index()
    {
        $patientId = Auth::guard('patient')->id();

        $appointments = Appointment::with('medecin')
            ->where('patient_id', $patientId)
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($app) {
                return [
                    'id' => $app->id,
                    'doctor' => $app->medecin
                        ? $app->medecin->nom . ' ' . $app->medecin->prenom
                        : 'Médecin supprimé',
                    'medecin_id' => $app->medecin ? $app->medecin->id : null,
                    'date' => $app->date,
                    'status' => $app->status,
                    'consultation_type' => $app->consultation_type,
                ];
            });

        return response()->json($appointments);
    }

    // Créer un nouveau rendez-vous
    public function store(Request $request)
    {
        $request->validate([
            'medecin_id' => 'required|exists:medecins,id',
            'date' => 'required|date|after_or_equal:today',
            'consultation_type' => 'required|string|max:255',
        ]);

        $patientId = Auth::guard('patient')->id();

        $appointment = Appointment::create([
            'patient_id' => $patientId,
            'medecin_id' => $request->medecin_id,
            'date' => $request->date,
            'consultation_type' => $request->consultation_type,
            'status' => 'en attente',
        ]);

        return response()->json([
            'message' => 'Rendez-vous confirmé !',
            'appointment' => $appointment,
        ]);
    }

    // Récupérer tous les rendez-vous du médecin connecté
    public function doctorAppointments()
    {
        $medecinId = Auth::guard('medecin')->id();

        $appointments = Appointment::with('patient')
            ->where('medecin_id', $medecinId)
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($app) {
                return [
                    'id' => $app->id,
                    'patient' => $app->patient
                        ? $app->patient->prenom . ' ' . $app->patient->nom
                        : 'Patient supprimé',
                    'patient_id' => $app->patient ? $app->patient->id : null,
                    'phone' => $app->patient ? $app->patient->telephone : null,
                    'address' => $app->patient ? $app->patient->address : null,
                    'date' => $app->date,
                    'status' => $app->status,
                    'consultation_type' => $app->consultation_type,
                ];
            });

        return response()->json($appointments);
    }


    // Confirmer un rendez-vous
    public function confirm($id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('medecin_id', Auth::guard('medecin')->id())
            ->firstOrFail();

        $appointment->status = 'confirmé';
        $appointment->save();

        return response()->json(['message' => 'Rendez-vous confirmé', 'appointment' => $appointment]);
    }

    // Refuser un rendez-vous
    public function reject($id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('medecin_id', Auth::guard('medecin')->id())
            ->firstOrFail();

        $appointment->status = 'refusé';
        $appointment->save();

        return response()->json(['message' => 'Rendez-vous refusé', 'appointment' => $appointment]);
    }
}

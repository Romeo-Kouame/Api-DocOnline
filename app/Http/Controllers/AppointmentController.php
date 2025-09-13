<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
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
                    'time' => $app->time,
                    'status' => $app->status,
                    'consultation_type' => $app->consultation_type,
                ];
            });

        return response()->json($appointments);
    }

    public function store(Request $request)
    {
        $request->validate([
            'medecin_id' => 'required|exists:medecins,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'consultation_type' => 'required|string|max:255',
        ]);

        $patientId = Auth::guard('patient')->id();
        $medecinId = $request->medecin_id;
        $date = $request->date;
        $time = $request->time;

        // Vérifier que l'heure est entre 09:00 et 18:00
        if ($time < '09:00' || $time > '18:00') {
            return response()->json([
                'message' => 'L’heure du rendez-vous doit être comprise entre 09:00 et 18:00.'
            ], 422);
        }

        // Vérifier si le médecin a déjà un rendez-vous à cette date avec un écart d'1h
        $existingRdv = Appointment::where('medecin_id', $medecinId)
            ->where('date', $date)
            ->where(function ($query) use ($time) {
                $query->whereBetween('time', [
                    date('H:i', strtotime($time . ' -1 hour')),
                    date('H:i', strtotime($time . ' +1 hour'))
                ]);
            })
            ->first();

        if ($existingRdv) {
            $medecin = \App\Models\Medecin::find($medecinId);
            $alternativeMedecins = \App\Models\Medecin::where('specialite', $medecin->specialite)
                ->where('id', '!=', $medecinId)
                ->get();

            return response()->json([
                'message' => 'Le rendez-vous est indisponible pour ce médecin à cette date et heure. Veuillez respecter un intervalle d’une heure.',
                'alternative_medecins' => $alternativeMedecins
            ], 409);
        }

        // Créer le rendez-vous
        $appointment = Appointment::create([
            'patient_id' => $patientId,
            'medecin_id' => $medecinId,
            'date' => $date,
            'time' => $time,
            'consultation_type' => $request->consultation_type,
            'status' => 'en attente',
        ]);

        return response()->json([
            'message' => 'Rendez-vous confirmé !',
            'appointment' => $appointment,
        ]);
    }

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
                    'time' => $app->time,
                    'status' => $app->status,
                    'consultation_type' => $app->consultation_type,
                ];
            });

        return response()->json($appointments);
    }

    public function confirm($id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('medecin_id', Auth::guard('medecin')->id())
            ->firstOrFail();

        $appointment->status = 'confirmé';
        $appointment->save();

        return response()->json(['message' => 'Rendez-vous confirmé', 'appointment' => $appointment]);
    }

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

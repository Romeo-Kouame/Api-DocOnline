<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // Récupérer les messages entre le patient et un médecin spécifique
    public function index($medecinId)
    {
        $patientId = Auth::guard('patient')->id();

        $messages = Message::where('patient_id', $patientId)
            ->where('medecin_id', $medecinId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    // Envoyer un message
    public function store(Request $request)
    {
        $request->validate([
            'medecin_id' => 'required|exists:medecins,id',
            'content' => 'required|string|max:1000',
        ]);

        $patientId = Auth::guard('patient')->id();

        $message = Message::create([
            'patient_id' => $patientId,
            'medecin_id' => $request->medecin_id,
            'content' => $request->content,
            'from_patient' => true,
        ]);

        return response()->json([
            'message' => 'Message envoyé !',
            'data' => $message
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $message = new Message();
        $message->content = $request->content;
        $message->save();

        return response()->json([
            'success' => true,
            'message' => 'Message envoyé avec succès !',
            'data' => $message
        ]);
    }
}

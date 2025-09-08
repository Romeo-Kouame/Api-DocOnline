<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class PatientAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|unique:patients',
            'telephone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:6',
            'photo_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
        ];

        if ($request->hasFile('photo_profil')) {
            $data['photo_profil'] = $request->file('photo_profil')->store('photos/patients', 'public');
        }

        $patient = Patient::create($data);

        $token = $patient->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'patient' => $patient,
            'photo_url' => $patient->photo_profil ? asset('storage/' . $patient->photo_profil) : null,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $patient = Patient::where('email', $request->email)->first();

        if (! $patient || ! Hash::check($request->password, $patient->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email ou mot de passe incorrect.'],
            ]);
        }

        $token = $patient->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'patient' => $patient,
            'photo_url' => $patient->photo_profil ? asset('storage/' . $patient->photo_profil) : null,
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $patient = $request->user();

        $request->validate([
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|unique:patients,email,' . $patient->id,
            'telephone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'photo_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['nom','prenom','email','telephone','address']);

        if ($request->hasFile('photo_profil')) {
            // Supprimer l'ancienne photo si elle existe
            if ($patient->photo_profil && Storage::disk('public')->exists($patient->photo_profil)) {
                Storage::disk('public')->delete($patient->photo_profil);
            }

            $data['photo_profil'] = $request->file('photo_profil')->store('photos/patients', 'public');
        }

        $patient->update($data);

        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'patient' => $patient,
            'photo_url' => $patient->photo_profil ? asset('storage/' . $patient->photo_profil) : null,
        ]);
    }
}

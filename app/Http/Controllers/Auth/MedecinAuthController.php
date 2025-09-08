<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Medecin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class MedecinAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|unique:medecins',
            'telephone' => 'nullable|string|max:20',
            'specialite' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'password' => 'required|string|min:6',
            'photo_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'specialite' => $request->specialite,
            'address' => $request->address,
            'bio' => $request->bio,
            'password' => Hash::make($request->password),
        ];

        if ($request->hasFile('photo_profil')) {
            $data['photo_profil'] = $request->file('photo_profil')->store('photos/medecins', 'public');
        }

        $medecin = Medecin::create($data);

        $token = $medecin->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'medecin' => $medecin,
            'photo_url' => $medecin->photo_profil ? asset('storage/' . $medecin->photo_profil) : null,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $medecin = Medecin::where('email', $request->email)->first();

        if (! $medecin || ! Hash::check($request->password, $medecin->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email ou mot de passe incorrect.'],
            ]);
        }

        $token = $medecin->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'medecin' => $medecin,
            'photo_url' => $medecin->photo_profil ? asset('storage/' . $medecin->photo_profil) : null,
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $medecin = $request->user();

        $request->validate([
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|unique:medecins,email,' . $medecin->id,
            'telephone' => 'nullable|string|max:20',
            'specialite' => 'sometimes|string|max:255',
            'address' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'photo_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['nom','prenom','email','telephone','specialite','address','bio']);

        if ($request->hasFile('photo_profil')) {
            // Supprimer ancienne photo
            if ($medecin->photo_profil && Storage::disk('public')->exists($medecin->photo_profil)) {
                Storage::disk('public')->delete($medecin->photo_profil);
            }
            $data['photo_profil'] = $request->file('photo_profil')->store('photos/medecins', 'public');
        }

        $medecin->update($data);

        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'medecin' => $medecin,
            'photo_url' => $medecin->photo_profil ? asset('storage/' . $medecin->photo_profil) : null,
        ]);
    }
}

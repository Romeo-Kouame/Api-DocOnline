<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Medecin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
        ]);

        $medecin = Medecin::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'specialite' => $request->specialite,
            'address' => $request->address,
            'bio' => $request->bio,
            'password' => Hash::make($request->password),
        ]);

        $token = $medecin->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'medecin' => $medecin
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
            'medecin' => $medecin
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
        ]);

        $medecin->update($request->all());

        return response()->json($medecin);
    }
}

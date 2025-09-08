<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medecin;
use Illuminate\Support\Facades\Auth;



class MedecinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Retourne la liste de tous les médecins
    public function index()
    {
        $medecins = Medecin::all();
        return response()->json($medecins);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:medecins,email',
            'password' => 'required|min:6',
            'photo_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();
        $data['password'] = bcrypt($data['password']);

        if ($request->hasFile('photo_profil')) {
            $data['photo_profil'] = $request->file('photo_profil')->store('assets/images/medecins', 'public');
        }

        $medecin = Medecin::create($data);

        return response()->json([
            'message' => 'Médecin créé avec succès',
            'data' => $medecin,
            'photo_url' => $medecin->photo_profil ? asset('assets/images' . $medecin->photo_profil) : null
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $medecin = Medecin::find($id);

        if (!$medecin) {
            return response()->json(['message' => 'Médecin non trouvé'], 404);
        }

        return response()->json($medecin);
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
    public function updateProfile(Request $request, $id)
    {
        //
        $medecin = Medecin::findOrFail($id);

        $request->validate([
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:medecins,email,' . $medecin->id,
            'photo_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo_profil')) {
            $data['photo_profil'] = $request->file('photo_profil')->store('photos/medecins', 'public');
        }

        $medecin->update($data);

        return response()->json([
            'message' => 'Médecin mis à jour avec succès',
            'data' => $medecin,
            'photo_url' => $medecin->photo_profil ? asset('storage/' . $medecin->photo_profil) : null
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function profile()
    {
        //
    }


    // Ici on affiche les mises à jour concernant les heures de travail du médecin 
    public function updateWorkingHours(Request $request)
    {
        $medecin = Medecin::find(Auth::guard('medecin')->id());

        $request->validate([
            'working_hours' => 'required|array',
            'working_hours.*.day' => 'required|string',
            'working_hours.*.hours' => 'required|string',
        ]);

        $medecin->working_hours = $request->working_hours;
        $medecin->save();

        return response()->json([
            'message' => 'Horaires de consultation mis à jour',
            'working_hours' => $medecin->working_hours,
        ]);
    }
}

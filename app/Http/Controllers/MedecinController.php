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
        //
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
    public function updateProfile(Request $request)
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

    public function profile()
    {
        //
    }

    public function updateWorkingHours(Request $request)
    {
        $medecin = Auth::guard('medecin')->user();

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

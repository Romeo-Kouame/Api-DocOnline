<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Medecin extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'specialite',
        'address',
        'bio',
        'password',
        'working_hours',
        'years_experience',         
        'languages',                
        'professional_background',  
        'consultation_price',       
        'insurance_accepted'
    ];

    protected $hidden = ['password'];

    // Convertit automatiquement le JSON en tableau PHP
    protected $casts = [
        'working_hours' => 'array',
    ];

    // Relation avec les rendez-vous
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'medecin_id');
    }
}
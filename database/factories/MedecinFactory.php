<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class MedecinFactory extends Factory
{
    protected $model = \App\Models\Medecin::class;

    public function definition()
    {
        return [
            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'email' => $this->faker->unique()->safeEmail,
            'telephone' => $this->faker->phoneNumber,
            'specialite' => $this->faker->randomElement([
                'Cardiologue',
                'Dermatologue',
                'Pédiatre',
                'Généraliste',
                'Neurologue',
                'Gynécologue'
            ]),
            'address' => $this->faker->address,
            'bio' => $this->faker->paragraph,
            'password' => Hash::make('password'), // mot de passe par défaut
            'working_hours' => [
                ['day' => 'Lundi', 'hours' => '09:00 - 12:30 | 14:00 - 18:00'],
                ['day' => 'Mardi', 'hours' => '09:00 - 12:30 | 14:00 - 18:00'],
                ['day' => 'Mercredi', 'hours' => '09:00 - 12:30'],
                ['day' => 'Jeudi', 'hours' => '09:00 - 12:30 | 14:00 - 18:00'],
                ['day' => 'Vendredi', 'hours' => '09:00 - 12:30 | 14:00 - 17:00'],
            ],
            'experience_years' => $this->faker->numberBetween(1, 30),
            'languages' => implode(', ', $this->faker->randomElements(['Français', 'Anglais', 'Espagnol', 'Allemand'], 2)),
            'professional_background' => $this->faker->paragraph,
            'consultation_price' => $this->faker->numberBetween(20, 150),
            'insurance_accepted' => $this->faker->randomElement([0, 1]),
        ];
    }
}

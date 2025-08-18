<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
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
        ];
    }
}

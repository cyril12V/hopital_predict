<?php

namespace Database\Factories;

use App\Models\Admission;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdmissionFactory extends Factory
{
    protected $model = Admission::class;

    public function definition()
    {
        return [
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'pathologie' => $this->faker->randomElement(['Grippe', 'Covid', 'Accident', 'Opération']),
            'nombre_patients' => $this->faker->numberBetween(5, 50),
            'duree_moyenne' => $this->faker->numberBetween(1, 10),
        ];
    }
}


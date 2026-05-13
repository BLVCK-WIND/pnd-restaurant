<?php

namespace Database\Factories;

use App\Models\Area;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Area>
 */
class AreaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'        => $this->faker->randomElement(['Tầng 1', 'Tầng 2', 'Ngoài trời', 'VIP']),
            'description' => $this->faker->sentence(),
            'is_active'   => true,
        ];
    }
}

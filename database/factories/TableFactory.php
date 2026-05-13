<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Table>
 */
class TableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'area_id'  => Area::inRandomOrder()->first()->id,
            'name'     => strtoupper($this->faker->bothify('B##')),
            'capacity' => $this->faker->randomElement([2, 4, 6, 8]),
            'status'   => 'active',
        ];
    }
}

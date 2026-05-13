<?php

namespace Database\Factories;
use Illuminate\Support\Str;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'Khai vị', 'Món chính', 'Tráng miệng',
            'Đồ uống', 'Món chay', 'Đặc sản',
        ]);

        return [
            'name'        => $name,
            'slug'        => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 999),
            'description' => $this->faker->sentence(),
            'image'       => null,
            'sort_order'  => $this->faker->numberBetween(0, 10),
            'is_active'   => true,
        ];
    }
}

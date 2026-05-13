<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MenuItem>
 */
class MenuItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'Phở bò', 'Bún bò Huế', 'Cơm tấm sườn', 'Bánh mì thịt',
            'Gỏi cuốn', 'Chả giò', 'Lẩu thái', 'Cá kho tộ',
            'Tôm nướng muối ớt', 'Sườn nướng mật ong',
            'Nước ép cam', 'Trà đào', 'Chè ba màu', 'Bánh flan',
        ]);

        return [
            'category_id' => Category::inRandomOrder()->first()->id,
            'name'        => $name,
            'slug'        => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 999),
            'description' => $this->faker->sentence(),
            'price'       => $this->faker->randomElement([25000, 35000, 45000, 55000, 75000, 95000, 120000, 150000]),
            'image'       => null,
            'status'      => 'active',
        ];
    }
}

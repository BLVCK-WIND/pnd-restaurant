<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Shift;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Tạo users cố định
        User::create([
            'name'      => 'Admin',
            'email'     => 'admin@pnd.com',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name'      => 'Nhân viên A',
            'email'     => 'staff_a@pnd.com',
            'password'  => Hash::make('password'),
            'role'      => 'staff',
            'phone'     => '0901111111',
            'is_active' => true,
        ]);

        User::create([
            'name'      => 'Nhân viên B',
            'email'     => 'staff_b@pnd.com',
            'password'  => Hash::make('password'),
            'role'      => 'staff',
            'phone'     => '0902222222',
            'is_active' => true,
        ]);

        // Tạo 5 guest
        User::factory(5)->create([
            'role'      => 'guest',
            'is_active' => true,
        ]);

        // 2. Tạo khu vực và bàn
        $areas = [
            ['name' => 'Tầng 1',     'description' => 'Khu vực tầng trệt'],
            ['name' => 'Tầng 2',     'description' => 'Khu vực tầng 2'],
            ['name' => 'Ngoài trời', 'description' => 'Khu vực sân vườn'],
            ['name' => 'VIP',        'description' => 'Phòng VIP riêng tư'],
        ];

        foreach ($areas as $area) {
            $created = Area::create([...$area, 'is_active' => true]);
            Table::factory(5)->create(['area_id' => $created->id]);
        }

        // 3. Tạo category và menu items
        $categories = [
            'Khai vị',
            'Món chính',
            'Tráng miệng',
            'Đồ uống',
        ];

        foreach ($categories as $index => $name) {
            $category = Category::create([
                'name'       => $name,
                'slug'       => \Illuminate\Support\Str::slug($name),
                'is_active'  => true,
                'sort_order' => $index + 1,
            ]);

            MenuItem::factory(6)->create(['category_id' => $category->id]);
        }

        // 4. Tạo ca làm việc
        $shifts = [
            ['name' => 'Ca sáng', 'start_time' => '06:00', 'end_time' => '12:00'],
            ['name' => 'Ca chiều', 'start_time' => '12:00', 'end_time' => '17:00'],
            ['name' => 'Ca tối',  'start_time' => '17:00', 'end_time' => '22:00'],
        ];

        foreach ($shifts as $shift) {
            Shift::create($shift);
        }
    }
}

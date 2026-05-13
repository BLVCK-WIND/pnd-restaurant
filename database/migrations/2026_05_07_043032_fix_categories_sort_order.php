<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Gán lại sort_order liên tục theo thứ tự hiện tại
        Category::orderBy('sort_order')->orderBy('id')
            ->get()
            ->each(function ($category, $index) {
                $category->update(['sort_order' => $index + 1]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

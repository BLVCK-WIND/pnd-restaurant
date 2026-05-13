<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('tables')->update([
            'status' => DB::raw("
                CASE
                    WHEN status IN ('available','maintenance') THEN 'active'
                    WHEN status IN ('occupied','reserved') THEN 'inactive'
                END
            ")
        ]);
    }

    public function down(): void
    {
        DB::table('tables')->update([
            'status' => DB::raw("
                CASE
                    WHEN status = 'active' THEN 'available'
                    WHEN status = 'inactive' THEN 'occupied'
                END
            ")
        ]);
    }
};

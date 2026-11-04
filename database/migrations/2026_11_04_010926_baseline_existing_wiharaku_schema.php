<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $requiredTables = [
            'provinces',
            'cities',
            'districts',
            'subdistricts',
            'users',
            'viharas',
            'verification_statuses',
        ];

        foreach ($requiredTables as $table) {
            if (! DB::getSchemaBuilder()->hasTable($table)) {
                throw new RuntimeException(
                    "Required existing table [{$table}] was not found."
                );
            }
        }
    }

    public function down(): void
    {
        // Baseline tidak menghapus schema yang sudah ada.
    }
};

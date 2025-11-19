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
        // Update enum to include sidebar-left and sidebar-right
        DB::statement("ALTER TABLE banners MODIFY COLUMN position ENUM('top', 'sidebar', 'sidebar-left', 'sidebar-right', 'bottom', 'inline', 'sticky') DEFAULT 'sidebar'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum
        DB::statement("ALTER TABLE banners MODIFY COLUMN position ENUM('top', 'sidebar', 'bottom', 'inline', 'sticky') DEFAULT 'sidebar'");
    }
};

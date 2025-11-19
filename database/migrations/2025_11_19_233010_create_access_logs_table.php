<?php

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
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->index();
            $table->string('url')->index();
            $table->string('method', 10)->default('GET');
            $table->string('user_agent')->nullable();
            $table->string('referer')->nullable();
            $table->integer('status_code')->nullable();
            $table->integer('response_time')->nullable()->comment('Response time in milliseconds');
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('device_type')->nullable()->comment('desktop, mobile, tablet');
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('created_at');
            $table->index(['ip_address', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_logs');
    }
};

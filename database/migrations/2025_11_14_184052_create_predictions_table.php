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
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id'); // Match ID from API
            $table->string('match_api_id')->nullable(); // Store API match ID as string
            $table->unsignedBigInteger('league_id')->nullable(); // League ID from API
            $table->string('home_team')->nullable(); // Store team names for reference
            $table->string('away_team')->nullable();
            $table->string('league_name')->nullable();
            $table->timestamp('match_time')->nullable(); // Match start time from API
            $table->string('title'); // Title of prediction
            $table->text('content'); // Main content
            $table->text('analysis')->nullable(); // Detailed analysis
            $table->string('prediction_result')->nullable(); // '1', 'X', '2' or '1X', '12', 'X2'
            $table->string('prediction_score')->nullable(); // e.g., "2-1"
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('match_datetime')->nullable(); // Match start time
            $table->timestamps();
            
            // Indexes
            $table->index('match_id');
            $table->index('match_api_id');
            $table->index('league_id');
            $table->index('status');
            $table->index('published_at');
            $table->index('match_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};

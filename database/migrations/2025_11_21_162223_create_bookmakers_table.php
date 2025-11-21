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
        Schema::create('bookmakers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên nhà cái
            $table->string('image'); // URL hoặc path ảnh/gif
            $table->string('link'); // Link nhà cái
            $table->enum('target', ['_blank', '_self'])->default('_blank'); // Mở tab mới hay cùng tab
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0); // Thứ tự hiển thị
            $table->text('notes')->nullable(); // Ghi chú
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookmakers');
    }
};

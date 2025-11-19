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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên banner để quản lý
            $table->string('image')->nullable(); // URL hoặc path hình ảnh
            $table->text('code')->nullable(); // HTML/JavaScript code quảng cáo
            $table->string('link')->nullable(); // URL khi click
            $table->string('alt')->default('Advertisement'); // Alt text
            $table->enum('size', ['small', 'medium', 'large', 'full-width', 'sidebar', 'square', 'rectangle'])->default('medium');
            $table->enum('position', ['top', 'sidebar', 'bottom', 'inline', 'sticky'])->default('sidebar');
            $table->enum('target', ['_blank', '_self'])->default('_blank');
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
        Schema::dropIfExists('banners');
    }
};

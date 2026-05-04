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
        Schema::create('color_palettes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Color name (e.g., 'primary', 'primary-dark')
            $table->string('category'); // Category (e.g., 'brand', 'complementary', 'neutral', 'shadows', 'semantic', 'usage')
            $table->string('theme'); // Theme (e.g., 'light', 'dark')
            $table->string('hex_value'); // Hex color value (e.g., '#94131D')
            $table->string('rgb_value')->nullable(); // RGB value (e.g., 'rgb(148, 19, 29)')
            $table->string('usage')->nullable(); // Usage description (e.g., 'buttons', 'text', 'backgrounds')
            $table->text('description')->nullable(); // Color description
            $table->boolean('is_active')->default(true); // Whether color is active
            $table->integer('sort_order')->default(0); // For ordering colors
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['theme', 'category']);
            $table->index(['name', 'theme']);
            $table->unique(['name', 'category', 'theme']); // Prevent duplicates
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('color_palettes');
    }
};

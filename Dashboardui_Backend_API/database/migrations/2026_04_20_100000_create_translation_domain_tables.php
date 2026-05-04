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
        Schema::create('translation_keys', function (Blueprint $table) {
            $table->id();
            $table->string('domain', 64);
            $table->string('key');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();

            $table->unique(['domain', 'key']);
            $table->index(['domain', 'status']);
        });

        Schema::create('translation_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('translation_key_id')->constrained('translation_keys')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->longText('value')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();

            $table->unique(['translation_key_id', 'locale']);
            $table->index(['locale', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_values');
        Schema::dropIfExists('translation_keys');
    }
};

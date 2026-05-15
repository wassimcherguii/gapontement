<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companion_patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('companion_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('patient_user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('can_book')->default(true);
            $table->timestamps();

            $table->unique(['companion_user_id', 'patient_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companion_patients');
    }
};

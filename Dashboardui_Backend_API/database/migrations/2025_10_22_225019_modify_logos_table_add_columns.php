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
        Schema::table('logos', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('logos', 'name')) {
                $table->string('name')->after('id');
            }
            if (!Schema::hasColumn('logos', 'filename')) {
                $table->string('filename')->after('name');
            }
            if (!Schema::hasColumn('logos', 'path')) {
                $table->string('path')->after('filename');
            }
            if (!Schema::hasColumn('logos', 'alt')) {
                $table->string('alt')->after('path');
            }
            if (!Schema::hasColumn('logos', 'description')) {
                $table->text('description')->nullable()->after('alt');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logos', function (Blueprint $table) {
            $table->dropColumn(['name', 'filename', 'path', 'alt', 'description']);
        });
    }
};

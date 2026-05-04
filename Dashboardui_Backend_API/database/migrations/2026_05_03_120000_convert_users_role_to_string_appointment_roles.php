<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Replace legacy enum role with a string column supporting appointment roles.
     * Maps visitor→patient, manager→secretary; admin and superadmin unchanged.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'role')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('role_new', 32)->nullable();
        });

        $legacyMap = [
            'visitor' => 'patient',
            'manager' => 'secretary',
            'admin' => 'admin',
            'superadmin' => 'superadmin',
        ];

        foreach ($legacyMap as $old => $new) {
            DB::table('users')->where('role', $old)->update(['role_new' => $new]);
        }

        DB::table('users')->whereNull('role_new')->update(['role_new' => 'patient']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('role_new', 'role');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 32)->default('patient')->nullable(false)->change();
        });
    }

    /**
     * Not safely reversible after new roles (doctor, companion, etc.) exist.
     */
    public function down(): void
    {
        throw new \RuntimeException('2026_05_03_120000_convert_users_role_to_string_appointment_roles cannot be reversed automatically.');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_domains', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 64)->unique();
            $table->string('name');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $bootstrapSlugs = config('translation_domains.domains', ['web', 'mobile', 'student', 'teacher']);
        foreach ($bootstrapSlugs as $i => $slug) {
            DB::table('translation_domains')->insert([
                'slug' => $slug,
                'name' => ucfirst($slug),
                'sort_order' => $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $slugToId = DB::table('translation_domains')->pluck('id', 'slug')->all();

        $orphanDomains = DB::table('translation_keys')
            ->select('domain')
            ->distinct()
            ->whereNotNull('domain')
            ->pluck('domain');

        foreach ($orphanDomains as $slug) {
            if ($slug === null || $slug === '') {
                continue;
            }
            if (! isset($slugToId[$slug])) {
                $next = count($slugToId);
                $id = DB::table('translation_domains')->insertGetId([
                    'slug' => $slug,
                    'name' => ucfirst((string) $slug),
                    'sort_order' => $next,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $slugToId[$slug] = $id;
            }
        }

        Schema::table('translation_keys', function (Blueprint $table) {
            $table->foreignId('translation_domain_id')->nullable()->after('id')->constrained('translation_domains')->restrictOnDelete();
        });

        foreach ($slugToId as $slug => $id) {
            DB::table('translation_keys')->where('domain', $slug)->update(['translation_domain_id' => $id]);
        }

        if (DB::table('translation_keys')->whereNull('translation_domain_id')->exists()) {
            throw new RuntimeException('translation_keys rows exist with unknown domain string; fix data before migrating.');
        }

        Schema::table('translation_keys', function (Blueprint $table) {
            $table->dropUnique(['domain', 'key']);
            $table->dropIndex(['domain', 'status']);
            $table->dropColumn('domain');
        });

        Schema::table('translation_keys', function (Blueprint $table) {
            $table->unique(['translation_domain_id', 'key']);
            $table->index(['translation_domain_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('translation_keys', function (Blueprint $table) {
            $table->dropUnique(['translation_domain_id', 'key']);
            $table->dropIndex(['translation_domain_id', 'status']);
        });

        Schema::table('translation_keys', function (Blueprint $table) {
            $table->string('domain', 64)->nullable()->after('id');
        });

        $slugToId = DB::table('translation_domains')->pluck('slug', 'id')->all();
        foreach ($slugToId as $id => $slug) {
            DB::table('translation_keys')->where('translation_domain_id', $id)->update(['domain' => $slug]);
        }

        Schema::table('translation_keys', function (Blueprint $table) {
            $table->dropConstrainedForeignId('translation_domain_id');
        });

        Schema::table('translation_keys', function (Blueprint $table) {
            $table->unique(['domain', 'key']);
            $table->index(['domain', 'status']);
        });

        Schema::dropIfExists('translation_domains');
    }
};

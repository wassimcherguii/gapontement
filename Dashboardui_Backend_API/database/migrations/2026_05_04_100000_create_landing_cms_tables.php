<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 64)->unique();
            $table->timestamps();
        });

        Schema::create('landing_page_locales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_page_id')->constrained('landing_pages')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->unique(['landing_page_id', 'locale']);
        });

        Schema::create('landing_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_page_id')->constrained('landing_pages')->cascadeOnDelete();
            $table->string('section_key', 64);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index(['landing_page_id', 'section_key']);
            $table->index(['landing_page_id', 'sort_order']);
        });

        Schema::create('landing_section_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_section_id')->constrained('landing_sections')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->json('content')->nullable();
            $table->timestamps();

            $table->unique(['landing_section_id', 'locale']);
        });

        Schema::create('landing_nav_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_page_id')->constrained('landing_pages')->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('href', 512);
            $table->string('route_key', 64)->nullable();
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_cta')->default(false);
            $table->string('icon', 64)->nullable();
            $table->timestamps();

            $table->index(['landing_page_id', 'sort_order']);
        });

        Schema::create('landing_nav_item_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_nav_item_id')->constrained('landing_nav_items')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('label', 255);
            $table->timestamps();

            $table->unique(['landing_nav_item_id', 'locale']);
        });

        Schema::create('landing_entities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_section_id')->constrained('landing_sections')->cascadeOnDelete();
            $table->string('type', 32);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('slug', 128)->nullable();
            $table->string('image_path', 512)->nullable();
            $table->string('href', 512)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('extra')->nullable();
            $table->timestamps();

            $table->index(['landing_section_id', 'sort_order']);
            $table->index(['landing_section_id', 'type']);
        });

        Schema::create('landing_entity_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_entity_id')->constrained('landing_entities')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('title', 512)->nullable();
            $table->string('subtitle', 512)->nullable();
            $table->text('body')->nullable();
            $table->string('cta_label', 255)->nullable();
            $table->timestamps();

            $table->unique(['landing_entity_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_entity_translations');
        Schema::dropIfExists('landing_entities');
        Schema::dropIfExists('landing_nav_item_translations');
        Schema::dropIfExists('landing_nav_items');
        Schema::dropIfExists('landing_section_translations');
        Schema::dropIfExists('landing_sections');
        Schema::dropIfExists('landing_page_locales');
        Schema::dropIfExists('landing_pages');
    }
};

<?php

namespace App\Providers;

use App\Services\TranslationDomainRegistry;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TranslationDomainRegistry::class, fn () => new TranslationDomainRegistry);

        $portalHelpers = app()->basePath('app/helpers/portal_translation_helpers.php');
        if (is_file($portalHelpers) && ! function_exists('portal_t')) {
            require_once $portalHelpers;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::bind('domain', function (string $value) {
            $registry = app(TranslationDomainRegistry::class);
            if (! in_array($value, $registry->allowedSlugs(), true)) {
                abort(404);
            }

            return $value;
        });
    }
}

<?php

use Illuminate\Support\Facades\Route;

// Redirect root to default language
Route::get('/', function () {
    $defaultLang = get_default_language();

    return redirect("/{$defaultLang}");
});

// Language-prefixed routes
Route::prefix('{lang}')->where(['lang' => 'en|fr|ar'])->group(function () {

    Route::post('/preferences/theme', [App\Http\Controllers\WelcomePreferencesController::class, 'updateTheme'])
        ->name('welcome.theme');

    // Welcome page
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');

    Route::get('/blog/{slug}', [App\Http\Controllers\BlogPublicController::class, 'show'])
        ->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')
        ->name('blog.show');

    // Portal routes (per-domain client translations; middleware sets translation_portal)
    Route::prefix('student')->middleware('portal:student')->group(function () {
        Route::get('/', function () {
            return view('portal.student.home');
        })->name('portal.student.home');
    });

    Route::prefix('teacher')->middleware('portal:teacher')->group(function () {
        Route::get('/', function () {
            return view('portal.teacher.home');
        })->name('portal.teacher.home');
    });

    // Guest Routes (Login) - redirect if already authenticated
    Route::middleware('guest')->group(function () {
        // Admin Login
        Route::get('/admin/login', [App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/admin/login', [App\Http\Controllers\Admin\AuthController::class, 'login'])->name('admin.login.submit');

        // Default login route - redirects to admin login
        Route::get('/login', function () {
            return redirect()->route('admin.login', ['lang' => app()->getLocale()]);
        })->name('login');

        // SuperAdmin Login
        Route::get('/superadmin/login', [App\Http\Controllers\SuperAdmin\AuthController::class, 'showLoginForm'])->name('superadmin.login');
        Route::post('/superadmin/login', [App\Http\Controllers\SuperAdmin\AuthController::class, 'login'])->name('superadmin.login.submit');
    });

    // Admin Routes (protected - requires admin or superadmin role)
    Route::middleware(['auth', 'admin'])->group(function () {
        // Admin Dashboard
        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        // Public website pages — explicit paths (no {page} param) to avoid routing ambiguity
        Route::get('/admin/website/home', function () {
            $lang = request()->route('lang') ?? get_default_language();

            return redirect()->route('admin.website.landing', ['lang' => $lang], 301);
        })->name('admin.website.home.legacy');
        Route::get('/admin/website/landing', [App\Http\Controllers\Admin\LandingHomeController::class, 'edit'])
            ->name('admin.website.landing');
        Route::post('/admin/website/landing/save', [App\Http\Controllers\Admin\LandingHomeController::class, 'update'])
            ->name('admin.website.landing.save');
        Route::post('/admin/website/landing/publish', [App\Http\Controllers\Admin\LandingHomeController::class, 'publish'])
            ->name('admin.website.landing.publish');
        Route::post('/admin/website/landing/sync-db-to-json', [App\Http\Controllers\Admin\LandingHomeController::class, 'syncDbToJson'])
            ->name('admin.website.landing.sync_db_to_json');
        Route::post('/admin/website/landing/save-json-cache', [App\Http\Controllers\Admin\LandingHomeController::class, 'saveJsonToPageCache'])
            ->name('admin.website.landing.save_json_cache');
        Route::post('/admin/website/landing/import-json-files', [App\Http\Controllers\Admin\LandingHomeController::class, 'importJsonFromFiles'])
            ->name('admin.website.landing.import_json_files');
        Route::post('/admin/website/landing/import-json', [App\Http\Controllers\Admin\LandingHomeController::class, 'importJson'])
            ->name('admin.website.landing.import_json');
        Route::get('/admin/website/about', [App\Http\Controllers\Admin\WebsitePageController::class, 'about'])
            ->name('admin.website.about');
        Route::get('/admin/website/blog', [App\Http\Controllers\Admin\BlogPostController::class, 'index'])
            ->name('admin.website.blog');
        Route::get('/admin/website/blog/create', [App\Http\Controllers\Admin\BlogPostController::class, 'create'])
            ->name('admin.website.blog.create');
        Route::post('/admin/website/blog', [App\Http\Controllers\Admin\BlogPostController::class, 'store'])
            ->name('admin.website.blog.store');
        Route::get('/admin/website/blog/{blog_post}/edit', [App\Http\Controllers\Admin\BlogPostController::class, 'edit'])
            ->name('admin.website.blog.edit');
        Route::put('/admin/website/blog/{blog_post}', [App\Http\Controllers\Admin\BlogPostController::class, 'update'])
            ->name('admin.website.blog.update');
        Route::delete('/admin/website/blog/{blog_post}', [App\Http\Controllers\Admin\BlogPostController::class, 'destroy'])
            ->name('admin.website.blog.destroy');
        Route::get('/admin/website/contacts', [App\Http\Controllers\Admin\WebsitePageController::class, 'contacts'])
            ->name('admin.website.contacts');

        // Admin Assets Routes
        Route::prefix('admin/assets')->group(function () {
            Route::get('/brand', [App\Http\Controllers\Admin\BrandController::class, 'index'])->name('admin.assets.brand');
            Route::post('/brand/upload-logo', [App\Http\Controllers\Admin\BrandController::class, 'uploadLogo'])->name('admin.assets.brand.upload-logo');
            Route::post('/brand/upload-favicon', [App\Http\Controllers\Admin\BrandController::class, 'uploadFavicon'])->name('admin.assets.brand.upload-favicon');
            Route::post('/brand/sync', [App\Http\Controllers\Admin\BrandController::class, 'syncToJson'])->name('admin.assets.brand.sync');
            Route::get('/brand/comparison', [App\Http\Controllers\Admin\BrandController::class, 'getComparison'])->name('admin.assets.brand.comparison');

            Route::get('/colors', [App\Http\Controllers\Admin\ColorController::class, 'index'])->name('admin.assets.colors');
            Route::put('/colors/update/{id}', [App\Http\Controllers\Admin\ColorController::class, 'update'])->where('id', '[0-9]+')->name('admin.assets.colors.update');
            Route::post('/colors/sync', [App\Http\Controllers\Admin\ColorController::class, 'syncToJson'])->name('admin.assets.colors.sync');
            Route::post('/colors/revert', [App\Http\Controllers\Admin\ColorController::class, 'revertFromJson'])->name('admin.assets.colors.revert');
            Route::get('/colors/comparison', [App\Http\Controllers\Admin\ColorController::class, 'getComparison'])->name('admin.assets.colors.comparison');
            Route::get('/colors/json-comparison', [App\Http\Controllers\Admin\ColorController::class, 'getJsonComparison'])->name('admin.assets.colors.json-comparison');

            Route::get('/themes', function () {
                return view('admin.assets.themes');
            })->name('admin.assets.themes');

            Route::get('/languages', function () {
                return view('admin.assets.languages');
            })->name('admin.assets.languages');

            Route::get('/client-translations', [App\Http\Controllers\Admin\ClientTranslationController::class, 'index'])
                ->name('admin.assets.client-translations.index');
            Route::post('/client-translations', [App\Http\Controllers\Admin\ClientTranslationController::class, 'store'])
                ->name('admin.assets.client-translations.store');
            Route::post('/client-translations/sync-to-json', [App\Http\Controllers\Admin\ClientTranslationController::class, 'syncToJson'])
                ->name('admin.assets.client-translations.sync-to-json');
            Route::post('/client-translations/sync-from-json', [App\Http\Controllers\Admin\ClientTranslationController::class, 'syncFromJson'])
                ->name('admin.assets.client-translations.sync-from-json');
            Route::get('/client-translations/sync-diff', [App\Http\Controllers\Admin\ClientTranslationController::class, 'syncDiff'])
                ->name('admin.assets.client-translations.sync-diff');
            Route::get('/client-translations/languages-catalog', [App\Http\Controllers\Admin\ClientTranslationController::class, 'languagesCatalog'])
                ->name('admin.assets.client-translations.languages-catalog');
            Route::post('/client-translations/languages-catalog', [App\Http\Controllers\Admin\ClientTranslationController::class, 'languagesCatalogUpdate'])
                ->name('admin.assets.client-translations.languages-catalog.update');
            Route::put('/client-translations/keys/{translation_key}', [App\Http\Controllers\Admin\ClientTranslationController::class, 'updateKey'])
                ->name('admin.assets.client-translations.keys.update');
            Route::put('/client-translations/json-key', [App\Http\Controllers\Admin\ClientTranslationController::class, 'updateJsonKey'])
                ->name('admin.assets.client-translations.json-key.update');

            Route::get('/translation-domains', [App\Http\Controllers\Admin\TranslationDomainController::class, 'index'])
                ->name('admin.assets.translation-domains.index');
            Route::post('/translation-domains', [App\Http\Controllers\Admin\TranslationDomainController::class, 'store'])
                ->name('admin.assets.translation-domains.store');

            Route::get('/company', [App\Http\Controllers\Admin\CompanyController::class, 'index'])->name('admin.assets.company');
            Route::post('/company/update', [App\Http\Controllers\Admin\CompanyController::class, 'update'])->name('admin.assets.company.update');

            Route::post('/languages/update-default', function (Illuminate\Http\Request $request) {
                $validated = $request->validate([
                    'default_language' => 'required|string|in:en,fr,ar',
                ]);

                try {
                    $path = base_path('jsonassets/languages.json');

                    if (! file_exists($path)) {
                        return response()->json(['success' => false, 'message' => 'Languages file not found'], 404);
                    }

                    $languages = json_decode(file_get_contents($path), true);

                    if (! $languages) {
                        return response()->json(['success' => false, 'message' => 'Failed to parse languages file'], 500);
                    }

                    $oldDefault = $languages['default'];
                    $languages['default'] = $validated['default_language'];

                    $json = json_encode($languages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

                    if (file_put_contents($path, $json) === false) {
                        return response()->json(['success' => false, 'message' => 'Failed to write to languages file'], 500);
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Default language updated successfully from '.strtoupper($oldDefault).' to '.strtoupper($validated['default_language']),
                    ]);

                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => 'Error: '.$e->getMessage()], 500);
                }
            })->name('admin.assets.languages.update-default');

            // Old Brand Management
            Route::get('/old-brand', [App\Http\Controllers\Admin\OldBrandController::class, 'index'])->name('admin.assets.old-brand');
            Route::get('/old-brand/logos', [App\Http\Controllers\Admin\OldBrandController::class, 'getLogos'])->name('admin.assets.old-brand.logos');
            Route::get('/old-brand/favicons', [App\Http\Controllers\Admin\OldBrandController::class, 'getFavicons'])->name('admin.assets.old-brand.favicons');
            Route::post('/old-brand/restore', [App\Http\Controllers\Admin\OldBrandController::class, 'restore'])->name('admin.assets.old-brand.restore');
            Route::delete('/old-brand/delete', [App\Http\Controllers\Admin\OldBrandController::class, 'delete'])->name('admin.assets.old-brand.delete');
        });

        // Admin Logout
        Route::post('/admin/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('admin.logout');
    });

    // SuperAdmin Routes (protected - requires superadmin role only)
    Route::middleware(['auth', 'superadmin'])->group(function () {
        // SuperAdmin Dashboard
        Route::get('/superadmin/dashboard', function () {
            return view('superadmin.dashboard');
        })->name('superadmin.dashboard');

        // SuperAdmin Users Management
        Route::prefix('superadmin/users')->group(function () {
            Route::get('/', [App\Http\Controllers\SuperAdmin\UserController::class, 'index'])->name('superadmin.users.index');
            Route::get('/create', [App\Http\Controllers\SuperAdmin\UserController::class, 'create'])->name('superadmin.users.create');
            Route::post('/', [App\Http\Controllers\SuperAdmin\UserController::class, 'store'])->name('superadmin.users.store');
            Route::get('/{id}', [App\Http\Controllers\SuperAdmin\UserController::class, 'show'])->name('superadmin.users.show');
            Route::get('/{id}/edit', [App\Http\Controllers\SuperAdmin\UserController::class, 'edit'])->name('superadmin.users.edit');
            Route::match(['put', 'patch'], '/{id}', [App\Http\Controllers\SuperAdmin\UserController::class, 'update'])->name('superadmin.users.update');
            Route::delete('/{id}', [App\Http\Controllers\SuperAdmin\UserController::class, 'destroy'])->name('superadmin.users.destroy');
        });

        // SuperAdmin Logout
        Route::post('/superadmin/logout', [App\Http\Controllers\SuperAdmin\AuthController::class, 'logout'])->name('superadmin.logout');
    });
});

/*
|--------------------------------------------------------------------------
| Fallback (must be last)
|--------------------------------------------------------------------------
| If this is registered before `{lang}/...` routes, it can match first and
| abort(404) for every URL that already starts with en|fr|ar — including
| prefixed admin URLs. Keep this after all language-prefixed routes.
*/
Route::fallback(function () {
    $defaultLang = get_default_language();
    $path = request()->path();

    // API routes are handled in routes/api.php; do not language-prefix them here
    if (str_starts_with($path, 'api/')) {
        abort(404);
    }

    $supportedLanguages = ['en', 'fr', 'ar'];
    $pathSegments = explode('/', trim($path, '/'));

    if (! empty($pathSegments) && in_array($pathSegments[0], $supportedLanguages, true)) {
        abort(404);
    }

    $newPath = '/'.$defaultLang.'/'.ltrim($path, '/');

    return redirect($newPath);
});

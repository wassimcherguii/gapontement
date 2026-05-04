<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class WebsitePageController extends Controller
{
    /**
     * @var array<string, string> slug => messages.php key for page title
     */
    private const PAGES = [
        'home' => 'website_home_page',
        'about' => 'website_about_us',
        'blog' => 'website_blog',
        'contacts' => 'website_contacts',
    ];

    public function show(string $page)
    {
        if (! array_key_exists($page, self::PAGES)) {
            abort(404);
        }

        if ($page === 'home') {
            return app(LandingHomeController::class)->edit();
        }

        return view('admin.website.page', [
            'page' => $page,
            'titleKey' => self::PAGES[$page],
        ]);
    }
}

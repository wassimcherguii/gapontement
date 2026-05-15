<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class WebsitePageController extends Controller
{
    /**
     * @var array<string, string> slug => messages.php key for page title
     */
    private const PAGES = [
        'about' => 'website_about_us',
        'contacts' => 'website_contacts',
    ];

    public function about(): View
    {
        return $this->render('about');
    }

    public function contacts(): View
    {
        return $this->render('contacts');
    }

    private function render(string $page): View
    {
        return view('admin.website.page', [
            'page' => $page,
            'titleKey' => self::PAGES[$page],
        ]);
    }
}

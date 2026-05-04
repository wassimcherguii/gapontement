<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Bootstrap translation domain slugs (optional)
    |--------------------------------------------------------------------------
    |
    | Runtime allowlists come from the translation_domains database table and
    | TranslationDomainRegistry (cached). This array is only used by:
    | - Database migrations / TranslationDomainSeeder to seed initial rows
    | - Documentation / conventions
    |
    | To add a domain in production, use Admin → Translation domains (creates
    | DB row + public_languages_{slug}.json + i18n/{slug}/).
    |
    */
    'domains' => [
        'web',
        'mobile',
        'student',
        'teacher',
    ],
];

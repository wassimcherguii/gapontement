<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ is_rtl_language(app()->getLocale()) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ portal_t('welcome.page_title') }}</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem; line-height: 1.5; color: #0f172a; background: #f8fafc; }
        main { max-width: 40rem; margin: 0 auto; background: #fff; padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgb(0 0 0 / 0.08); }
        h1 { margin-top: 0; font-size: 1.5rem; }
        p { color: #475569; }
        a { color: #2563eb; }
    </style>
</head>
<body>
<main>
    <h1>{{ portal_t('welcome.title') }}</h1>
    <p>{{ portal_t('welcome.body') }}</p>
    <p><a href="{{ route_with_lang('welcome') }}">{{ get_translation('go_home') }}</a></p>
</main>
</body>
</html>

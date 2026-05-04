<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Services\TranslationDomainRegistry;
use App\Services\TranslationPublishService;

class ClientTranslationController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly TranslationPublishService $publishService,
        private readonly TranslationDomainRegistry $domainRegistry
    ) {}

    public function languages(string $domain)
    {
        $domain = $this->resolveDomain($domain);
        if (! $domain) {
            return $this->error('Invalid translation domain.', 422);
        }

        return $this->success($this->publishService->readLanguages($domain, true));
    }

    public function bundle(string $domain, string $locale)
    {
        $domain = $this->resolveDomain($domain);
        if (! $domain) {
            return $this->error('Invalid translation domain.', 422);
        }

        $languages = $this->publishService->readLanguages($domain, true);
        $supported = array_keys($languages['supported'] ?? []);
        if (! in_array($locale, $supported, true)) {
            return $this->error('Unsupported locale for this domain.', 422);
        }

        return $this->success([
            'domain' => $domain,
            'locale' => $locale,
            'bundle' => $this->publishService->readBundle($domain, $locale),
            'meta' => $this->publishService->readMeta($domain),
        ]);
    }

    private function resolveDomain(string $domain): ?string
    {
        return in_array($domain, $this->domainRegistry->allowedSlugs(), true) ? $domain : null;
    }
}

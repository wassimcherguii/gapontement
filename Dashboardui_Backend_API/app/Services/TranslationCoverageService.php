<?php

namespace App\Services;

use App\Models\TranslationKey;
use Illuminate\Support\Facades\DB;

class TranslationCoverageService
{
    /**
     * Per-locale coverage for published keys in a domain.
     *
     * Denominator: count of translation_keys where domain and status = published.
     * Numerator: distinct published keys that have a published translation_values row
     * for that locale with a non-empty trimmed value.
     *
     * @param  array<string, array<string, mixed>>  $supported  From public_languages JSON "supported" map
     * @return list<array{code: string, meta: array<string, mixed>, filled: int, total: int, percent: int|null}>
     */
    public function localeCoverage(string $domain, array $supported): array
    {
        $total = TranslationKey::query()
            ->domain($domain)
            ->where('status', 'published')
            ->count();

        $filledByLocale = [];
        if ($total > 0) {
            $filledByLocale = DB::table('translation_values as tv')
                ->join('translation_keys as tk', 'tk.id', '=', 'tv.translation_key_id')
                ->join('translation_domains as td', 'td.id', '=', 'tk.translation_domain_id')
                ->where('td.slug', $domain)
                ->where('tk.status', 'published')
                ->where('tv.status', 'published')
                ->whereNotNull('tv.value')
                ->whereRaw("TRIM(tv.value) <> ''")
                ->select('tv.locale', DB::raw('COUNT(DISTINCT tv.translation_key_id) as filled'))
                ->groupBy('tv.locale')
                ->pluck('filled', 'locale')
                ->all();
        }

        $rows = [];
        foreach ($supported as $code => $meta) {
            if (! is_string($code) || ! is_array($meta)) {
                continue;
            }
            $filled = (int) ($filledByLocale[$code] ?? 0);
            $percent = $total > 0 ? (int) round(100 * $filled / $total) : null;
            $rows[] = [
                'code' => $code,
                'meta' => $meta,
                'filled' => $filled,
                'total' => $total,
                'percent' => $percent,
            ];
        }

        usort($rows, fn ($a, $b) => strcmp($a['code'], $b['code']));

        return $rows;
    }
}

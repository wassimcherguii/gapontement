<?php

namespace App\Console\Commands;

use App\Services\LandingPagePublishService;
use Illuminate\Console\Command;

class LandingPublishCommand extends Command
{
    protected $signature = 'landing:publish {slug=home : Page slug}';

    protected $description = 'Publish landing page CMS data to jsonassets/page-cache JSON files';

    public function handle(LandingPagePublishService $publisher): int
    {
        $slug = (string) $this->argument('slug');
        $result = $publisher->publish($slug);
        $this->info('Published '.$slug.' for locales: '.implode(', ', $result['locales']));
        $this->line('Checksum: '.$result['meta']['checksum']);

        return self::SUCCESS;
    }
}

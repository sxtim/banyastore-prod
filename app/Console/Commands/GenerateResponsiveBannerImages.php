<?php

namespace App\Console\Commands;

use App\Models\Banner;
use App\Services\BannerImageService;
use Illuminate\Console\Command;

class GenerateResponsiveBannerImages extends Command
{
    protected $signature = 'banners:generate-responsive {--force : Regenerate existing responsive WebP files}';

    protected $description = 'Generate responsive WebP copies for main banner images';

    public function handle(BannerImageService $bannerImageService): int
    {
        $force = (bool) $this->option('force');
        $processed = 0;
        $failed = 0;

        Banner::query()
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->orderBy('id')
            ->each(function (Banner $banner) use ($bannerImageService, $force, &$processed, &$failed) {
                try {
                    $bannerImageService->generateResponsiveCopies($banner->image, $force);
                    $processed++;
                    $this->line('Generated responsive images for banner #' . $banner->id);
                } catch (\Throwable $exception) {
                    $failed++;
                    $this->warn('Failed banner #' . $banner->id . ': ' . $exception->getMessage());
                }
            });

        $this->info("Processed: {$processed}. Failed: {$failed}.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}

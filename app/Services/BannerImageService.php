<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class BannerImageService
{
    public const VARIANT_MOBILE = 'mobile';
    public const VARIANT_DESKTOP = 'desktop';

    private const WEBP_QUALITY = 88;

    private const VARIANTS = [
        self::VARIANT_MOBILE => 960,
        self::VARIANT_DESKTOP => 1296,
    ];

    public function generateResponsiveCopies(?string $imagePath, bool $force = false): array
    {
        if (!$imagePath || !Storage::exists($imagePath)) {
            return [];
        }

        $generated = [];

        foreach (self::VARIANTS as $variant => $targetWidth) {
            $generated[$variant] = $this->generateVariant($imagePath, $variant, $targetWidth, $force);
        }

        return $generated;
    }

    public function deleteResponsiveCopies(?string $imagePath): void
    {
        if (!$imagePath) {
            return;
        }

        foreach (array_keys(self::VARIANTS) as $variant) {
            Storage::delete($this->getVariantPath($imagePath, $variant));
        }
    }

    public function getVariantPath(string $imagePath, string $variant): string
    {
        $info = pathinfo($imagePath);

        return $info['dirname'] . '/' . $info['filename'] . '-' . $variant . '.webp';
    }

    public function getVariantUrl(?string $imagePath, string $variant): ?string
    {
        if (!$imagePath) {
            return null;
        }

        $variantPath = $this->getVariantPath($imagePath, $variant);

        return Storage::exists($variantPath) ? Storage::url($variantPath) : null;
    }

    private function generateVariant(string $imagePath, string $variant, int $targetWidth, bool $force): ?string
    {
        $sourcePath = Storage::path($imagePath);
        $variantPath = $this->getVariantPath($imagePath, $variant);
        $destinationPath = Storage::path($variantPath);

        if (!$force && file_exists($destinationPath) && filemtime($destinationPath) >= filemtime($sourcePath)) {
            return $variantPath;
        }

        $imageSize = getimagesize($sourcePath);
        if (!$imageSize) {
            throw new RuntimeException('Cannot read banner image size: ' . $imagePath);
        }

        [$sourceWidth, $sourceHeight] = $imageSize;
        $mime = $imageSize['mime'] ?? '';
        $newWidth = min($sourceWidth, $targetWidth);
        $newHeight = (int) round($sourceHeight * ($newWidth / $sourceWidth));

        $sourceImage = $this->createImageResource($sourcePath, $mime);
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);

        imagecopyresampled(
            $resizedImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $sourceWidth,
            $sourceHeight
        );

        $temporaryPath = $destinationPath . '.tmp';
        if (!imagewebp($resizedImage, $temporaryPath, self::WEBP_QUALITY)) {
            imagedestroy($sourceImage);
            imagedestroy($resizedImage);

            throw new RuntimeException('Cannot write banner WebP variant: ' . $variantPath);
        }

        imagedestroy($sourceImage);
        imagedestroy($resizedImage);

        rename($temporaryPath, $destinationPath);

        return $variantPath;
    }

    private function createImageResource(string $path, string $mime)
    {
        return match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/png' => imagecreatefrompng($path),
            'image/gif' => imagecreatefromgif($path),
            'image/webp' => imagecreatefromwebp($path),
            default => throw new RuntimeException('Unsupported banner image type: ' . $mime),
        };
    }

    public function safelyGenerateResponsiveCopies(?string $imagePath): void
    {
        try {
            $this->generateResponsiveCopies($imagePath);
        } catch (\Throwable $exception) {
            Log::warning('Banner responsive image generation failed', [
                'image' => $imagePath,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}

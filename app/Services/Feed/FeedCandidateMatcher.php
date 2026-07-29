<?php

namespace App\Services\Feed;

use App\Models\Feed\FeedProductLink;
use App\Models\Feed\FeedSource;
use App\Models\Shop\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class FeedCandidateMatcher
{
    public function __construct(private readonly FeedCategoryResolver $categoryResolver) {}

    public function candidates(FeedSource $source, array $offer, int $limit = 3): array
    {
        return $this->productQuery($source, $offer)->get()
            ->map(fn (Product $product) => [
                'product' => $product,
                'score' => $this->score($offer, $product),
            ])
            ->filter(fn (array $candidate) => $candidate['score'] >= 35)
            ->sortByDesc('score')
            ->take($limit)
            ->map(fn (array $candidate) => $this->serialize($candidate['product'], $candidate['score']))
            ->values()
            ->all();
    }

    public function exactCandidate(FeedSource $source, array $offer): ?array
    {
        $feedName = $this->normalize($offer['name']);
        $product = $this->productQuery($source, $offer)->get()
            ->first(fn (Product $product) => $this->normalize($product->name) === $feedName);

        return $product ? $this->serialize($product, 100) : null;
    }

    private function productQuery(FeedSource $source, array $offer): Builder
    {
        $category = $this->categoryResolver->resolve($source, $offer);
        $reservedProductIds = FeedProductLink::query()
            ->where('feed_source_id', $source->id)
            ->whereNotNull('product_id')
            ->pluck('product_id');
        $rolledBackProductIds = FeedProductLink::query()
            ->where('feed_source_id', $source->id)
            ->whereNotNull('metadata->rolled_back_product_id')
            ->get(['metadata'])
            ->map(fn (FeedProductLink $link) => (int) ($link->metadata['rolled_back_product_id'] ?? 0))
            ->filter();

        return Product::query()
            ->with('category')
            ->when($category, fn (Builder $query) => $query->where('category_id', $category->id))
            ->when(
                $reservedProductIds->isNotEmpty(),
                fn (Builder $query) => $query->whereNotIn('id', $reservedProductIds)
            )
            ->when(
                $rolledBackProductIds->isNotEmpty(),
                fn (Builder $query) => $query->whereNotIn('id', $rolledBackProductIds)
            );
    }

    private function score(array $offer, Product $product): float
    {
        $feedName = $this->normalize($offer['name']);
        $productName = $this->normalize($product->name);
        similar_text($feedName, $productName, $nameScore);

        $score = $nameScore * 0.75;
        $score += $this->tokenCompatibility($feedName, $productName, ['xl', 'l']) * 12.5;
        $score += $this->tokenCompatibility($feedName, $productName, ['тоннелем', 'тоннель']) * 7.5;

        $maxPrice = max((float) $offer['price'], (float) $product->price, 1);
        $priceDifference = abs((float) $offer['price'] - (float) $product->price) / $maxPrice;
        $score += max(0, 5 - ($priceDifference * 10));

        return round(min(100, $score), 1);
    }

    private function tokenCompatibility(string $left, string $right, array $tokens): float
    {
        $leftToken = $this->firstToken($left, $tokens);
        $rightToken = $this->firstToken($right, $tokens);

        if ($leftToken === null && $rightToken === null) {
            return 0.5;
        }

        return $leftToken === $rightToken ? 1 : -1;
    }

    private function firstToken(string $value, array $tokens): ?string
    {
        foreach ($tokens as $token) {
            if (preg_match('/(^|\s)'.preg_quote($token, '/').'(\s|$)/u', $value)) {
                return $token;
            }
        }

        return null;
    }

    private function normalize(string $value): string
    {
        $value = Str::lower(str_replace('ё', 'е', $value));
        $value = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $value);

        return trim((string) preg_replace('/\s+/u', ' ', $value));
    }

    private function serialize(Product $product, float $score): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'category' => $product->category?->name,
            'image_url' => $product->image_url,
            'score' => $score,
        ];
    }
}

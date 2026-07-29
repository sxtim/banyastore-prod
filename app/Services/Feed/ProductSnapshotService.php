<?php

namespace App\Services\Feed;

use App\Models\Shop\Product;

class ProductSnapshotService
{
    public function capture(Product $product): array
    {
        $product->loadMissing(['additionalImages', 'propertiesValues']);

        return [
            'kind' => 'existing',
            'product_id' => $product->id,
            'price' => $product->price,
            'discount_id' => $product->discount_id,
            'description' => $product->description,
            'is_active' => $product->is_active,
            'image' => $product->image,
            'additional_images' => $product->additionalImages->pluck('file_path')->all(),
            'property_value_ids' => $product->propertiesValues->pluck('id')->all(),
            'image_backups' => [],
        ];
    }

    public function created(Product $product, ?array $feedDiscount = null): array
    {
        return [
            'kind' => 'created',
            'product_id' => $product->id,
            'feed_discount' => $feedDiscount,
        ];
    }
}

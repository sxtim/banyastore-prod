<?php

namespace App\Services\Seo;

use App\DTO\Seo\SeoDto;
use App\Models\SeoTemplate;
use App\Models\Shop\Category;
use App\Models\Shop\Product;

class SeoTemplateService
{
    public function getTemplateProduct(Product $product): SeoDto
    {
        $seoDto = new SeoDto();
        $titleDefault = 'Купить - '.$product->name.' - цена - '.$product->getCurrentPrice().'  в Banyastore.
        Ежедневная доставка по Москве и России';
        $seoDto->setTitle($titleDefault);

        $descriptionDefault = 'Заказывайте '.$product->name.' в интернет-магазине Banyastore (Ферингер опт). Большой выбор, доставка, помощь в выборе.
        Поможем подобрать '.$product->name.' под ваш запрос быстро и четко';
        $seoDto->setDescription($descriptionDefault);

        $seoTitle = $this->replaceDataProduct($product, SeoTemplate::TYPE_TEMPLATE_TITLE);
        if ($seoTitle) {
            $seoDto->setTitle($seoTitle);
        }
        $seoDescription = $this->replaceDataProduct($product, SeoTemplate::TYPE_TEMPLATE_DESCRIPTION);
        if ($seoDescription) {
            $seoDto->setDescription($seoDescription);
        }

        return $seoDto;
    }

    private function replaceDataProduct(Product $product, string $templateType): ?string
    {
        $templateProduct = SeoTemplate::where('category_id', $product->category_id)
            ->where('type_material', SeoTemplate::MATERIAL_TYPE_PRODUCT)
            ->where('type_template', $templateType)
            ->where('is_main', false)
            ->first();
        if (!$templateProduct) {
            $templateProduct = SeoTemplate::where('type_material', SeoTemplate::MATERIAL_TYPE_PRODUCT)
                ->where('type_template', $templateType)
                ->where('is_main', true)
                ->first();
        }
        if ($templateProduct) {
            $title = str_replace('#NAME#', $product->name, $templateProduct->text_template);
            $title = str_replace('#PRICE#', $product->price, $title);
            foreach ($product->propertiesValues as $value) {
                $title = str_replace('#PROP_'.$value->property->id.'#', $value->name, $title);
            }
            return preg_replace('/#.*?#/', '', $title);
        }

        return null;
    }

    public function getTemplateCategory(Category $category): SeoDto
    {
        $seoDto = new SeoDto();
        $titleDefault = 'Купить - '.$category->name.' -  в Banyastore. Ежедневная доставка по Москве и России';
        $seoDto->setTitle($titleDefault);

        $descriptionDefault = 'Заказывайте '.$category->name.' в интернет-магазине Banyastore (Ферингер опт). Большой выбор, доставка, помощь в выборе.
        Поможем подобрать '.$category->name.' под ваш запрос быстро и четко';
        $seoDto->setDescription($descriptionDefault);

        $seoTitle = $this->replaceDataCategory($category, SeoTemplate::TYPE_TEMPLATE_TITLE);
        if ($seoTitle) {
            $seoDto->setTitle($seoTitle);
        }
        $seoDescription = $this->replaceDataCategory($category, SeoTemplate::TYPE_TEMPLATE_DESCRIPTION);
        if ($seoDescription) {
            $seoDto->setDescription($seoDescription);
        }

        return $seoDto;
    }

    private function replaceDataCategory(Category $category, string $templateType): ?string
    {
        $templateCategory = SeoTemplate::where('category_id', $category->id)
            ->where('type_material', SeoTemplate::MATERIAL_TYPE_CATEGORY)
            ->where('type_template', $templateType)
            ->where('is_main', false)
            ->first();
        if (!$templateCategory) {
            $templateCategory = SeoTemplate::where('type_material', SeoTemplate::MATERIAL_TYPE_CATEGORY)
                ->where('type_template', $templateType)
                ->where('is_main', true)
                ->first();
        }
        if ($templateCategory) {
            $text = str_replace('#NAME#', $category->name, $templateCategory->text_template);
            return preg_replace('/#.*?#/', '', $text);
        }

        return null;
    }
}


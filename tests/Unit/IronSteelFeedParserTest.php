<?php

namespace Tests\Unit;

use App\Services\Feed\FeedDescriptionSanitizer;
use App\Services\Feed\FeedException;
use App\Services\Feed\FeedValueNormalizer;
use App\Services\Feed\IronSteelBrandSeriesResolver;
use App\Services\Feed\IronSteelFeedParser;
use App\Services\Feed\IronSteelOfferNormalizer;
use PHPUnit\Framework\TestCase;

class IronSteelFeedParserTest extends TestCase
{
    public function test_it_parses_feed_fields_and_parameters(): void
    {
        $parsed = $this->parser()->parse(
            file_get_contents(__DIR__.'/../Fixtures/iron-steel-feed.xml')
        );

        $this->assertCount(3, $parsed['offers']);
        $this->assertSame('group-100', $parsed['offers']['1001']['group_id']);
        $this->assertSame('ПроМеталл', $parsed['offers']['1001']['vendor']);
        $this->assertSame('p1001', $parsed['offers']['1001']['vendor_code']);
        $this->assertSame(150000.0, $parsed['offers']['1001']['price']);
        $this->assertNull($parsed['offers']['1001']['old_price']);
        $this->assertSame(
            [
                'Объем парной' => '12-20 м³',
                'Бренд' => 'ПроМеталл',
                'Серия' => 'Атмосфера',
            ],
            $parsed['offers']['1001']['params']
        );
        $this->assertSame('197 кг', $parsed['offers']['1001']['raw_params']['Масса печи']);
        $this->assertSame('200 кг', $parsed['offers']['1001']['description_params']['Масса печи']);
        $this->assertSame('Масса печи', $parsed['offers']['1001']['property_conflicts'][0]['name']);
        $this->assertCount(2, $parsed['offers']['1001']['pictures']);
        $this->assertSame('Комбинированная отделка', $parsed['offers']['1001']['category_name']);
    }

    public function test_it_rejects_invalid_xml(): void
    {
        $this->expectException(FeedException::class);

        $this->parser()->parse('<broken>');
    }

    public function test_it_rejects_duplicate_offer_ids(): void
    {
        $xml = file_get_contents(__DIR__.'/../Fixtures/iron-steel-feed.xml');
        $offer = '<offer id="1001"><name>Дубль</name><price>1</price><categoryId>741483991681</categoryId></offer>';
        $xml = str_replace('</offers>', $offer.'</offers>', $xml);

        $this->expectException(FeedException::class);
        $this->expectExceptionMessage('повторяется offer id 1001');

        $this->parser()->parse($xml);
    }

    public function test_it_rejects_non_positive_price(): void
    {
        $xml = file_get_contents(__DIR__.'/../Fixtures/iron-steel-feed.xml');
        $xml = str_replace('<price>150000.00</price>', '<price>0</price>', $xml);

        $this->expectException(FeedException::class);
        $this->expectExceptionMessage('некорректная цена');

        $this->parser()->parse($xml);
    }

    public function test_it_parses_old_price(): void
    {
        $xml = file_get_contents(__DIR__.'/../Fixtures/iron-steel-feed.xml');
        $xml = str_replace(
            '<price>150000.00</price>',
            '<price>150000.00</price><oldprice>165000</oldprice>',
            $xml
        );

        $parsed = $this->parser()->parse($xml);

        $this->assertSame(150000.0, $parsed['offers']['1001']['price']);
        $this->assertSame(165000.0, $parsed['offers']['1001']['old_price']);
    }

    public function test_it_rejects_old_price_not_greater_than_current_price(): void
    {
        $xml = file_get_contents(__DIR__.'/../Fixtures/iron-steel-feed.xml');
        $xml = str_replace(
            '<price>150000.00</price>',
            '<price>150000.00</price><oldprice>140000</oldprice>',
            $xml
        );

        $this->expectException(FeedException::class);
        $this->expectExceptionMessage('некорректная старая цена');

        $this->parser()->parse($xml);
    }

    private function parser(): IronSteelFeedParser
    {
        $values = new FeedValueNormalizer;

        return new IronSteelFeedParser(
            new IronSteelOfferNormalizer($values, new FeedDescriptionSanitizer),
            new IronSteelBrandSeriesResolver
        );
    }
}

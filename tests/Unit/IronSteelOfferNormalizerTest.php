<?php

namespace Tests\Unit;

use App\Services\Feed\FeedDescriptionSanitizer;
use App\Services\Feed\FeedValueNormalizer;
use App\Services\Feed\IronSteelOfferNormalizer;
use PHPUnit\Framework\TestCase;

class IronSteelOfferNormalizerTest extends TestCase
{
    public function test_it_extracts_only_known_properties_and_cleans_the_description(): void
    {
        $description = <<<'HTML'
Объем топки - 48 литров<br />
Размер экрана - 240х270 мм<br />
Размер (ВхДхШ) 785х660х560 мм<br />
Размер в упаковке (ВхДхШ) 950х735х770 мм<br />
Материал корпуса - Чугун ЧХ-1<br />
Масса печи с сеткой - 119кг<br />
Объем парной до 14 м3<br />
Объем закрытой каменки - 13 литров<br />
Масса камней в каменке - 18-25 кг<br />
Масса камней в сетке 90-110 кг<br />
Гарантия 36 месяцев<br /><br />
Ультра компактная модель банной печи.
HTML;

        $result = $this->normalizer()->normalize([], $description);

        $this->assertSame('240×270 мм', $result['params']['Размер экрана']);
        $this->assertSame('785×660×560 мм', $result['params']['Размер товара']);
        $this->assertSame('119 кг', $result['params']['Масса печи']);
        $this->assertSame('до 14 м³', $result['params']['Объем парной']);
        $this->assertSame('90-110 кг', $result['params']['Масса камней в сетке']);
        $this->assertStringNotContainsString('Размер в упаковке', $result['description']);
        $this->assertSame(
            ['Размер в упаковке (ВхДхШ) 950×735×770 мм'],
            $result['packaging_lines']
        );
        $this->assertStringNotContainsString('Масса печи с сеткой', $result['description']);
        $this->assertStringContainsString('Гарантия 36 месяцев', $result['description']);
        $this->assertStringContainsString('Ультра компактная модель', $result['description']);
        $this->assertSame(['Гарантия 36 месяцев'], $result['unmapped_description_lines']);
    }

    public function test_param_has_priority_over_description(): void
    {
        $result = $this->normalizer()->normalize(
            ['Масса камней в сетке' => '120 кг'],
            'Масса камней в сетке 120-150 кг<br /><br />Описание'
        );

        $this->assertSame('120 кг', $result['params']['Масса камней в сетке']);
        $this->assertArrayNotHasKey('property_conflicts', $result);
        $this->assertSame('Описание', $result['description']);
    }

    public function test_equivalent_units_do_not_create_a_false_conflict(): void
    {
        $result = $this->normalizer()->normalize(
            ['Объем парной' => '12-20 м3'],
            'Объем парной - 12-20 м³<br /><br />Описание'
        );

        $this->assertSame(['Объем парной' => '12-20 м³'], $result['params']);
        $this->assertArrayNotHasKey('property_conflicts', $result);
        $this->assertSame('Описание', $result['description']);
    }

    public function test_packaging_lines_are_removed_even_without_a_separator(): void
    {
        $result = $this->normalizer()->normalize(
            [],
            implode("\n", [
                'Размер в упаковке (ВхДхШ)1120х880х770 мм',
                'Размеры упаковки облицовки (ВхДхШ) 500х850х650 мм',
                'Масса облицовки в упаковке - 230 кг',
                'Масса облицовки без упаковки - 210 кг',
                'Нормальное описание товара.',
            ])
        );

        $this->assertStringNotContainsString('Размер в упаковке', $result['description']);
        $this->assertStringNotContainsString('Размеры упаковки', $result['description']);
        $this->assertStringNotContainsString('в упаковке', $result['description']);
        $this->assertStringContainsString('Масса облицовки без упаковки', $result['description']);
        $this->assertStringContainsString('Нормальное описание товара.', $result['description']);
        $this->assertSame([
            'Размер в упаковке (ВхДхШ) 1120×880×770 мм',
            'Размеры упаковки облицовки (ВхДхШ) 500×850×650 мм',
            'Масса облицовки в упаковке - 230 кг',
        ], $result['packaging_lines']);
    }

    public function test_it_normalizes_full_feed_parameters_and_splits_insulation(): void
    {
        $result = $this->normalizer()->normalize([
            'Объём' => '50л',
            'Сталь' => 'AISI 430',
            'Диаметр круга' => '115х200 мм',
            'Модель' => 'KRAFT',
            'Цвет | Теплоизоляция' => 'черный | керамика',
        ], '');

        $this->assertSame('50 л', $result['params']['Объем']);
        $this->assertSame('AISI 430', $result['params']['Марка стали']);
        $this->assertSame('115×200 мм', $result['params']['Диаметр круга']);
        $this->assertSame('KRAFT', $result['params']['Модель']);
        $this->assertSame('черный', $result['params']['Цвет']);
        $this->assertSame('керамика', $result['params']['Теплоизоляция']);
        $this->assertSame(
            'черный | керамика',
            $result['raw_params']['Цвет | Теплоизоляция']
        );
    }

    public function test_it_removes_supplier_page_controls_but_keeps_useful_tail_content(): void
    {
        $result = $this->normalizer()->normalize([], implode("\n", [
            'Нормальное описание товара.',
            'Гарантия 5 лет подробнее →',
            '__________________________________',
            'Скачать 3d модель',
            'Открыть паспорт на печь',
            '↓ Полные характеристики печи ↓',
            'Толщина трубы - 3 мм',
        ]));

        $this->assertSame(implode("\n", [
            'Нормальное описание товара.',
            'Гарантия 5 лет',
            'Толщина трубы - 3 мм',
        ]), $result['description']);
        $this->assertSame(
            ['Гарантия 5 лет', 'Толщина трубы - 3 мм'],
            $result['unmapped_description_lines']
        );
    }

    private function normalizer(): IronSteelOfferNormalizer
    {
        return new IronSteelOfferNormalizer(
            new FeedValueNormalizer,
            new FeedDescriptionSanitizer
        );
    }
}

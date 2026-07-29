<?php

namespace Tests\Unit;

use App\Services\Feed\IronSteelBrandSeriesResolver;
use PHPUnit\Framework\TestCase;

class IronSteelBrandSeriesResolverTest extends TestCase
{
    public function test_it_uses_vendor_as_brand_and_stove_name_as_series(): void
    {
        $result = $this->resolver()->resolve(
            ['Бренд' => 'Атмосфера'],
            'Печь банная «Атмосфера XL» в ламелях',
            'ПроМеталл',
            'Банные печи в ламелях'
        );

        $this->assertSame('ПроМеталл', $result['Бренд']);
        $this->assertSame('Атмосфера', $result['Серия']);
    }

    public function test_it_normalizes_legacy_and_vendor_brand_spellings(): void
    {
        $this->assertSame(
            'ПроМеталл',
            $this->resolver()->resolve(
                ['Бренд' => 'Prometall'],
                'Ковш для бани',
                null,
                'Аксессуары'
            )['Бренд']
        );
        $this->assertSame(
            'Крафт',
            $this->resolver()->resolve([], 'Труба', 'КРАФТ', 'Дымоходы')['Бренд']
        );
        $this->assertSame(
            'Феррум',
            $this->resolver()->resolve([], 'Бак', 'Ferrum', 'Бак для воды')['Бренд']
        );
    }

    public function test_accessory_for_a_stove_does_not_receive_its_series(): void
    {
        $result = $this->resolver()->resolve(
            ['Бренд' => 'ПроМеталл'],
            'Полки для подогрева к печи отопительной «Тайга» Про',
            'ПроМеталл',
            'Отопительные печи'
        );

        $this->assertSame('ПроМеталл', $result['Бренд']);
        $this->assertArrayNotHasKey('Серия', $result);
    }

    public function test_it_does_not_clear_brand_when_feed_has_no_brand(): void
    {
        $result = $this->resolver()->resolve(
            [],
            'Комплект дымохода',
            null,
            'Специальные дымоходы'
        );

        $this->assertArrayNotHasKey('Бренд', $result);
        $this->assertArrayNotHasKey('Серия', $result);
    }

    public function test_it_groups_model_variants_under_one_series(): void
    {
        foreach ([
            ['Печь-камин «Маэстро II»', 'Маэстро'],
            ['Печь-камин «Бахта II»', 'Бахта'],
            ['Печь отопительная «Тайга PRO»', 'Тайга'],
            ['Печь отопительная «Бахтинка»', 'Бахтинка'],
            ['Печь отопительная «Буредан»', 'Буредан'],
        ] as [$name, $series]) {
            $result = $this->resolver()->resolve(
                ['Бренд' => $series],
                $name,
                'ПроМеталл',
                'Отопительные печи'
            );

            $this->assertSame('ПроМеталл', $result['Бренд']);
            $this->assertSame($series, $result['Серия']);
        }
    }

    private function resolver(): IronSteelBrandSeriesResolver
    {
        return new IronSteelBrandSeriesResolver;
    }
}

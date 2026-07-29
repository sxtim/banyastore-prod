<?php

namespace Tests\Unit;

use App\Services\Feed\FeedDescriptionFactory;
use Tests\TestCase;

class FeedDescriptionFactoryTest extends TestCase
{
    public function test_it_adds_packaging_as_separate_editor_blocks(): void
    {
        $description = app(FeedDescriptionFactory::class)->make(
            'Описание товара',
            [
                'Размер упаковки 330×260×220 мм',
                'Масса в упаковке - 15 кг',
            ]
        );

        $this->assertSame('Описание товара', $description['blocks'][0]['data']['text']);
        $this->assertSame('header', $description['blocks'][1]['type']);
        $this->assertSame('Упаковка', $description['blocks'][1]['data']['text']);
        $this->assertSame(3, $description['blocks'][1]['data']['level']);
        $this->assertSame(
            "Размер упаковки 330×260×220 мм<br>\nМасса в упаковке - 15 кг",
            $description['blocks'][2]['data']['text']
        );
    }
}

<?php

namespace Tests\Feature;

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RoleMiddleware;
use App\Jobs\BuildFeedPreview;
use App\Models\Feed\FeedImportRun;
use App\Models\Feed\FeedProductLink;
use App\Models\SeoTemplate;
use App\Models\Shop\Category;
use App\Models\Shop\Discount;
use App\Models\Shop\Product;
use App\Models\Shop\Property\Property;
use App\Models\Shop\Property\PropertyValue;
use App\Services\Feed\FeedApplyService;
use App\Services\Feed\FeedException;
use App\Services\Feed\FeedPreviewService;
use App\Services\Feed\FeedProductImporter;
use App\Services\Feed\FeedProductRollback;
use App\Services\Feed\FeedRollbackService;
use App\Services\Feed\IronSteelSetupService;
use App\Services\Seo\SeoTemplateService;
use Illuminate\Bus\PendingBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FeedImportTest extends TestCase
{
    use RefreshDatabase;

    private string $feedXml;

    protected function setUp(): void
    {
        parent::setUp();

        $this->feedXml = file_get_contents(__DIR__.'/../Fixtures/iron-steel-feed.xml');
        foreach ([
            'Печи для бани и сауны',
            'Отопительные печи',
            'Специальные дымоходы',
            'Аксессуары',
            'Облицовка камнем',
        ] as $index => $name) {
            Category::query()->create([
                'name' => $name,
                'is_active' => true,
                'sort' => $index,
                'image' => '',
            ]);
        }

        Storage::fake('local');
        config()->set('feed_import.http.allowed_image_hosts', ['images.test']);
        Http::fake([
            config('feed_import.iron_steel.url') => Http::response($this->feedXml, 200),
        ]);
    }

    public function test_setup_switches_the_existing_source_and_preserves_product_links(): void
    {
        config()->set('feed_import.iron_steel.name', 'Iron&Steel');
        config()->set('feed_import.iron_steel.url', 'https://ironandsteel.ru/old-feed.yml');

        $source = app(IronSteelSetupService::class)->sync();
        $product = $this->product('Связанный товар', 100000);
        $link = FeedProductLink::query()->create([
            'feed_source_id' => $source->id,
            'offer_id' => '1001',
            'product_id' => $product->id,
            'decision' => FeedProductLink::DECISION_LINK,
        ]);

        config()->set('feed_import.iron_steel.name', 'ProMetall');
        config()->set(
            'feed_import.iron_steel.url',
            'https://prometall.ru/tstore/yml/full-feed.yml'
        );

        $updatedSource = app(IronSteelSetupService::class)->sync();

        $this->assertSame($source->id, $updatedSource->id);
        $this->assertSame('iron-steel', $updatedSource->slug);
        $this->assertSame('ProMetall', $updatedSource->name);
        $this->assertSame(
            'https://prometall.ru/tstore/yml/full-feed.yml',
            $updatedSource->url
        );
        $this->assertDatabaseHas('feed_product_links', [
            'id' => $link->id,
            'feed_source_id' => $source->id,
            'offer_id' => '1001',
            'product_id' => $product->id,
            'decision' => FeedProductLink::DECISION_LINK,
        ]);
        $this->assertDatabaseHas('categories', [
            'name' => 'Баки для воды',
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('feed_category_mappings', [
            'feed_source_id' => $source->id,
            'external_id' => '722232690111',
            'category_id' => Category::query()
                ->where('name', 'Специальные дымоходы')
                ->value('id'),
            'is_excluded' => false,
        ]);
        $this->assertDatabaseHas('feed_category_mappings', [
            'feed_source_id' => $source->id,
            'external_id' => '443122286032',
            'category_id' => null,
            'is_excluded' => true,
        ]);
        $this->assertDatabaseHas('feed_property_mappings', [
            'feed_source_id' => $source->id,
            'external_name' => 'Серия',
            'property_id' => Property::query()->where('name', 'Серия')->value('id'),
            'target_name' => 'Серия',
        ]);
    }

    public function test_setup_merges_legacy_brand_aliases_without_duplicate_assignments(): void
    {
        $brand = Property::query()->create([
            'name' => 'Бренд',
            'is_required' => false,
        ]);
        $proMetall = PropertyValue::query()->create([
            'property_id' => $brand->id,
            'name' => 'ПроМеталл',
        ]);
        $prometall = PropertyValue::query()->create([
            'property_id' => $brand->id,
            'name' => 'Prometall',
        ]);
        $craft = PropertyValue::query()->create([
            'property_id' => $brand->id,
            'name' => 'Craft',
        ]);
        $ferrum = PropertyValue::query()->create([
            'property_id' => $brand->id,
            'name' => 'Ferrum',
        ]);
        $unrelated = PropertyValue::query()->create([
            'property_id' => $brand->id,
            'name' => 'Другой бренд',
        ]);

        $prometallProduct = $this->product('Prometall', 100000);
        $prometallProduct->propertiesValues()->attach([$prometall->id, $proMetall->id]);
        $craftProduct = $this->product('Craft', 100000);
        $craftProduct->propertiesValues()->attach($craft);
        $ferrumProduct = $this->product('Ferrum', 100000);
        $ferrumProduct->propertiesValues()->attach($ferrum);
        $unrelatedProduct = $this->product('Другой бренд', 100000);
        $unrelatedProduct->propertiesValues()->attach($unrelated);

        app(IronSteelSetupService::class)->sync();
        app(IronSteelSetupService::class)->sync();

        $craftCanonical = PropertyValue::query()
            ->where('property_id', $brand->id)
            ->where('name', 'Крафт')
            ->firstOrFail();
        $ferrumCanonical = PropertyValue::query()
            ->where('property_id', $brand->id)
            ->where('name', 'Феррум')
            ->firstOrFail();

        $this->assertSame(
            [$proMetall->id],
            $prometallProduct->propertiesValues()->pluck('property_values.id')->all()
        );
        $this->assertSame(
            [$craftCanonical->id],
            $craftProduct->propertiesValues()->pluck('property_values.id')->all()
        );
        $this->assertSame(
            [$ferrumCanonical->id],
            $ferrumProduct->propertiesValues()->pluck('property_values.id')->all()
        );
        $this->assertSame(
            [$unrelated->id],
            $unrelatedProduct->propertiesValues()->pluck('property_values.id')->all()
        );
        $this->assertSoftDeleted($prometall);
        $this->assertSoftDeleted($craft);
        $this->assertSoftDeleted($ferrum);
    }

    public function test_preview_does_not_change_catalog_and_groups_offers(): void
    {
        $source = app(IronSteelSetupService::class)->sync();
        $product = $this->product('Наше название', 140000);
        FeedProductLink::query()->create([
            'feed_source_id' => $source->id,
            'offer_id' => '1001',
            'product_id' => $product->id,
            'decision' => FeedProductLink::DECISION_LINK,
        ]);
        FeedProductLink::query()->create([
            'feed_source_id' => $source->id,
            'offer_id' => '1002',
            'decision' => FeedProductLink::DECISION_CREATE,
        ]);

        $run = app(FeedPreviewService::class)->create($source, null);

        $this->assertSame([
            'total' => 3,
            'update' => 1,
            'create' => 1,
            'pending' => 0,
            'excluded' => 1,
            'removed' => 0,
            'errors' => 0,
        ], $run->summary);
        $this->assertSame(140000.0, $product->fresh()->price);
        Storage::assertExists($run->snapshot_path);
        $updateItem = $run->items()->where('offer_id', '1001')->firstOrFail();
        $this->assertSame('197 кг', $updateItem->diff['feed_properties']['Масса печи']);
        $this->assertArrayNotHasKey('property_conflicts', $updateItem->diff);
        $this->assertSame('ПроМеталл', $updateItem->diff['feed_vendor']);
        $this->assertSame('group-100', $updateItem->diff['feed_group_id']);
        $this->assertSame('ПроМеталл', $updateItem->link->metadata['feed_vendor']);
        $this->assertSame('group-100', $updateItem->link->metadata['feed_group_id']);
        $this->assertSame('update', $updateItem->action);
    }

    public function test_import_screen_hides_technical_property_sources(): void
    {
        $source = app(IronSteelSetupService::class)->sync();
        $product = $this->product('Наше название', 140000);
        FeedProductLink::query()->create([
            'feed_source_id' => $source->id,
            'offer_id' => '1001',
            'product_id' => $product->id,
            'decision' => FeedProductLink::DECISION_LINK,
        ]);

        $run = app(FeedPreviewService::class)->create($source, null);

        $response = $this
            ->withoutMiddleware([Authenticate::class, RoleMiddleware::class])
            ->get(route('backend.feed-import.index'));

        $response->assertOk()
            ->assertSee('Характеристики после импорта')
            ->assertSee('Исправить связь')
            ->assertSee('offer id 1001')
            ->assertDontSee('Расхождения свойств')
            ->assertDontSee('Параметры &lt;param&gt;', false)
            ->assertDontSee('Извлечено из описания')
            ->assertDontSee('Описание:</strong>', false)
            ->assertDontSee('Связан не с тем товаром');
        $this->assertArrayNotHasKey('property_conflicts', $run->summary);
    }

    public function test_preview_reports_only_linked_products_missing_from_feed(): void
    {
        $source = app(IronSteelSetupService::class)->sync();
        $product = $this->product('Исчезнувший связанный товар', 50000);
        FeedProductLink::query()->create([
            'feed_source_id' => $source->id,
            'offer_id' => 'missing-linked',
            'product_id' => $product->id,
            'decision' => FeedProductLink::DECISION_LINK,
        ]);
        FeedProductLink::query()->create([
            'feed_source_id' => $source->id,
            'offer_id' => 'missing-never-created',
            'decision' => FeedProductLink::DECISION_CREATE,
        ]);

        $run = app(FeedPreviewService::class)->create($source, null);

        $this->assertSame(1, $run->summary['removed']);
        $this->assertDatabaseHas('feed_import_items', [
            'feed_import_run_id' => $run->id,
            'offer_id' => 'missing-linked',
            'product_id' => $product->id,
            'action' => 'removed',
        ]);
        $this->assertDatabaseMissing('feed_import_items', [
            'feed_import_run_id' => $run->id,
            'offer_id' => 'missing-never-created',
        ]);
    }

    public function test_preview_request_is_dispatched_to_the_queue(): void
    {
        Bus::fake();
        $source = app(IronSteelSetupService::class)->sync();

        $response = $this
            ->withoutMiddleware([Authenticate::class, RoleMiddleware::class])
            ->post(route('backend.feed-import.preview'));

        $response->assertRedirect(route('backend.feed-import.index'));
        $run = $source->runs()->latest('id')->firstOrFail();
        $this->assertSame(FeedImportRun::STATUS_PREPARING, $run->status);
        Bus::assertDispatched(
            BuildFeedPreview::class,
            fn (BuildFeedPreview $job) => $job->runId === $run->id
        );
    }

    public function test_failed_preview_does_not_replace_the_last_successful_report(): void
    {
        $source = app(IronSteelSetupService::class)->sync();
        $ready = FeedImportRun::query()->create([
            'feed_source_id' => $source->id,
            'kind' => FeedImportRun::KIND_PREVIEW,
            'status' => FeedImportRun::STATUS_READY,
            'summary' => ['total' => 163, 'update' => 71],
            'snapshot_hash' => 'successful-snapshot',
            'feed_generated_at' => now()->subDay(),
            'started_at' => now()->subMinute(),
            'finished_at' => now(),
        ]);
        $failed = FeedImportRun::query()->create([
            'feed_source_id' => $source->id,
            'kind' => FeedImportRun::KIND_PREVIEW,
            'status' => FeedImportRun::STATUS_FAILED,
            'error' => 'Не удалось скачать фид.',
            'started_at' => now(),
            'finished_at' => now(),
        ]);

        $response = $this
            ->withoutMiddleware([Authenticate::class, RoleMiddleware::class])
            ->get(route('backend.feed-import.index'));

        $response->assertOk()
            ->assertSee("Проверка #{$failed->id} не выполнена")
            ->assertSee('Не удалось скачать фид.')
            ->assertSee("Проверка #{$ready->id}, снимок")
            ->assertSee('71');
    }

    public function test_stale_preview_cannot_be_applied(): void
    {
        $source = app(IronSteelSetupService::class)->sync();
        $preview = FeedImportRun::query()->create([
            'feed_source_id' => $source->id,
            'kind' => FeedImportRun::KIND_PREVIEW,
            'status' => FeedImportRun::STATUS_READY,
        ]);
        $this->applyRun($source->id)->update(['status' => FeedImportRun::STATUS_COMPLETED]);

        $this->expectException(FeedException::class);
        $this->expectExceptionMessage('Проверка устарела');

        app(FeedApplyService::class)->start($preview, null);
    }

    public function test_active_operation_blocks_another_start(): void
    {
        $source = app(IronSteelSetupService::class)->sync();
        $preview = FeedImportRun::query()->create([
            'feed_source_id' => $source->id,
            'kind' => FeedImportRun::KIND_PREVIEW,
            'status' => FeedImportRun::STATUS_READY,
        ]);
        FeedImportRun::query()->create([
            'feed_source_id' => $source->id,
            'kind' => FeedImportRun::KIND_PREVIEW,
            'status' => FeedImportRun::STATUS_PREPARING,
        ]);

        $this->expectException(FeedException::class);
        $this->expectExceptionMessage('Другая операция уже выполняется');

        app(FeedApplyService::class)->start($preview, null);
    }

    public function test_older_import_cannot_be_rolled_back_after_latest_was_rolled_back(): void
    {
        $source = app(IronSteelSetupService::class)->sync();
        $older = $this->applyRun($source->id);
        $older->update(['status' => FeedImportRun::STATUS_COMPLETED]);
        $newer = $this->applyRun($source->id);
        $newer->update(['status' => FeedImportRun::STATUS_ROLLED_BACK]);

        $this->expectException(FeedException::class);
        $this->expectExceptionMessage('Разрешён откат только последнего импорта');

        app(FeedRollbackService::class)->start($older, null);
    }

    public function test_create_decision_with_exact_catalog_name_requires_confirmation(): void
    {
        $source = app(IronSteelSetupService::class)->sync();
        $existingProduct = $this->product('Облицовка стены "Пироксенит"', 45000);
        $existingProduct->update([
            'category_id' => Category::query()->where('name', 'Облицовка камнем')->value('id'),
        ]);
        $rolledBackProduct = $this->product('Облицовка стены «Пироксенит»', 48300);
        $rolledBackProduct->update([
            'category_id' => Category::query()->where('name', 'Облицовка камнем')->value('id'),
            'is_active' => false,
        ]);
        $linkedProduct = $this->product('Наше название', 140000);

        FeedProductLink::query()->create([
            'feed_source_id' => $source->id,
            'offer_id' => 'old-1002',
            'decision' => FeedProductLink::DECISION_CREATE,
            'metadata' => [
                'created_by_feed' => true,
                'rolled_back_product_id' => $rolledBackProduct->id,
            ],
        ]);
        FeedProductLink::query()->create([
            'feed_source_id' => $source->id,
            'offer_id' => '1001',
            'product_id' => $linkedProduct->id,
            'decision' => FeedProductLink::DECISION_LINK,
        ]);
        FeedProductLink::query()->create([
            'feed_source_id' => $source->id,
            'offer_id' => '1002',
            'decision' => FeedProductLink::DECISION_CREATE,
        ]);

        $run = app(FeedPreviewService::class)->create($source, null);
        $item = $run->items()->where('offer_id', '1002')->firstOrFail();

        $this->assertSame('pending', $item->action);
        $this->assertSame('pending', $item->status);
        $this->assertStringContainsString("ID {$existingProduct->id}", $item->error);
        $this->assertSame($existingProduct->id, $item->diff['candidates'][0]['id']);
        $this->assertNotSame($rolledBackProduct->id, $item->diff['candidates'][0]['id']);
        $this->assertSame(1, $run->summary['pending']);
        $this->assertSame(0, $run->summary['create']);
    }

    public function test_only_failed_items_are_retried_in_the_same_run(): void
    {
        Bus::fake();
        $source = app(IronSteelSetupService::class)->sync();
        $run = FeedImportRun::query()->create([
            'feed_source_id' => $source->id,
            'kind' => FeedImportRun::KIND_APPLY,
            'status' => FeedImportRun::STATUS_COMPLETED_WITH_ERRORS,
            'summary' => ['error' => 1],
        ]);
        $item = $run->items()->create([
            'offer_id' => '1001',
            'action' => 'update',
            'status' => 'error',
            'feed_payload' => $this->offer('1001'),
            'error' => 'Временная ошибка',
        ]);

        $retriedRun = app(FeedApplyService::class)->retryErrors($run, null);

        $this->assertSame($run->id, $retriedRun->id);
        $this->assertSame(FeedImportRun::STATUS_RUNNING, $retriedRun->status);
        $this->assertSame('ready', $item->fresh()->status);
        $this->assertNull($item->fresh()->error);
        Bus::assertBatched(
            fn (PendingBatch $batch) => $batch->jobs->count() === 1
                && $batch->name === "Feed import retry {$run->id}"
        );
    }

    public function test_existing_product_keeps_owned_fields_and_replaces_feed_data_and_images(): void
    {
        $source = app(IronSteelSetupService::class)->sync();
        $product = $this->product('Наше название', 140000);
        $product->update([
            'description' => ['blocks' => [[
                'type' => 'paragraph',
                'data' => [
                    'text' => "Наше описание<br>\n__________________________________<br>\nСкачать 3d модель",
                ],
            ]]],
            'image' => 'public/products/old.jpg',
        ]);
        Storage::put('public/products/old.jpg', $this->png());

        $brand = Property::query()->where('name', 'Бренд')->firstOrCreate([
            'name' => 'Бренд',
        ], ['is_required' => false]);
        $brandValue = PropertyValue::query()->create(['property_id' => $brand->id, 'name' => 'Наш бренд']);
        $product->propertiesValues()->attach($brandValue);
        $series = Property::query()->where('name', 'Серия')->firstOrCreate([
            'name' => 'Серия',
        ], ['is_required' => false]);
        $seriesValue = PropertyValue::query()->create(['property_id' => $series->id, 'name' => 'Старая серия']);
        $product->propertiesValues()->attach($seriesValue);

        $link = FeedProductLink::query()->create([
            'feed_source_id' => $source->id,
            'offer_id' => '1001',
            'product_id' => $product->id,
            'decision' => FeedProductLink::DECISION_LINK,
        ]);
        $run = $this->applyRun($source->id);
        $item = $run->items()->create([
            'feed_product_link_id' => $link->id,
            'product_id' => $product->id,
            'offer_id' => '1001',
            'action' => 'update',
            'status' => 'ready',
            'feed_payload' => $this->offer('1001'),
        ]);
        Http::fake(['https://images.test/*' => Http::response($this->png(), 200, ['Content-Type' => 'image/png'])]);

        app(FeedProductImporter::class)->import($item);

        $product->refresh()->load('propertiesValues.property');
        $this->assertSame('Наше название', $product->name);
        $this->assertSame('Наше описание', $product->description['blocks'][0]['data']['text']);
        $this->assertSame(150000.0, $product->price);
        $this->assertStringContainsString('/feed/iron-steel/1001/', $product->image);
        $this->assertFalse($product->propertiesValues->contains('name', 'Наш бренд'));
        $this->assertFalse($product->propertiesValues->contains('name', 'Старая серия'));
        $this->assertTrue($product->propertiesValues->contains('name', 'ПроМеталл'));
        $this->assertTrue($product->propertiesValues->contains('name', 'Атмосфера'));
        $this->assertTrue($product->propertiesValues->contains('name', '12-20 м³'));
        $this->assertSame('success', $item->fresh()->status);
        Storage::assertMissing('public/products/old.jpg');
    }

    public function test_feed_discount_replaces_manual_discount_and_rollback_restores_it(): void
    {
        $source = app(IronSteelSetupService::class)->sync();
        $manualDiscount = Discount::query()->create([
            'name' => 'Ручная скидка',
            'type' => Discount::TYPE_RUB,
            'discount' => 5000,
            'is_active' => true,
        ]);
        $product = $this->product('Наше название', 140000);
        $product->update([
            'discount_id' => $manualDiscount->id,
            'image' => 'public/products/old.jpg',
        ]);
        Storage::put('public/products/old.jpg', $this->png());
        $link = FeedProductLink::query()->create([
            'feed_source_id' => $source->id,
            'offer_id' => '1001',
            'product_id' => $product->id,
            'decision' => FeedProductLink::DECISION_LINK,
        ]);
        $run = $this->applyRun($source->id);
        $payload = $this->offer('1001');
        $payload['old_price'] = 165000.0;
        $item = $run->items()->create([
            'feed_product_link_id' => $link->id,
            'product_id' => $product->id,
            'offer_id' => '1001',
            'action' => 'update',
            'status' => 'ready',
            'feed_payload' => $payload,
        ]);
        Http::fake([
            'https://images.test/*' => Http::response(
                $this->png(),
                200,
                ['Content-Type' => 'image/png']
            ),
        ]);

        app(FeedProductImporter::class)->import($item);

        $product->refresh()->unsetRelation('discount');
        $this->assertSame(165000.0, $product->price);
        $this->assertSame(150000.0, $product->getCurrentPrice());
        $this->assertNotSame($manualDiscount->id, $product->discount_id);
        $this->assertDatabaseHas('discounts', [
            'id' => $product->discount_id,
            'feed_source_id' => $source->id,
            'feed_offer_id' => '1001',
            'type' => Discount::TYPE_RUB,
            'discount' => 15000,
            'is_active' => true,
        ]);
        SeoTemplate::query()->create([
            'is_main' => true,
            'text_template' => 'Цена #PRICE#',
            'type_material' => SeoTemplate::MATERIAL_TYPE_PRODUCT,
            'type_template' => SeoTemplate::TYPE_TEMPLATE_TITLE,
        ]);
        $this->assertSame(
            'Цена 150000',
            app(SeoTemplateService::class)->getTemplateProduct($product)->getTitle()
        );
        $this->assertSame(
            $manualDiscount->id,
            $item->fresh()->before_snapshot['discount_id']
        );

        $rollbackItem = $this->rollbackItem($run, $item);
        app(FeedProductRollback::class)->rollback($rollbackItem);

        $product->refresh()->unsetRelation('discount');
        $this->assertSame(140000.0, $product->price);
        $this->assertSame($manualDiscount->id, $product->discount_id);
        $this->assertSame(135000.0, $product->getCurrentPrice());
        $this->assertDatabaseHas('discounts', [
            'feed_source_id' => $source->id,
            'feed_offer_id' => '1001',
            'is_active' => false,
        ]);
    }

    public function test_feed_discount_is_removed_and_can_be_restored_by_rollback(): void
    {
        $source = app(IronSteelSetupService::class)->sync();
        $product = $this->product('Наше название', 140000);
        $product->update(['image' => 'public/products/old.jpg']);
        Storage::put('public/products/old.jpg', $this->png());
        $link = FeedProductLink::query()->create([
            'feed_source_id' => $source->id,
            'offer_id' => '1001',
            'product_id' => $product->id,
            'decision' => FeedProductLink::DECISION_LINK,
        ]);
        Http::fake([
            'https://images.test/*' => Http::response(
                $this->png(),
                200,
                ['Content-Type' => 'image/png']
            ),
        ]);

        $discountedRun = $this->applyRun($source->id);
        $discountedPayload = $this->offer('1001');
        $discountedPayload['old_price'] = 165000.0;
        $discountedItem = $discountedRun->items()->create([
            'feed_product_link_id' => $link->id,
            'product_id' => $product->id,
            'offer_id' => '1001',
            'action' => 'update',
            'status' => 'ready',
            'feed_payload' => $discountedPayload,
        ]);
        app(FeedProductImporter::class)->import($discountedItem);
        $feedDiscountId = $product->fresh()->discount_id;

        $plainRun = $this->applyRun($source->id);
        $plainItem = $plainRun->items()->create([
            'feed_product_link_id' => $link->id,
            'product_id' => $product->id,
            'offer_id' => '1001',
            'action' => 'update',
            'status' => 'ready',
            'feed_payload' => $this->offer('1001'),
        ]);
        app(FeedProductImporter::class)->import($plainItem);

        $product->refresh()->unsetRelation('discount');
        $this->assertSame(150000.0, $product->price);
        $this->assertSame(150000.0, $product->getCurrentPrice());
        $this->assertNull($product->discount_id);
        $this->assertDatabaseHas('discounts', [
            'id' => $feedDiscountId,
            'is_active' => false,
        ]);

        $rollbackItem = $this->rollbackItem($plainRun, $plainItem);
        app(FeedProductRollback::class)->rollback($rollbackItem);

        $product->refresh()->unsetRelation('discount');
        $this->assertSame(165000.0, $product->price);
        $this->assertSame($feedDiscountId, $product->discount_id);
        $this->assertSame(150000.0, $product->getCurrentPrice());
        $this->assertDatabaseHas('discounts', [
            'id' => $feedDiscountId,
            'discount' => 15000,
            'is_active' => true,
        ]);
    }

    public function test_new_discounted_product_rollback_deactivates_managed_discount(): void
    {
        $source = app(IronSteelSetupService::class)->sync();
        $link = FeedProductLink::query()->create([
            'feed_source_id' => $source->id,
            'offer_id' => '1002',
            'decision' => FeedProductLink::DECISION_CREATE,
        ]);
        $run = $this->applyRun($source->id);
        $payload = $this->offer('1002');
        $payload['old_price'] = 55000.0;
        $item = $run->items()->create([
            'feed_product_link_id' => $link->id,
            'offer_id' => '1002',
            'action' => 'create',
            'status' => 'ready',
            'feed_payload' => $payload,
        ]);
        Http::fake([
            'https://images.test/*' => Http::response(
                $this->png(),
                200,
                ['Content-Type' => 'image/png']
            ),
        ]);

        app(FeedProductImporter::class)->import($item);

        $product = Product::query()->findOrFail($item->fresh()->product_id);
        $product->unsetRelation('discount');
        $discountId = $product->discount_id;
        $this->assertSame(55000.0, $product->price);
        $this->assertSame(48300.0, $product->getCurrentPrice());
        $this->assertNotNull($discountId);

        $rollbackItem = $this->rollbackItem($run, $item);
        app(FeedProductRollback::class)->rollback($rollbackItem);

        $product->refresh();
        $this->assertFalse($product->is_active);
        $this->assertNull($product->discount_id);
        $this->assertDatabaseHas('discounts', [
            'id' => $discountId,
            'is_active' => false,
        ]);
    }

    public function test_photo_failure_updates_data_but_keeps_existing_photos(): void
    {
        $source = app(IronSteelSetupService::class)->sync();
        $product = $this->product('Наше название', 140000);
        $product->update(['image' => 'public/products/old.jpg']);
        Storage::put('public/products/old.jpg', $this->png());
        $link = FeedProductLink::query()->create([
            'feed_source_id' => $source->id,
            'offer_id' => '1001',
            'product_id' => $product->id,
            'decision' => FeedProductLink::DECISION_LINK,
        ]);
        $run = $this->applyRun($source->id);
        $item = $run->items()->create([
            'feed_product_link_id' => $link->id,
            'product_id' => $product->id,
            'offer_id' => '1001',
            'action' => 'update',
            'status' => 'ready',
            'feed_payload' => $this->offer('1001'),
        ]);
        Http::fake(['https://images.test/*' => Http::response('', 500)]);

        app(FeedProductImporter::class)->import($item);

        $this->assertSame(150000.0, $product->fresh()->price);
        $this->assertSame('public/products/old.jpg', $product->fresh()->image);
        $this->assertSame('warning', $item->fresh()->status);
        Storage::assertExists('public/products/old.jpg');
    }

    public function test_image_download_rejects_hosts_outside_the_allowlist(): void
    {
        Http::fake();

        $this->expectException(\App\Services\Feed\FeedImageException::class);
        $this->expectExceptionMessage('Некорректная ссылка');

        app(\App\Services\Feed\FeedImageManager::class)->prepareAll(
            ['https://127.0.0.1/private.png'],
            1,
            '1001'
        );
    }

    public function test_new_product_uses_cladding_override_and_can_be_rolled_back(): void
    {
        $source = app(IronSteelSetupService::class)->sync();
        $link = FeedProductLink::query()->create([
            'feed_source_id' => $source->id,
            'offer_id' => '1002',
            'decision' => FeedProductLink::DECISION_CREATE,
        ]);
        $run = $this->applyRun($source->id);
        $item = $run->items()->create([
            'feed_product_link_id' => $link->id,
            'offer_id' => '1002',
            'action' => 'create',
            'status' => 'ready',
            'feed_payload' => $this->offer('1002'),
        ]);
        Http::fake(['https://images.test/*' => Http::response($this->png(), 200, ['Content-Type' => 'image/png'])]);

        app(FeedProductImporter::class)->import($item);

        $product = Product::query()->findOrFail($item->fresh()->product_id);
        $product->load('propertiesValues.property');
        $this->assertSame('Облицовка камнем', $product->category->name);
        $this->assertTrue($product->is_active);
        $this->assertSame(48300.0, $product->price);
        $this->assertTrue($product->propertiesValues->contains('name', '785×660×560 мм'));
        $this->assertTrue($product->propertiesValues->contains('name', 'Камень'));
        $this->assertSame(
            'Описание облицовки',
            $product->description['blocks'][0]['data']['text']
        );
        $this->assertSame('Упаковка', $product->description['blocks'][1]['data']['text']);
        $this->assertSame(
            'Размер в упаковке (ВхДхШ) - 950×735×770 мм',
            $product->description['blocks'][2]['data']['text']
        );

        $rollbackRun = FeedImportRun::query()->create([
            'feed_source_id' => $source->id,
            'parent_run_id' => $run->id,
            'kind' => FeedImportRun::KIND_ROLLBACK,
            'status' => FeedImportRun::STATUS_RUNNING,
        ]);
        $rollbackItem = $rollbackRun->items()->create([
            'feed_product_link_id' => $link->id,
            'product_id' => $product->id,
            'offer_id' => '1002',
            'action' => 'rollback',
            'status' => 'ready',
            'before_snapshot' => $item->fresh()->before_snapshot,
        ]);

        app(FeedProductRollback::class)->rollback($rollbackItem);

        $this->assertFalse($product->fresh()->is_active);
        $link->refresh();
        $this->assertSame('rolled_back', $link->last_status);
        $this->assertNull($link->product_id);
        $this->assertSame(FeedProductLink::DECISION_CREATE, $link->decision);

        $reapplyPayload = $this->offer('1002');
        unset($reapplyPayload['params']['Размер товара']);
        $reapplyRun = $this->applyRun($source->id);
        $reapplyItem = $reapplyRun->items()->create([
            'feed_product_link_id' => $link->id,
            'offer_id' => '1002',
            'action' => 'create',
            'status' => 'ready',
            'feed_payload' => $reapplyPayload,
        ]);

        app(FeedProductImporter::class)->import($reapplyItem);

        $this->assertSame($product->id, $reapplyItem->fresh()->product_id);
        $this->assertTrue($product->fresh()->is_active);
        $this->assertFalse(
            $product->fresh()->propertiesValues()->where('name', '785×660×560 мм')->exists()
        );
        $this->assertSame(1, Product::query()->where('name', $product->name)->count());
    }

    public function test_status_endpoint_reports_rollback_progress(): void
    {
        $source = app(IronSteelSetupService::class)->sync();
        $run = FeedImportRun::query()->create([
            'feed_source_id' => $source->id,
            'kind' => FeedImportRun::KIND_ROLLBACK,
            'status' => FeedImportRun::STATUS_RUNNING,
            'started_at' => now(),
        ]);
        foreach (['success', 'running', 'ready'] as $index => $status) {
            $run->items()->create([
                'offer_id' => (string) (2000 + $index),
                'action' => 'rollback',
                'status' => $status,
            ]);
        }

        $response = $this
            ->withoutMiddleware([Authenticate::class, RoleMiddleware::class])
            ->getJson(route('backend.feed-import.status', $run));

        $response->assertOk()->assertJson([
            'id' => $run->id,
            'kind' => FeedImportRun::KIND_ROLLBACK,
            'kind_label' => 'Откат',
            'status' => FeedImportRun::STATUS_RUNNING,
            'status_label' => 'Выполняется',
            'progress' => 33,
            'processed' => 1,
            'total' => 3,
            'finished' => false,
        ]);
    }

    public function test_active_operation_progress_is_visible_on_history_tab(): void
    {
        $source = app(IronSteelSetupService::class)->sync();
        $run = FeedImportRun::query()->create([
            'feed_source_id' => $source->id,
            'kind' => FeedImportRun::KIND_ROLLBACK,
            'status' => FeedImportRun::STATUS_RUNNING,
            'started_at' => now(),
        ]);

        $response = $this
            ->withoutMiddleware([Authenticate::class, RoleMiddleware::class])
            ->get(route('backend.feed-import.index', ['tab' => 'history']));

        $response->assertOk()
            ->assertSee('feed-run-progress', false)
            ->assertSee('data-operation-label>Откат</span>', false)
            ->assertSee("#{$run->id}:", false)
            ->assertSee('Выполняется');
    }

    public function test_opening_import_page_does_not_reconfigure_source(): void
    {
        $source = app(IronSteelSetupService::class)->sync();
        $configuredAt = $source->updated_at;
        $this->travel(5)->minutes();

        $response = $this
            ->withoutMiddleware([Authenticate::class, RoleMiddleware::class])
            ->get(route('backend.feed-import.index'));

        $response->assertOk();
        $this->assertTrue($source->fresh()->updated_at->equalTo($configuredAt));
    }

    public function test_import_screen_shows_when_catalog_was_really_updated(): void
    {
        $source = app(IronSteelSetupService::class)->sync();
        FeedImportRun::query()->create([
            'feed_source_id' => $source->id,
            'kind' => FeedImportRun::KIND_APPLY,
            'status' => FeedImportRun::STATUS_COMPLETED,
            'started_at' => '2026-08-06 14:28:11',
            'finished_at' => '2026-08-06 14:29:37',
        ]);

        $response = $this
            ->withoutMiddleware([Authenticate::class, RoleMiddleware::class])
            ->get(route('backend.feed-import.index'));

        $response->assertOk()
            ->assertSee('Товары сайта обновлены:')
            ->assertSee('06.08.2026 14:29');
    }

    public function test_completed_import_hides_the_applied_preview_table(): void
    {
        $source = app(IronSteelSetupService::class)->sync();
        $product = $this->product('Наше название', 140000);
        FeedProductLink::query()->create([
            'feed_source_id' => $source->id,
            'offer_id' => '1001',
            'product_id' => $product->id,
            'decision' => FeedProductLink::DECISION_LINK,
        ]);
        $preview = app(FeedPreviewService::class)->create($source, null);
        $import = FeedImportRun::query()->create([
            'feed_source_id' => $source->id,
            'parent_run_id' => $preview->id,
            'kind' => FeedImportRun::KIND_APPLY,
            'status' => FeedImportRun::STATUS_COMPLETED,
            'summary' => [
                'total' => 3,
                'success' => 2,
                'skipped' => 1,
                'error' => 0,
            ],
            'started_at' => now()->subMinute(),
            'finished_at' => now(),
        ]);

        $response = $this
            ->withoutMiddleware([Authenticate::class, RoleMiddleware::class])
            ->get(route('backend.feed-import.index'));

        $response->assertOk()
            ->assertSee("Импорт #{$import->id}")
            ->assertSee('успешно завершён')
            ->assertSee('Обработано: 2')
            ->assertSee('Открыть историю')
            ->assertDontSee('Характеристики после импорта')
            ->assertDontSee('offer id 1001');
    }

    public function test_csv_report_escapes_spreadsheet_formulas(): void
    {
        $source = app(IronSteelSetupService::class)->sync();
        $run = FeedImportRun::query()->create([
            'feed_source_id' => $source->id,
            'kind' => FeedImportRun::KIND_PREVIEW,
            'status' => FeedImportRun::STATUS_READY,
        ]);
        $run->items()->create([
            'offer_id' => '1001',
            'action' => 'pending',
            'status' => 'pending',
            'feed_payload' => ['name' => '=HYPERLINK("https://example.test")'],
            'error' => '+SUM(1,1)',
        ]);

        $response = $this
            ->withoutMiddleware([Authenticate::class, RoleMiddleware::class])
            ->get(route('backend.feed-import.report', $run));

        $response->assertOk();
        $rows = array_map('str_getcsv', preg_split('/\r?\n/', trim($response->streamedContent())));
        $this->assertSame("'=HYPERLINK(\"https://example.test\")", $rows[1][4]);
        $this->assertSame("'+SUM(1,1)", $rows[1][5]);
    }

    private function product(string $name, float $price): Product
    {
        return Product::query()->create([
            'name' => $name,
            'category_id' => Category::query()->where('name', 'Печи для бани и сауны')->value('id'),
            'price' => $price,
            'image' => '',
            'description' => ['blocks' => []],
            'preview_text' => null,
            'sort' => 1,
            'is_active' => true,
            'is_popular' => false,
        ]);
    }

    private function applyRun(int $sourceId): FeedImportRun
    {
        return FeedImportRun::query()->create([
            'feed_source_id' => $sourceId,
            'kind' => FeedImportRun::KIND_APPLY,
            'status' => FeedImportRun::STATUS_RUNNING,
            'started_at' => now(),
        ]);
    }

    private function rollbackItem(
        FeedImportRun $applyRun,
        \App\Models\Feed\FeedImportItem $sourceItem
    ): \App\Models\Feed\FeedImportItem {
        $rollbackRun = FeedImportRun::query()->create([
            'feed_source_id' => $applyRun->feed_source_id,
            'parent_run_id' => $applyRun->id,
            'kind' => FeedImportRun::KIND_ROLLBACK,
            'status' => FeedImportRun::STATUS_RUNNING,
        ]);

        return $rollbackRun->items()->create([
            'feed_product_link_id' => $sourceItem->feed_product_link_id,
            'product_id' => $sourceItem->product_id,
            'offer_id' => $sourceItem->offer_id,
            'action' => 'rollback',
            'status' => 'ready',
            'before_snapshot' => $sourceItem->fresh()->before_snapshot,
        ]);
    }

    private function offer(string $offerId): array
    {
        $parsed = app(\App\Services\Feed\IronSteelFeedParser::class)->parse($this->feedXml);

        return $parsed['offers'][$offerId];
    }

    private function png(): string
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');
    }
}

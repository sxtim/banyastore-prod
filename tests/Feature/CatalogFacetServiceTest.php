<?php

namespace Tests\Feature;

use App\Services\CatalogFacetService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CatalogFacetServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        DB::purge('sqlite');

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->boolean('is_active')->default(true);
            $table->integer('sort')->default(0);
            $table->unsignedInteger('price')->default(0);
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
        });

        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->softDeletes();
        });

        Schema::create('property_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->string('name');
            $table->softDeletes();
        });

        Schema::create('products_property_values', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('property_value_id');
        });

        DB::table('properties')->insert([
            ['id' => 10, 'name' => 'Цвет'],
            ['id' => 11, 'name' => 'Размер'],
        ]);
        DB::table('categories')->insert([
            'id' => 3,
            'slug' => 'facet-category',
        ]);
        DB::table('property_values')->insert([
            ['id' => 101, 'property_id' => 10, 'name' => 'Красный'],
            ['id' => 102, 'property_id' => 10, 'name' => 'Синий'],
            ['id' => 201, 'property_id' => 11, 'name' => 'Маленький'],
            ['id' => 202, 'property_id' => 11, 'name' => 'Большой'],
        ]);
        DB::table('products')->insert([
            ['id' => 1, 'category_id' => 3, 'is_active' => true, 'sort' => 1, 'price' => 50000],
            ['id' => 2, 'category_id' => 3, 'is_active' => true, 'sort' => 2, 'price' => 100000],
            ['id' => 3, 'category_id' => 3, 'is_active' => true, 'sort' => 3, 'price' => 150000],
            ['id' => 4, 'category_id' => 4, 'is_active' => true, 'sort' => 4, 'price' => 200000],
            ['id' => 5, 'category_id' => 3, 'is_active' => false, 'sort' => 5, 'price' => 250000],
            ['id' => 6, 'category_id' => 3, 'is_active' => true, 'sort' => 6, 'price' => 300000],
        ]);
        DB::table('products_property_values')->insert([
            ['product_id' => 1, 'property_value_id' => 101],
            ['product_id' => 1, 'property_value_id' => 201],
            ['product_id' => 2, 'property_value_id' => 102],
            ['product_id' => 2, 'property_value_id' => 201],
            ['product_id' => 3, 'property_value_id' => 101],
            ['product_id' => 3, 'property_value_id' => 202],
            ['product_id' => 4, 'property_value_id' => 102],
            ['product_id' => 4, 'property_value_id' => 201],
            ['product_id' => 5, 'property_value_id' => 101],
            ['product_id' => 5, 'property_value_id' => 201],
        ]);
    }

    public function test_it_counts_or_within_a_property_and_and_between_properties(): void
    {
        $data = (new CatalogFacetService())->getData(3, [
            10 => [101, 102],
            11 => [201],
        ]);

        $this->assertSame(2, $data['result_count']);
    }

    public function test_it_builds_self_excluding_facets(): void
    {
        $data = (new CatalogFacetService())->getData(3, [
            10 => [101],
            11 => [201],
        ]);

        $this->assertSame(1, $data['result_count']);
        $this->assertEquals([101 => 1, 102 => 1], $data['facets'][10]);
        $this->assertSame([201 => 1, 202 => 1], $data['facets'][11]);
    }

    public function test_it_counts_only_active_products_from_the_requested_category(): void
    {
        $data = (new CatalogFacetService())->getData(3);

        $this->assertSame(4, $data['result_count']);
    }

    public function test_facets_endpoint_returns_validated_json_and_noindex_header(): void
    {
        $response = $this->getJson('/ajax/category/facet-category/facets?'.http_build_query([
            'properties' => [10 => [101]],
        ]));

        $response
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertJsonPath('result_count', 2)
            ->assertJsonPath('selected.10.0', 101);
    }

    public function test_it_applies_price_to_results_and_property_facets(): void
    {
        $data = (new CatalogFacetService())->getData(3, [], [
            'min' => 75000,
            'max' => 175000,
        ]);

        $this->assertSame(2, $data['result_count']);
        $this->assertEquals([101 => 1, 102 => 1], $data['facets'][10]);
        $this->assertSame(['min' => 50000, 'max' => 300000], $data['price_bounds']);
    }

    public function test_facets_endpoint_returns_selected_price_and_bounds(): void
    {
        $response = $this->getJson('/ajax/category/facet-category/facets?'.http_build_query([
            'price_min' => 75000,
            'price_max' => 175000,
        ]));

        $response
            ->assertOk()
            ->assertJsonPath('result_count', 2)
            ->assertJsonPath('selected_price.min', 75000)
            ->assertJsonPath('selected_price.max', 175000)
            ->assertJsonPath('price_bounds.min', 50000)
            ->assertJsonPath('price_bounds.max', 300000);
    }

    public function test_facets_endpoint_rejects_more_than_the_supported_property_groups(): void
    {
        $properties = [];

        for ($propertyId = 1; $propertyId <= 31; $propertyId++) {
            $properties[$propertyId] = [1];
        }

        $this->getJson('/ajax/category/facet-category/facets?'.http_build_query([
            'properties' => $properties,
        ]))->assertStatus(400);
    }
}

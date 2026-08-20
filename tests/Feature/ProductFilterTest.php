<?php

namespace Tests\Feature;

use App\Http\Filters\ProductFilter;
use App\Models\Shop\Product;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class ProductFilterTest extends TestCase
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
            $table->unsignedInteger('price')->default(0);
        });

        Schema::create('property_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->softDeletes();
        });

        Schema::create('products_property_values', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('property_value_id');
        });
    }

    public function test_it_uses_or_within_a_property_and_and_between_properties(): void
    {
        DB::table('products')->insert([
            ['id' => 1, 'category_id' => 3, 'is_active' => true],
            ['id' => 2, 'category_id' => 3, 'is_active' => true],
            ['id' => 3, 'category_id' => 3, 'is_active' => true],
            ['id' => 4, 'category_id' => 3, 'is_active' => true],
        ]);

        DB::table('property_values')->insert([
            ['id' => 101, 'property_id' => 10],
            ['id' => 102, 'property_id' => 10],
            ['id' => 103, 'property_id' => 10],
            ['id' => 201, 'property_id' => 11],
            ['id' => 202, 'property_id' => 11],
        ]);

        DB::table('products_property_values')->insert([
            ['product_id' => 1, 'property_value_id' => 101],
            ['product_id' => 1, 'property_value_id' => 201],
            ['product_id' => 2, 'property_value_id' => 102],
            ['product_id' => 2, 'property_value_id' => 201],
            ['product_id' => 3, 'property_value_id' => 103],
            ['product_id' => 3, 'property_value_id' => 202],
            ['product_id' => 4, 'property_value_id' => 101],
        ]);

        $productIds = $this->filteredProductIds([
            10 => [101, 102],
            11 => [201],
        ]);

        $this->assertSame([1, 2], $productIds);
    }

    public function test_it_requires_values_to_belong_to_the_requested_property(): void
    {
        DB::table('products')->insert([
            'id' => 1,
            'category_id' => 3,
            'is_active' => true,
        ]);
        DB::table('property_values')->insert([
            'id' => 201,
            'property_id' => 11,
        ]);
        DB::table('products_property_values')->insert([
            'product_id' => 1,
            'property_value_id' => 201,
        ]);

        $this->assertSame([], $this->filteredProductIds([10 => [201]]));
    }

    public function test_it_ignores_soft_deleted_property_values(): void
    {
        DB::table('products')->insert([
            'id' => 1,
            'category_id' => 3,
            'is_active' => true,
        ]);
        DB::table('property_values')->insert([
            'id' => 101,
            'property_id' => 10,
            'deleted_at' => now(),
        ]);
        DB::table('products_property_values')->insert([
            'product_id' => 1,
            'property_value_id' => 101,
        ]);

        $this->assertSame([], $this->filteredProductIds([10 => [101]]));
    }

    public function test_it_accepts_current_catalog_filter_dimensions(): void
    {
        $properties = [];

        for ($propertyId = 1; $propertyId <= 23; $propertyId++) {
            $properties[$propertyId] = range(1, 34);
        }

        $filter = $this->makeFilter($properties);

        $this->assertSame($properties, $filter->filters()['properties']);
    }

    public function test_it_rejects_too_many_property_groups(): void
    {
        $properties = [];

        for ($propertyId = 1; $propertyId <= 31; $propertyId++) {
            $properties[$propertyId] = [1];
        }

        $this->expectInvalidFilter($properties);
    }

    public function test_it_rejects_too_many_values_in_one_group(): void
    {
        $this->expectInvalidFilter([1 => range(1, 41)]);
    }

    public function test_it_builds_one_grouped_subquery_instead_of_exists_per_group(): void
    {
        $query = Product::query()->filter($this->makeFilter([
            10 => [101, 102],
            11 => [201],
        ]));

        $sql = strtolower($query->toSql());

        $this->assertStringContainsString('count(distinct property_values.property_id)', $sql);
        $this->assertStringNotContainsString('exists (', $sql);
    }

    public function test_it_filters_products_by_price_range(): void
    {
        DB::table('products')->insert([
            ['id' => 1, 'category_id' => 3, 'is_active' => true, 'price' => 50000],
            ['id' => 2, 'category_id' => 3, 'is_active' => true, 'price' => 100000],
            ['id' => 3, 'category_id' => 3, 'is_active' => true, 'price' => 150000],
        ]);

        $filter = new ProductFilter(Request::create('/', 'GET', [
            'price_min' => 75000,
            'price_max' => 125000,
        ]));

        $this->assertSame(
            [2],
            Product::query()->filter($filter)->orderBy('id')->pluck('id')->all()
        );
    }

    public function test_it_rejects_an_inverted_price_range(): void
    {
        $filter = new ProductFilter(Request::create('/', 'GET', [
            'price_min' => 150000,
            'price_max' => 100000,
        ]));

        try {
            $filter->filters();
            $this->fail('Invalid price filter was accepted.');
        } catch (HttpException $exception) {
            $this->assertSame(400, $exception->getStatusCode());
        }
    }

    private function filteredProductIds(array $properties): array
    {
        return Product::query()
            ->filter($this->makeFilter($properties))
            ->orderBy('id')
            ->pluck('id')
            ->all();
    }

    private function makeFilter(array $properties): ProductFilter
    {
        return new ProductFilter(Request::create('/', 'GET', [
            'properties' => $properties,
        ]));
    }

    private function expectInvalidFilter(array $properties): void
    {
        try {
            $this->makeFilter($properties)->filters();
            $this->fail('Invalid filter was accepted.');
        } catch (HttpException $exception) {
            $this->assertSame(400, $exception->getStatusCode());
        }
    }
}

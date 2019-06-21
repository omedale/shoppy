<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Product;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductAttribute;
use App\Models\Customer;

class ProductTest extends TestCase
{
    public function test_product_are_listed_correctly_with_default_params()
    {

        $customer = factory(Customer::class)->create();
        $token = $customer->generateToken('omedale');
        $headers = ['Authorization' => "Bearer $token"];

        $response = $this->json('GET', '/api/products', [], $headers);
        $rows = $response->baseResponse->getData()->rows;
        $response->assertStatus(200)
            ->assertJsonStructure([
                'rows',
                'count',
            ])
            ->assertJson([
                'count' => 101,
                'rows' => [
                    ['product_id' => 1, 'name' => "Arc d'Triomphe"]
                ]
            ]);
        $this->assertEquals(20, count($rows));
    }

    public function test_product_are_listed_correctly_with_limit_equal_one()
    {

        $customer = factory(Customer::class)->create();
        $token = $customer->generateToken('omedale');
        $headers = ['Authorization' => "Bearer $token"];

        $response = $this->json('GET', '/api/products', ['limit' => 1], $headers);
        $rows = $response->baseResponse->getData()->rows;
        $response->assertStatus(200)
            ->assertJsonStructure([
                'rows',
                'count',
            ])
            ->assertJson([
                'count' => 101,
                'rows' => [
                    ['product_id' => 1, 'name' => "Arc d'Triomphe"]
                ]
            ]);
        $this->assertEquals(1, count($rows));
    }

    public function test_search_product_are_not_listed_with_no_search_query()
    {
        $customer = factory(Customer::class)->create();
        $token = $customer->generateToken('omedale');
        $headers = ['Authorization' => "Bearer $token"];

        $response = $this->json('GET', '/api/products/search', [], $headers)
            ->assertJson([
            'error' => [
                'status' => 500,
                'code' => "USR_02",
                'message' => "The field(s) are/is required.",
                'field_errors' => [
                    "q" => ["Search query is required"]
                ]
            ]
        ]);
    }

    public function test_search_product_are_listed_correctly_with_search_query()
    {

        $customer = factory(Customer::class)->create();
        $token = $customer->generateToken('omedale');
        $headers = ['Authorization' => "Bearer $token"];

        $response = $this->json('GET', '/api/products/search', ['q' => 'shirt', 'limit' => 5], $headers);
        $rows = $response->baseResponse->getData()->rows;
        $response->assertStatus(200)
            ->assertJsonStructure([
                'rows',
                'count',
            ])
            ->assertJson([
                'count' => 80,
            ]);
        $this->assertEquals(5, count($rows));
    }
}

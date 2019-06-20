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
}

<?php

namespace Tests\Feature;
use App\Models\Customer;

use App\Models\ShoppingCart;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Helpers\CommonHelper;

class ShoppingCartTest extends TestCase
{

    public function test_add_to_cart_with_valid_header_and_no_fields()
    {
        $this->json('POST', 'api/shoppingcart/add', [], $this->getHeader())
            ->assertStatus(500)
            ->assertJson([
                'error' => [
                    'status' => 500,
                    'code' => "USR_02",
                    'message' => "The field(s) are/is required.",
                    'field_errors' => [
                        "cart_id" => ["Cart ID is required"],
                        "product_id" => ["Product ID is required"],
                        "attributes" => ["Attributes is required"]
                    ]
                ]
            ]);
    }

    public function test_add_to_cart_with_valid_header_and_fields()
    {
        $cart_id = CommonHelper::generateUniqueId();
        $this->json('POST', 'api/shoppingcart/add',
            [
                "attributes" => "L, White",
                "product_id" => 2,
                "cart_id" => $cart_id
            ],
            $this->getHeader())
            ->assertStatus(200)
            ->assertJson([
                [
                    'attributes' => "L, White",
                    'product_id' => 2,
                    'quantity' => 1,
                ]
            ]);
    }

    public function test_update_cart_with_valid_header_and_fields()
    {
        $shopping_cart = factory(ShoppingCart::class)->create();
        $this->json('PUT', 'api/shoppingcart/update/'.$shopping_cart->item_id,
            [
                'quantity' => 5,
            ],
            $this->getHeader())
            ->assertStatus(200)
            ->assertJson([
                [
                    'attributes' => $shopping_cart->attributes,
                    'product_id' => $shopping_cart->product_id,
                    'quantity' => 5,
                ]
            ]);
    }

    public function test_cart_items_are_listed_correctly_with_cart_id()
    {
        $shopping_cart = factory(ShoppingCart::class)->create();
        $response = $this->json('GET',
                            '/api/shoppingcart/'.$shopping_cart->cart_id,
                            ['limit' => 1],
                            $this->getHeader());
        $cart_items = $response->baseResponse->getData();
        $response->assertStatus(200);
        $this->assertEquals(1, count($cart_items));
        $this->clear_shopping_cart_table();
    }

    private function getHeader() {
        $customer = factory(Customer::class)->create();
        $token = $customer->generateToken('omedale');
        return ['Authorization' => "Bearer $token"];
    }

    private function clear_shopping_cart_table() {
        ShoppingCart::truncate();
    }
}

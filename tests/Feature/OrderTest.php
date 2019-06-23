<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Customer;
use App\Models\Shipping;
use App\Models\ShoppingCart;
use App\Models\Tax;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    public function test_create_order_valid_header_and_no_fields()
    {
        $this->json('POST', 'api/orders', [], $this->getHeader())
            ->assertStatus(500)
            ->assertJson([
                'error' => [
                    'status' => 500,
                    'code' => "USR_02",
                    'message' => "The field(s) are/is required.",
                    'field_errors' => [
                        "cart_id" => ["Cart ID is required"],
                        "shipping_id" => ["Shipping ID is required"],
                        "tax_id" => ["Tax ID is required"]
                    ]
                ]
            ]);
    }

    public function test_update_cart_with_valid_header_and_fields()
    {
        $shopping_cart = factory(ShoppingCart::class)->create();
        $shipping = Shipping::first();
        $tax = Tax::first();
        $response = $this->json('POST', 'api/orders',
            [
                'cart_id' => $shopping_cart->cart_id,
                'shipping_id' => $shipping->shipping_id,
                'tax_id' => $tax->tax_id,
            ],
            $this->getHeader());
        $response->assertStatus(200)
            ->assertJsonStructure([
                'orderId'
            ])
            ->assertJson([
                'orderId' => 1,
            ]);
        $this->clear_order_tables();
    }

    private function getHeader() {
        $customer = factory(Customer::class)->create();
        $token = $customer->generateToken('omedale');
        return ['Authorization' => "Bearer $token"];
    }

    private function clear_order_tables() {
        Order::truncate();
        OrderDetail::truncate();
    }
}

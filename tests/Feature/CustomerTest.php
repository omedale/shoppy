<?php

namespace Tests\Feature;

use App\Models\Customer;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTest extends TestCase
{
    public $auth_payload = ['name' => 'Oluwafemi', 'email' => 'omedal@customer.com', 'password' => 'test'];

    public function test_email_name_and_password_required_to_register()
    {
        $this->json('POST', 'api/customer')
            ->assertStatus(500)
            ->assertJson([
                'error' => [
                    'status' => 500,
                    'code' => "USR_02",
                    'message' => "The field(s) are/is required.",
                    'field_errors' => [
                        "name" => ["Name is required"],
                        "email" => ["Email is required"],
                        "password" => ["Password is required"]
                    ]
                ]
            ]);
    }

    public function test_successfull_registeration()
    {
        $this->json('POST', 'api/customer', $this->auth_payload)
            ->assertStatus(201)
            ->assertJsonStructure([
                'customer' => [
                    'schema' => [
                        'name',
                        'email',
                    ],
                ],
                'accessToken',
                'expires_in',
            ])
            ->assertJson([
                'customer' => [
                    'schema' => [
                        'name' => 'Oluwafemi',
                        'email' => 'omedal@customer.com',
                    ],
                ],
                'expires_in' => '24h',
            ]);
    }


    public function test_email_and_password_required_to_login()
    {
        $this->json('POST', 'api/customer/login')
            ->assertStatus(500)
            ->assertJson([
                'error' => [
                    'status' => 500,
                    'code' => "USR_02",
                    'message' => "The field(s) are/is required.",
                    'field_errors' => [
                        "email" => ["Email is required"],
                        "password" => ["Password is required"]
                    ]
                ]
            ]);
    }


    public function test_successfull_login() {
        $this->json('POST', 'api/customer/login', $this->auth_payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                'customer' => [
                    'schema' => [
                        'name',
                        'email',
                    ],
                ],
                'accessToken',
                'expires_in',
            ])
            ->assertJson([
                'customer' => [
                    'schema' => [
                        'name' => 'Oluwafemi',
                        'email' => 'omedal@customer.com',
                    ],
                ],
                'expires_in' => '24h',
            ]);
            $this->clear_customer_table();
    }

    public function test_unsuccessfull_customer_address_update() {
        $customer = factory(Customer::class)->create();
        $token = $customer->generateToken('omedale');
        $headers = ['API-KEY' => "Bearer $token"];
        $payload = [
            'address_2' => 'Ipsum',
        ];
        $response = $this->json('PUT', 'api/customers/address', $payload, $headers);
        $response->assertStatus(500)
        ->assertJson([
            'error' => [
                'status' => 500,
                'code' => "USR_02",
                'message' => "The field(s) are/is required.",
                'field_errors' => [
                    "address_1" => ["Address 1 is required"],
                    "city" => ["City is required"],
                    "region" => ["Region is required"],
                    "postal_code" => ["Postal code is required"],
                    "country" => ["Country is required"],
                    "shipping_region_id" => ["Shipping region id is required"]
                ]
            ]
        ]);
    }

    public function test_successfull_customer_address_update() {
        $customer = factory(Customer::class)->create();
        $token = $customer->generateToken('omedale');
        $headers = ['API-KEY' => "Bearer $token"];
        $payload = [
            'address_1' => 'Lorem',
            'address_2' => 'Ipsum',
            'city' => 'Lorem',
            'postal_code' => 'Ipsum',
            'shipping_region_id' => 2,
            'country' => 'Nigeria',
            'region' => 'Other'
        ];
        $response = $this->json('PUT', 'api/customers/address', $payload, $headers);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'address_1',
                'shipping_region_id',
            ])
            ->assertJson([
                'address_1' => 'Lorem',
                'shipping_region_id' => 2,
            ]);
    }

    private function clear_customer_table() {
        Customer::truncate();
    }
}

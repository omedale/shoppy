<?php

namespace Tests\Feature;

use App\Models\Customer;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Helpers\ErrorHelper;

class CustomerTest extends TestCase
{
    public $auth_payload = ['name' => 'Oluwafemi', 'email' => 'omedal@customer.com', 'password' => 'test'];
    /**
     * Test invalid registration
     * When name, email and password are required
     *
     * @return void
     */
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

    /**
     * Test successfull registration
     *
     * @return void
     */
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

    /**
     * Test invalid login
     * When email and password are required
     *
     * @return void
     */
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

    /**
     * Test successfull login
     *
     * @return void
     */

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
        $this->clear_db();
    }

    private function clear_db() {
        Customer::truncate();
    }
}

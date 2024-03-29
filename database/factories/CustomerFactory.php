<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Customer;
use Illuminate\Support\Str;
use Faker\Generator as Faker;


$factory->define(Customer::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt('omedale'),
    ];
});

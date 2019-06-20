<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Product;
use Illuminate\Support\Str;
use Faker\Generator as Faker;


$factory->define(Product::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->sentence($nbWords = 6, $variableNbWords = true),
        'price' => $faker->randomNumber(2),
        'discounted_price' =>  $faker->randomNumber(2),
        'display' => $faker->randomNumber(2)
    ];
});

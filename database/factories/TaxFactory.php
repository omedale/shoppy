<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Tax;
use Illuminate\Support\Str;
use Faker\Generator as Faker;


$factory->define(Tax::class, function (Faker $faker) {
    return [
        'tax_type' => $faker->name,
        'tax_percentage' => 0.00,
    ];
});

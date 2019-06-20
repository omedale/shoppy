<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Attribute;
use Illuminate\Support\Str;
use Faker\Generator as Faker;


$factory->define(Attribute::class, function (Faker $faker) {
    return [
        'name' => $faker->name
    ];
});

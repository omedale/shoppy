<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\AttributeValue;
use App\Models\Attribute;
use Illuminate\Support\Str;
use Faker\Generator as Faker;


$factory->define(AttributeValue::class, function (Faker $faker) {
    return [
        'value' => $faker->name,
        'attribute_id' => Attribute::all()->random()->attribute_id
    ];
});

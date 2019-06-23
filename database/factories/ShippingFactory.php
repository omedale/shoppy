<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Shipping;
use App\Models\ShippingRegion;
use Illuminate\Support\Str;
use Faker\Generator as Faker;


$factory->define(Shipping::class, function (Faker $faker) {
    return [
        'shipping_type' => $faker->name,
        'shipping_cost' => $faker->randomNumber(2),
        'shipping_region_id' => ShippingRegion::first()->shipping_region_id
    ];
});

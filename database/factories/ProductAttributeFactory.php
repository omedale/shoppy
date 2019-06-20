<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Product;
use App\Models\AttributeValue;
use App\Models\ProductAttribute;
use Illuminate\Support\Str;
use Faker\Generator as Faker;


$factory->define(ProductAttribute::class, function (Faker $faker) {
    return [
        'attribute_value_id' => AttributeValue::all()->random()->attribute_value_id,
        'product_id' => Product::all()->random()->product_id
    ];
});

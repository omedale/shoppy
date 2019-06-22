<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Product;
use App\Models\ShoppingCart;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use App\Helpers\CommonHelper;


$factory->define(ShoppingCart::class, function (Faker $faker) {
    return [
        'cart_id' => CommonHelper::generateUniqueId(),
        'product_id' => Product::first()->product_id,
        'attributes' => "XL, White",
        'quantity' => 1,
        'added_on' => true,
        'added_on' => CommonHelper::getCurrentDateTime()
    ];
});

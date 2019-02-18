<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Person::class, function (Faker $faker) {
    return [
        'email' => $faker->unique()->email,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
    ];
});

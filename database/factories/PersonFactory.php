<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Person::class, function (Faker $faker) {
    return [
        'first_name' =>  $faker->firstName,
        'last_name' => $faker->lastName,
        'user_id'=>1
    ];
});

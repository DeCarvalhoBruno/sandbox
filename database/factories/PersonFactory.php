<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Person::class, function (Faker $faker) {
    return [
        'person_first_name' =>  $faker->firstName,
        'person_last_name' => $faker->lastName,
        'user_id'=>1
    ];
});

<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Group::class, function (Faker $faker) {
    return [
        'group_name' =>  $faker->word,
        'group_mask'=>0x00001
    ];
});

<?php

$factory->define(\RafflesArgentina\ResourceController\Models\User::class, function (\Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt($faker->password),
    ];
});

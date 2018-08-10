<?php

$factory->define(
    \RafflesArgentina\ResourceController\Models\User::class, function (\Faker\Generator $faker) {
        return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt($faker->password),
        ];
    }
);

$factory->define(
    \RafflesArgentina\ResourceController\Models\Related::class, function (\Faker\Generator $faker) {
        return [
        'a' => str_random(),
        'b' => str_random(),
        'c' => str_random(),
        ];
    }
);

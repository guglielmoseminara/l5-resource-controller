<?php

use Illuminate\Support\Str;

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
        'a' => Str::random(),
        'b' => Str::random(),
        'c' => Str::random(),
        ];
    }
);

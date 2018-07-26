<?php

use Faker\Generator as Faker;

$factory->define(App\Document::class, function (Faker $faker) {
    $shouldBeModified = random_int(0,1);
    $shouldBeExported = random_int(0,1);

    $created_at = $faker->dateTime;
    $modified_at = $faker->dateTimeBetween($created_at);
    $exported_at = $faker->dateTimeBetween($created_at);

    return [
        'title' => $faker->text,
        'creator_id' => App\User::all()->pluck('id')->random(),
        'last_modifier_id' => $shouldBeModified ? App\User::all()->pluck('id')->random() : null,
        'last_exporter_id' => $shouldBeExported ? App\User::all()->pluck('id')->random() : null,
        'created_at' => $created_at,
        'updated_at' => $shouldBeModified ? $modified_at : $created_at,
        'last_export' => $shouldBeExported ? $exported_at : null,
    ];
});

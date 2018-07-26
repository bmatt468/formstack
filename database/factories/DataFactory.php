<?php

use Faker\Generator as Faker;

$factory->define(App\Data::class, function (Faker $faker) {
    // Define our potential types, and pick a type to use.
    $types = ['string','number','date'];
    $type = $types[random_int(0, 2)];

    // Get our random value based off of our selected type
    $value = null;
    switch ($type) {
        case 'string':
            $value = $faker->text;
            break;

        case 'number':
            $makeFloat = random_int(0,1);
            if ($makeFloat) {
                $value = $faker->randomFloat;
            } else {
                $value = $faker->randomNumber;
            }
            break;

        case 'date':
            $value = $faker->unixTime;
            break;

        // Let the default be a string (though we should never hit here)
        default:
            $value = $faker->text;
            break;
    }

    return [
        'key' => $faker->word,
        'type' => $type,
        'value' => $value,
    ];
});

<?php

use App\Field;
use App\Subscriber;
use Faker\Generator as Faker;

$factory->define(Field::class, function (Faker $faker) {
    return [
        'subscriber_id' => function () {
            return factory(Subscriber::class)->create()->id;
        },
        'title' => $faker->word,
        'type' => $faker->randomElement(['date', 'number', 'string', 'boolean'])
    ];
});
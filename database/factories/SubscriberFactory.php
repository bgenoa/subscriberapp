<?php

use App\Subscriber;
use Faker\Generator as Faker;

$factory->define(Subscriber::class, function (Faker $faker) {
    return [
        'email' => $faker->unique()->safeEmail,
        'firstname' => $faker->firstName,
        'lastname' => $faker->lastName, 
        'state' => $faker->randomElement(['active', 'unsubscribed', 'junk', 'bounced', 'unconfirmed'])
    ];
});
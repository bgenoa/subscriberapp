<?php

use Illuminate\Database\Seeder;

class SubscriberFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // use factory to create Subscriber and Field records
        factory(\App\Subscriber::class, 5)->create()->each(function($s) {            
            $s->fields()->saveMany(
                factory(\App\Field::class, Arr::random([0, 1, 2, 3]))->make(['subscriber_id' => NULL])
            );
        });
    }
}

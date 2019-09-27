<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FieldTest extends TestCase
{
    use DatabaseTransactions;

    // Create and authenticate users
    protected function authenticate() 
    {
        // Create Test User
        //if(empty(\App\User::where('email', 'test@test.com'))) {
            \App\User::create([
                'name' => 'Test User', 
                'email' => 'test@test.com', 
                'password' => bcrypt('testing123$')
            ]);
        //}

        // POST authorization request
        $response = $this->post(route('passport.token'), [
            'username' => 'test@test.com',
            'password' => 'testing123$',
            'grant_type' => 'password', 
            'client_id' => env('PASSPORT_CLIENT_ID'), 
            'client_secret' => env('PASSPORT_CLIENT_SECRET')
        ]);

        return $response->json()['access_token'];
    }
    
    /**
     * Test auth failure on Field resource
     *
     * @return void
     */
    public function testAuthFailure()
    {
        // Get auth token
        $token = 'invalidtoken';

        // get subscriber list
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('GET', route('fields.index'));
        
        // test unauthorized response
        $response->assertUnauthorized();
    }

    /**
     * Test retrieve all fields
     *
     * @return void
     */
    public function testListFields()
    {
        // Get auth token
        $token = $this->authenticate();

        // use factory to create Field records for Subscriber records
        $fields = factory(\App\Subscriber::class, 5)->create()->each(function($s) {            
            $s->fields()->saveMany(
                factory(\App\Field::class, 10)->make(['subscriber_id' => NULL])
            );
        });

        // get field list
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('GET', route('fields.index'));
        
        // test successful response
        $response->assertStatus(200);
    }

    /**
     * Test can create field for subscriber
     *
     * @return void
     */
    public function testCreateField()
    {
        // Get auth token
        $token = $this->authenticate();

        // use factory to create Subscriber record
        $subscriber = factory(\App\Subscriber::class)->create();

        // Create Field data
        $data = [
            'title' => $this->faker->word,
            'type' => $this->faker->randomElement(['date', 'number', 'string', 'boolean'])
        ];

        // Send Subscriber field store
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('POST', route('subscribers.fields.store', $subscriber->id), $data);

        // test successful response
        $response->assertStatus(200);
    }

    /**
     * Test create field for subscriber with invalid type
     *
     * @return void
     */
    public function testCreateFieldInvalidType()
    {
        // Get auth token
        $token = $this->authenticate();

        // use factory to create Subscriber record
        $subscriber = factory(\App\Subscriber::class)->create();

        // Create Field data
        $data = [
            'title' => $this->faker->word,
            'type' => 'blah'
        ];

        // Send Subscriber field store
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('POST', route('subscribers.fields.store', $subscriber->id), $data);

        // test invalid request response
        $response->assertStatus(400);
    }

    /**
     * Test retrieve field record
     *
     * @return void
     */
    public function testRetrieveField()
    {
        // Get auth token
        $token = $this->authenticate();

        // use factory to create Field record
        $field = factory(\App\Field::class)->create();
        
        // get field
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('GET', route('fields.show', $field->id));
        
        // test successful response
        $response->assertStatus(200);
    }

    /**
     * Test Field record not found
     *
     * @return void
     */
    public function testRetrieveFieldNotFound()
    {
        // Get auth token
        $token = $this->authenticate();

        // use factory to create Field record
        $field = factory(\App\Field::class)->create();
        
        // get field
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('GET', route('fields.show', -1));
        
        // test not found response
        $response->assertStatus(404);
    }

    /**
     * Test retrieve all fields for Subscriber record
     *
     * @return void
     */
    public function testRetrieveSubscriberFields()
    {
        // Get auth token
        $token = $this->authenticate();

        // use factory to create Subscriber record
        $subscriber = factory(\App\Subscriber::class)->create();

        // use factory to create several Field records for Subscriber
        $subscriber->fields()->saveMany(
            factory(\App\Field::class, 10)->make(['subscriber_id' => NULL])
        );
        
        // get subscriber fields
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('GET', route('subscribers.fields.list', $subscriber->id));
        
        // test successful response
        $response->assertStatus(200);

        // test all fields are returned
        $response->assertJsonCount(10, 'data');
    }

    /**
     * Test update Field record for Subscriber
     *
     * @return void
     */
    public function testUpdateSubscriberField()
    {
        // Get auth token
        $token = $this->authenticate();

        // use factory to create Subscriber record
        $subscriber = factory(\App\Subscriber::class)->create();

        // use factory to create Field record for Subscriber
        $subscriber->fields()->saveMany(
            factory(\App\Field::class, 1)->make(['subscriber_id' => NULL])
        );

        // id for Subscriber field
        $field_id = $subscriber->fields()->first()->id;

        // set new data
        $data = [
            'type' => 'string'
        ];
        
        // update subscriber field
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('PATCH', route('fields.update', $field_id), $data);
        
        // test successful response
        $response->assertStatus(200);
    }

    /**
     * Test update Field record for Subscriber with invalid data
     *
     * @return void
     */
    public function testUpdateSubscriberFieldInvalid()
    {
        // Get auth token
        $token = $this->authenticate();

        // use factory to create Subscriber record
        $subscriber = factory(\App\Subscriber::class)->create();

        // use factory to create Field record for Subscriber
        $subscriber->fields()->saveMany(
            factory(\App\Field::class, 1)->make(['subscriber_id' => NULL])
        );

        // id for Subscriber field
        $field_id = $subscriber->fields()->first()->id;

        // set new data
        $data = [
            'type' => 'blah'
        ];
        
        // update subscriber field
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('PATCH', route('fields.update', $field_id), $data);
        
        // test successful response
        $response->assertStatus(400);
    }

    /**
     * Test can delete field record
     *
     * @return void
     */
    public function testDeleteField()
    {
        // Get auth token
        $token = $this->authenticate();

        // use factory to create Subscriber record
        $subscriber = factory(\App\Subscriber::class)->create();

        // use factory to create Field record for Subscriber
        $subscriber->fields()->saveMany(
            factory(\App\Field::class, 1)->make(['subscriber_id' => NULL])
        );

        // id for Subscriber field
        $field_id = $subscriber->fields()->first()->id;
        
        // delete field
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('DELETE', route('fields.destroy', $field_id));
        
        // test successful response
        $response->assertStatus(200);
    }
}

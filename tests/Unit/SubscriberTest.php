<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SubscribeTest extends TestCase
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
     * Test auth failure on Subscriber resource
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
        ])->json('GET', route('subscribers.index'));
        
        // test unauthorized response
        $response->assertUnauthorized();
    }
    
    /**
     * Test retrieve all subscribers
     *
     * @return void
     */
    public function testListSubscribers()
    {
        // Get auth token
        $token = $this->authenticate();

        // use factory to create Subscriber records
        $subscribers = factory(\App\Subscriber::class, 5)->create();

        // get subscriber list
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('GET', route('subscribers.index'));
        
        // test successful response
        $response->assertStatus(200);
    }

    /**
     * Test can create subscriber
     *
     * @return void
     */
    public function testCreateSubscriber()
    {
        // Get auth token
        $token = $this->authenticate();

        // Create Subscriber data
        $data = [
            'email' => $this->faker->unique()->safeEmail,
            'firstname' => $this->faker->name,
            'lastname' => $this->faker->name,
            'state' => 'active'
        ];

        // Send Subscriber store
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('POST', route('subscribers.store'), $data);

        // test successful response
        $response->assertStatus(200);
    }

    /**
     * Test create subscriber with invalid state
     *
     * @return void
     */
    public function testCreateSubscriberInvalidState()
    {
        // Get auth token
        $token = $this->authenticate();

        // Create Subscriber data
        $data = [
            'email' => $this->faker->unique()->safeEmail,
            'firstname' => $this->faker->name,
            'lastname' => $this->faker->name,
            'state' => 'blah'
        ];

        // Send Subscriber store
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('POST', route('subscribers.store'), $data);

        // test invalid request response
        $response->assertStatus(400);
    }

    /**
     * Test create subscriber with invalid email
     *
     * @return void
     */
    public function testCreateSubscriberInvalidEmail()
    {
        // Get auth token
        $token = $this->authenticate();

        // Create Subscriber data
        $data = [
            'email' => $this->faker->name,
            'firstname' => $this->faker->name,
            'lastname' => $this->faker->name,
            'state' => 'active'
        ];

        // Send Subscriber store
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('POST', route('subscribers.store'), $data);

        // test invalid request response
        $response->assertStatus(400);
    }

    /**
     * Test create subscriber with invalid email
     *
     * @return void
     */
    public function testCreateSubscriberInvalidEmailNotUnique()
    {
        // Get auth token
        $token = $this->authenticate();

        // Create Subscriber data
        $data = [
            'email' => $this->faker->unique()->safeEmail,
            'firstname' => $this->faker->name,
            'lastname' => $this->faker->name,
            'state' => 'blah'
        ];

        // Send Subscriber store
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('POST', route('subscribers.store'), $data);

        // Send Subscriber store with same email
        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('POST', route('subscribers.store'), $data);

        // test invalid request response
        $response2->assertStatus(400);
    }

    /**
     * Test retrieve subscriber record
     *
     * @return void
     */
    public function testRetrieveSubscriber()
    {
        // Get auth token
        $token = $this->authenticate();

        // use factory to create Subscriber record
        $subscriber = factory(\App\Subscriber::class)->create();
        
        // get subscriber
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('GET', route('subscribers.show', $subscriber->id));
        
        // test successful response
        $response->assertStatus(200);
    }

    /**
     * Test subscriber record not found
     *
     * @return void
     */
    public function testRetrieveSubscriberNotFound()
    {
        // Get auth token
        $token = $this->authenticate();

        // use factory to create Subscriber record
        $subscriber = factory(\App\Subscriber::class)->create();
        
        // get subscriber
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('GET', route('subscribers.show', -1));
        
        // test not found response
        $response->assertStatus(404);
    }

    /**
     * Test update subscriber record
     *
     * @return void
     */
    public function testUpdateSubscriber()
    {
        // Get auth token
        $token = $this->authenticate();

        // use factory to create Subscriber record
        $subscriber = factory(\App\Subscriber::class)->create();
        
        // set new data
        $data = [
            'firstname' => $this->faker->name,
            'state' => 'unsubscribed'
        ];
        
        // udpate subscriber
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('PATCH', route('subscribers.update', $subscriber->id), $data);
        
        // test successful response
        $response->assertStatus(200);
    }

    /**
     * Test update subscriber record to taken email
     *
     * @return void
     */
    public function testUpdateSubscriberEmailExists()
    {
        // Get auth token
        $token = $this->authenticate();

        // use factory to create Subscriber record
        $subscriber = factory(\App\Subscriber::class)->create();
        $subscriber2 = factory(\App\Subscriber::class)->create();
        
        // set new data, including second subscriber email
        $data = [
            'firstname' => $this->faker->name,
            'state' => 'unsubscribed', 
            'email' => $subscriber2->email
        ];
        
        // udpate subscriber
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('PATCH', route('subscribers.update', $subscriber->id), $data);
        
        // test invalid request response
        $response->assertStatus(400);
    }

    /**
     * Test can delete subscriber record
     *
     * @return void
     */
    public function testDeleteSubscriber()
    {
        // Get auth token
        $token = $this->authenticate();

        // use factory to create Subscriber record
        $subscriber = factory(\App\Subscriber::class)->create();
        
        // delete subscriber
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('DELETE', route('subscribers.destroy', $subscriber->id));
        
        // test successful response
        $response->assertStatus(200);
    }
}

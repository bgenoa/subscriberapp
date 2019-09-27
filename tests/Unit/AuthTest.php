<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    /**
     * Testing authorization with Passport
     *
     * @return void
     */
    public function testAuthorization()
    {
        // Create Test User
        \App\User::create([
            'name' => 'Test User', 
            'email' => 'test1@test.com', 
            'password' => bcrypt('testing123$')
        ]);

        // POST authorization request
        $response = $this->post(route('passport.token'), [
            'username' => 'test1@test.com',
            'password' => 'testing123$',
            'grant_type' => 'password', 
            'client_id' => env('PASSPORT_CLIENT_ID'), 
            'client_secret' => env('PASSPORT_CLIENT_SECRET')
        ]);

        // Confirm successful response
        $response->assertStatus(200);

        // Confirm access token in response
        $this->assertArrayHasKey('access_token', $response->json());

        // Delete user
        \App\User::where('email','test1@test.com')->delete();
    }

    /**
     * Testing failed authorization with Passport
     *
     * @return void
     */
    public function testFailedAuthorization()
    {
        // Create Test User
        \App\User::create([
            'name' => 'Test User 2', 
            'email' => 'test2@test.com', 
            'password' => bcrypt('testing123$')
        ]);

        // POST authorization request with incorrect credentials
        $response = $this->post(route('passport.token'), [
            'username' => 'test2@test.com',
            'password' => 'testing',
            'grant_type' => 'password', 
            'client_id' => env('PASSPORT_CLIENT_ID'), 
            'client_secret' => env('PASSPORT_CLIENT_SECRET')
        ]);

        // Confirm successful response
        $response->assertStatus(401);

        // Delete user
        \App\User::where('email','test2@test.com')->delete();
    }
}

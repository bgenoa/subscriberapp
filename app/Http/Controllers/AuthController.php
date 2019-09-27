<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException as GuzzleException;

class AuthController extends Controller
{
    /**
     * POST API to get a token for this user
     */
    public function login(Request $request) 
    {
        $guzzle = new GuzzleClient();
        
        try 
        {
            $response = $guzzle->post(route('passport.token'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => env('PASSPORT_CLIENT_ID'),
                    'client_secret' => env('PASSPORT_CLIENT_SECRET'),
                    'username' => $request->username,
                    'password' => $request->password
                ]
            ]);
            
            $body = new \stdClass();
            $body->access_token = json_decode($response->getBody())->access_token;
            
            return response()->json($body, 200);
        } 
        catch (GuzzleException $e) 
        {
            $error = new \stdClass();
            $error->message = '';
            
            if ($e->getCode() === 400) {
                $error->message = "Invalid Request.  Please enter a username and password.";
                return response()->json($error, $e->getCode());
            } else if ($e->getCode() === 401) {
                $error->message = "Invalid Credentials.  Please try again.";
                return response()->json($error, $e->getCode());
            }
            else {
                $error->message = $e->getMessage();
                return response()->json($error, $e->getCode());
            }        
        }
    }

    /**
     * POST API to logout and destroy all tokens for this user
     */
    public function logout(Request $request) 
    {        
        $header = $request->header('Authorization');
        
        if (!isset($header)) {
            $response = new \stdClass();
            $response->message = "No token provided";
            return response()->json($response, 400);
        }
        
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
        
        $response = new \stdClass();
        $response->message = "Logged out successfully";
        
        return response()->json($response, 200);
    }
}

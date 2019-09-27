<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Login route
Route::post('/login', 'AuthController@login')->name('login');

// Authenticated API user routes
Route::middleware('auth:api')->group( function () {
    Route::resource('subscribers', 'Api\SubscriberController');
    
    Route::get('fields', 'Api\FieldController@index')->name('fields.index');
    Route::get('fields/{field}', 'Api\FieldController@show')->name('fields.show');
    Route::patch('fields/{field}', 'Api\FieldController@update')->name('fields.update');
    Route::delete('fields/{field}', 'Api\FieldController@destroy')->name('fields.destroy');

    // Fields belong to a subscriber so creating should be done through Subscriber model
    Route::get('subscribers/{subscriber}/fields', 'Api\FieldController@showAllForSubscriber')->name('subscribers.fields.list');
    Route::post('subscribers/{subscriber}/fields', 'Api\FieldController@store')->name('subscribers.fields.store');

    // logout
    Route::post('/logout', 'AuthController@logout');

    // get pulse
    Route::get('/pulse', function() {
        $result = new \stdClass();
        $result->status = "OK";
        return response()->json($result, 200);
    });

    // get user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

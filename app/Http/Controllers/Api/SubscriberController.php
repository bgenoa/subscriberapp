<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use App\Subscriber;
use Validator;
use Illuminate\Validation\Rule;

class SubscriberController extends ResponseController
{
    /**
     * List Resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get all subscribers
        $products = Subscriber::all();

        return $this->sendResponse($products->toArray(), 'All Subscribers');
    }

    /**
     * Save new subscriber.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validate request
        $input = $request->all();

        $validator = Validator::make($input, [
            'email' => 'required|email|unique:subscribers', 
            'state' => [
                Rule::in(['active', 'unsubscribed', 'junk', 'bounced', 'unconfirmed'])
            ]
        ]);

        if($validator->fails()){
            return $this->sendError('New Subscriber Validation Error.', $validator->errors(), 400);       
        }

        // create new subscriber model
        $subscriber = new Subscriber();
        $subscriber->email = $request->email;
        $subscriber->firstname = $request->firstname;
        $subscriber->lastname = $request->lastname;
        $subscriber->state = (empty($request->state) ? 'unconfirmed' : $request->state);        
        $subscriber->save();

        return $this->sendResponse($subscriber->toArray(), 'Subscriber created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // get specified subscriber
        $subscriber = Subscriber::find($id);

        if (empty($subscriber)) {
            return $this->sendError('Subscriber not found.', [], 404);
        }

        return $this->sendResponse($subscriber->toArray(), 'Retrieved subscriber');
    }

    /**
     * Update the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subscriber $subscriber)
    {
        // validate request
        $props = $request->all();
        
        $validator = Validator::make($props, [
            'email' => [
                Rule::unique('subscribers')->ignore($subscriber->id),
            ], 
            'state' => [
                Rule::in(['active', 'unsubscribed', 'junk', 'bounced', 'unconfirmed'])
            ]
        ]);

        if($validator->fails()){
            return $this->sendError('Update Subscriber Validation Error.', $validator->errors(), 400);       
        }

        // update model with request properties
        foreach($props as $prop => $value) {
            $subscriber->$prop = $value;
        }
        
        $subscriber->save();

        return $this->sendResponse($subscriber->toArray(), 'Subscriber successfully updated.');
    }

    /**
     * Delete the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subscriber $subscriber)
    {
        // delete specified subscriber
        $subscriber->delete();

        return $this->sendResponse($subscriber->toArray(), 'Subscriber record deleted.');
    }
}

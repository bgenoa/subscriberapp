<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use App\Subscriber;
use App\Field;
use Validator;
use Illuminate\Validation\Rule;

class FieldController extends ResponseController
{
    /**
     * List Resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all fields
        $fields = Field::all();

        return $this->sendResponse($fields->toArray(), 'All Fields');
    }

    /**
     * Save new field for subscriber.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Subscriber $subscriber)
    {
        // validate incoming request
        $input = $request->all();

        $validator = Validator::make($input, [
            'title' => 'required', 
            'type' => [
                'required', 
                Rule::in(['date', 'number', 'string', 'boolean'])
            ]
        ]);

        if($validator->fails()){
            return $this->sendError('New Subscriber Field Validation Error.', $validator->errors(), 400);       
        }

        // create new field model
        $field = new Field();
        $field->title = $request->title;
        $field->type = $request->type;
        $field->subscriber_id = $subscriber->id;
        $field->save();

        return $this->sendResponse($field->toArray(), 'Subscriber Field created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // get specified field
        $field = Field::find($id);

        if (empty($field)) {
            return $this->sendError('Field not found.', [], 404);
        }

        return $this->sendResponse($field->toArray(), 'Retrieved field');
    }

    /**
     * Display all fields for Subscriber resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAllForSubscriber(Subscriber $subscriber)
    {
        // get only the fields for the specified subscriber
        $fields = $subscriber->fields()->get();

        if (empty($fields)) {
            return $this->sendError('Fields not found.', [], 404);
        }

        return $this->sendResponse($fields->toArray(), 'Retrieved fields for Subscriber');
    }

    /**
     * Update the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Field $field)
    {
        // validate request
        $props = $request->all();
        
        $validator = Validator::make($props, [
            'title' => 'sometimes:required', 
            'type' => [
                'sometimes', 
                'required', 
                Rule::in(['date', 'number', 'string', 'boolean'])
            ]
        ]);

        if($validator->fails()){
            return $this->sendError('Update Subscriber Field Validation Error.', $validator->errors(), 400);       
        }

        // update model with request properties
        foreach($props as $prop => $value) {
            $field->$prop = $value;
        }
        
        $field->save();

        return $this->sendResponse($field->toArray(), 'Field successfully updated.');
    }

    /**
     * Delete the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Field $field)
    {
        // delete the specified field
        $field->delete();

        return $this->sendResponse($field->toArray(), 'Field record deleted.');
    }
}

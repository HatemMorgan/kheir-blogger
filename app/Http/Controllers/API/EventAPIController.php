<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Photo;
use App\Event;

class EventAPIController extends Controller
{

    /**
     *  get json list of all organizations
     */
    public function index()
    {
        $events = Event::all();
        return $events;
    }


    /**
     *  show a json of an organization and all its events, reviews and subscribers
     */
    public function show($id)
    {
          $event = Event::findOrFail($id);
        $event->posts = $event->posts()->get();
        $event->reviews = $event->reviews()->get();
        $event->questions = $event->questions()->get();
        $event->photos = $event->photos()->get();
        return $event;
    }

}
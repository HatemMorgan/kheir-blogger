<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Event\EventController;

use App\Http\Requests\EventPostRequest;

use App\Event;
use App\EventPost;
use App\Notification;
use App\Organization;

class EventPostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth_organization', ['only' => [
            'create', 'store', 'edit', 'update','destroy'
        ]]);
    }

    /**
     * Show all posts of a certain event
     */
    public function index($id)
    {
        $event = Event::findOrFail($id);
        return $event->posts()->get();
    }

    /**
     * Show a certain event post
     */
    public function show($id)
    {
        //TODO
    }

    /**
     * Create a new event post
     */
    public function create($event_id)
    {
        $event = Event::findOrFail($event_id);
        if($event->organization()->id == auth()->guard('organization')->id())
            return view('event.post.create', compact('event_id'));
        return redirect()->action('EventController@show', [$event_id]);
    }

    /*
    * Add a new post to the event (and notify users).
    */
    public function store(EventPostRequest $request, $event_id)
    {
        $organization_id = auth()->guard('organization')->user()->id;
        $eventPost = new EventPost($request->all());
        $eventPost->event_id = $event_id;
        $eventPost->organization_id = $organization_id;
        $eventPost->save();
        if($request->sendnotifications == 1)
        {
            $event = Event::find($event_id);
            $description = "Event ".($event->name)." has a new post";
            $link = "/event".$event_id;
            Notification::notify($event->volunteers, 4, $event, $description, $link);
        }
        return redirect()->action('Event\EventController@show', [$event_id]);
    }

    /**
     * Edit an event post
     */
    public function edit()
    {
        //TODO
    }

    /**
     * Update the edited event post
     */
    public function update()
    {
        //TODO
    }

    /**
     * Delete an event post
     */
    public function destroy($id,$post_id)
    {
        $event = Event::findOrFail($id);
        $post  = EventPost::findOrFail($post_id);

		if(auth()->guard('organization')->user()->id == $event->organization()->id)
		{
			$post->delete();
		}
        return redirect()->action('Event\EventController@show', [$id]);
    }
}
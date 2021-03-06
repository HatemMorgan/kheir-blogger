<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Services\EventService;

use Illuminate\Http\Request;
use App\Http\Requests\EventRequest;

use App\Event;

class EventAPIController extends Controller
{
  private $eventService;

  /**
   * Constructor.
   * Sets middlewares for controller functions and initializes event service.
   */
  public function __construct()
  {
      $this->eventService = new EventService();
      $this->middleware('auth_volunteer', ['only' => [
        'follow', 'unfollow', 'register', 'unregister',
        'attend', 'unattend'
      ]]);

      $this->middleware('auth_organization', ['only' => [
    	   'store', 'update', 'cancel'
      ]]);

      $this->middleware('auth_admin', ['only' => ['destroy']]);
  }

/*
|==========================================================================
| Event CRUD Functions
|==========================================================================
|
*/
    /**
     *  Get json list of all events.
     */
    public function index()
    {
        $events = Event::all();
        return response()->json($events);
    }

    /**
     *  Show a json of an event and all its posts, reviews and questions and photos.
     */
    public function show($id)
    {
        $event = Event::findOrFail($id);
        $event->posts = $event->posts()->get();
        $event->reviews = $event->reviews()->with('user')->get();
        $event->questions = $event->questions()->with('user')->get();
        $event->photos = $event->photos()->get();
        return response()->json($event);
    }

    /**
     * Store the created event in the database.
     */
    public function store(EventRequest $request)
    {
        $this->eventService->store($request);
        return response()->json(['message' => 'Success.'], 200);
    }

  	/**
  	 * Update the information of an edited event.
  	 */
  	public function update(EventRequest $request, $id)
  	{
  		  $this->eventService->update($request, $id);
        return response()->json(['message' => 'Success.'], 200);
  	}

    /**
     * Organization cancel an event
     * @param int $id event id.
     */
    public function cancel($id)
    {
        $success = $this->eventService->cancel($id, auth()->guard('organization')->user());
        if($success)
            return response()->json(['message' => 'Success.'], 200);
        return response()->json(['message' => 'You are not authorized to cancel this event.'], 400);
    }

    /**
     * Admin Delete an event.
     * @param int $id event id.
     */
    public function destroy($id)
    {
         $this->eventService->destroy($id);
         return response()->json(['message' => 'Success.'], 200);
    }

/*
|==========================================================================
| Volunteers' Interaction with Event
|==========================================================================
|
*/
    public function follow(Request $request, $id)
    {
        $this->eventService->follow($id, $request->get('volunteer'));
        return response()->json(['message' => 'Success.'], 200);
    }

    public function unfollow(Request $request, $id)
    {
        $this->eventService->unfollow($id, $request->get('volunteer'));
        return response()->json(['message' => 'Success.'], 200);
    }

    public function register(Request $request, $id)
    {
        $validator = $this->eventService->register($id, $request->get('volunteer'));
        if($validator->passes())
            return response()->json(['message' => 'Success.'], 200);
        return response()->json(['message' => 'Registration Failed.', 'errors' => $validator->errors()], 400);
    }

    public function unregister(Request $request, $id)
    {
        $this->eventService->unregister($id, $request->get('volunteer'));
        return response()->json(['message' => 'Success.'], 200);
    }

    public function attend(Request $request, $id)
    {
        $this->eventService->attend($id, $request->get('volunteer'));
        return response()->json(['message' => 'Success.'], 200);
    }

    public function unattend(Request $request, $id)
    {
        $this->eventService->unattend($id, $request->get('volunteer'));
        return response()->json(['message' => 'Success.'], 200);
    }
}

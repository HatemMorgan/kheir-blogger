<?php

namespace App\Http\Controllers;

use App\Http\Requests\GalleryCaptionRequest;
use App\Http\Requests\GalleryRequest;
use Illuminate\Http\Request;
use App\Http\Requests\EventRequest;

use App\Organization;
use App\Notification;
use App\Event;
use App\Photo;

use Carbon\Carbon;
use Auth;
use Input;
use Validator;
use Session;

class EventController extends Controller
{

	public function __construct()
	{
        $this->middleware('auth_volunteer', ['only' => [
			'follow', 'unfollow', 'register', 'unregister',
			'confirm', 'unconfirm'
        ]]);

        $this->middleware('auth_organization', ['only' => [
            'create', 'store', 'edit', 'update', 'destroy',
        ]]);
    }

/*
|==========================================================================
| Event CRUD Functions
|==========================================================================
|
*/
	/**
	 * Show all events of a certain organization.
	 */
	public function index($organization_id)
	{
		$organization = Organization::findOrFail($organization_id);
		$organization_name = $organization->name;
		$events = $organization->events;
		return view('event.index', compact('organization_name', 'events'));
	}

	/**
	 * Show Event's page.
	 */

	public function show($id)
	{
        $event = Event::findOrFail($id);
        $posts = $event->posts;
        $questions = $event->questions()->answered()->get();
        $reviews = $event->reviews;
		$creator = null;
		if(Auth::guard('organization')->id() == $event->organization_id)
			$creator = true;
		return view('event.show',
			compact('event', 'posts', 'questions', 'reviews', 'creator'));
	}

	/**
	 * Create a new event.
	 */
	public function create()
	{
		return view('event.create');
	}

	/**
	 * Store the created event in the database.
	 */
	public function store(EventRequest $request)
	{
		$organization = auth()->guard('organization')->user();
		$event = $organization->createEvent($request);
		$notification_description = $organization->name." created a new event ".$request->name;
		Notification::notify($organization->subscribers, $event,
							$notification_description, url("/event", $event->id));
		return redirect()->action('EventController@show', [$event->id]);
	}

	/**
	 * Edit the information of a certain event.
	 */
	public function edit($id)
	{
		$event = Event::findOrFail($id);
		if(auth()->guard('organization')->user()->id == $event->organization()->id)
			return view('event.edit', compact('event'));
		return redirect()->action('EventController@show', [$id]);
	}

	/**
	 * Update the information of an edited event.
	 */
	public function update(EventRequest $request, $id)
	{
		$event = Event::findorfail($id);
		if(auth()->guard('organization')->user()->id == $event->organization()->id)
		{
			$event = Event::findOrFail($id);
			$event->update($request->all());
			Notification::notify($event->volunteers, $event,
								"Event ".($event->name)." has been updated", url("/event",$id));
		}
		return redirect()->action('EventController@show', [$id]);
	}

	/**
	 * Cancel an event.
	 */
	public function destroy($id)
	{
		$event = Event::findOrFail($id);
		if(auth()->guard('organization')->user()->id == $event->organization()->id)
		{
			$event->delete();
			Notification::notify($event->volunteers, null,
								"Event ".($event->name)."has been cancelled", url("/"));
		}
		return redirect('/');
	}

    /**
     * Event Gallery.
     */

    /*public function add_photos($id)
    {
        $event = Event::findOrFail($id);
        if(auth()->guard('organization')->user()->id == $event->organization()->id)
            return view('event.gallery.upload', compact('event'));
        return redirect()->action('EventController@index', [$id]);

    }*/

    public function test()
    {
        //$event = Event::findOrFail($id);

        return view('event.gallery.upload');
    }


    public function add_photos()
    {
        $files = Input::file('images');
        $uploaded = 0;
        $failed = 0;
        $paths=array();
        $counter=0;
        foreach ($files as $file) {
            $rules = array('file' => 'required|image');
            $validator = Validator::make(array('file'=> $file), $rules);

            if($validator->passes()) {
                $destinationPath = 'db/gallery';
                //$filename = $file->getClientOriginalName();
                $filename =$counter;
                $counter++;
                $upload_success = $file->move($destinationPath,$filename);
                if($upload_success){
                    array_push($paths,$destinationPath.'/'.$filename);
                    $uploaded ++;
                }
                else {
                    $failed ++;
                }
            } else {
                $failed ++;
            }
        }

        if($failed > 0)
        {
            return redirect()->action('EventController@test');
        }
        else
        {
            return view('event.gallery.add_caption',compact('paths'));
            //return 'lolo';
        }

    }

    public function store_gallery(Request $req)
    {
        $input = $req->all();
        $captions =$input['captions'];
        //$captions = Input::text('captions');
        echo sizeof($captions).'\n';
        foreach($captions as $caption)
            if($caption)
                echo 'whynot ';
            else
                echo '1 ';
        //echo sizeof($paths);
        return 'lolo stores';
    }


    /*
    |==========================================================================
    | Volunteers' Interaction with Event
    |==========================================================================
    |
    */
	public function follow($id)
	{
		Auth::user()->followEvent($id);
		return redirect()->action('EventController@show', [$id]);
	}

	public function unfollow($id)
	{
		Auth::user()->unfollowEvent($id);
		return redirect()->action('EventController@show', [$id]);
	}

	public function register($id)
	{
		$event = Event::findOrFail($id);
		if($event->timing > carbon::now())
			Auth::user()->registerEvent($id);
		return redirect()->action('EventController@show', [$id]);
	}

	public function unregister($id)
	{
		Auth::user()->unregisterEvent($id);
		return redirect()->action('EventController@show', [$id]);
	}

	public function confirm($id)
	{
		$event = Auth::registeredEvents()->findOrFail($id);
		if($event->timing < carbon::now())
			return view('event.confirm', compact('id'));
	}

	public function attend($id)
	{
		$event = Event::findOrFail($id);
		if($event->timing < carbon::now())
			Auth::user()->attendEvent($id);
		return redirect()->action('EventController@show',[$id]);
	}

	public function unattend($id)
	{
		$event = Event::findOrFail($id);
		if($event->timing < carbon::now())
			Auth::user()->unattendEvent($id);
		return redirect()->action('EventController@show',[$id]);
	}
}

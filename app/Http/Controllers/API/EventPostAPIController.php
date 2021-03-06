<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Services\EventPostService;

use App\Http\Requests\EventPostRequest;

class EventPostAPIController extends Controller
{
    private $eventPostService;

    /**
     * Constructor.
     * Sets middlewares for controller functions and initializes event post service.
     */
    public function __construct()
    {
        $this->eventPostService = new EventPostService();
        $this->middleware('auth_organization', ['only' => [
             'store', 'update'
        ]]);
    }

    /**
     * Create a new post and store it in the database.
     */
    public function store(EventPostRequest $request, $event_id)
    {
        $this->eventPostService($request, $event_id);
        return response()->json(['message' => 'Success.'], 200);
    }

    /**
     * Update the edited event post.
     */
    public function update(EventPostRequest $request, $id, $post_id)
    {
        $this->eventPostService->update($request, $id, $post_id);
        return response()->json(['message' => 'Success.'], 200);
    }
}

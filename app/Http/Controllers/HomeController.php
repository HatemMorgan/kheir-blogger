<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Feedback;

// use Illuminate\Http\Request;
use Request;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth_both');

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /*
    *show the sendFeedback page
    */
    public function sendFeedback()
    {

      return view('sendFeedback');
    }

    public function storeFeedback($id)
    {
      $input = Request::all();
      $feedback = new Feedback;
      $feedback->user_id = $id;
      $feedback->subject = $input['subject'];
      $feedback->message = $input['message'];
    }
}

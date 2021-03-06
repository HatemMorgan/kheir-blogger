<?php
/*
|==========================================================================
| Application Routes
|==========================================================================
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
|    - Authentication Routes
|    - Functional Routes
|       |- Organization Routes
|       |- Volunteer Routes
|       |- Event Routes
|    - Search Routes
|    - Control Routes
|    - API Routes
*/

Route::group(['middleware' => ['web']], function () {

    /**
     * Homepage for logged-in volunteers/organizations or Welcome page for others.
     */
    Route::get('/', function () {
        if(Auth::user() || auth()->guard('organization')->check())
            return view('home');
        return view('welcome');
    });

    Route::get('/home', function(){ return redirect('/'); });

/*
|==========================================================================
| Authentication Routes
|==========================================================================
|
| These routes are related to the authentication of volunteers/organizations.
|
*/

    /**
     * Organization register page.
     */
    Route::get('register_organization', ['middleware' => 'guest', function(){
        return view('auth.register_organization');
    }]);

    /**
     * Organization register request.
     */
    Route::post('register_organization','Auth\OrganizationAuthController@register');

    /**
     * Organization login page.
     */
    Route::get('login_organization', ['middleware' => 'guest', function(){
        return view('auth.login_organization');
    }]);

    /**
     * Organization login request.
     */
    Route::post('login_organization','Auth\OrganizationAuthController@login');

    /**
     * Organization logout request.
     */
    Route::get('logout_organization','Auth\OrganizationAuthController@logout');

    /**
     * Organization forget password.
     */
    Route::get('password_organization/reset', 'Auth\OrganizationPasswordController@getEmail');
    Route::post('password_organization/email', 'Auth\OrganizationPasswordController@sendResetLinkEmail');
    Route::get('password_organization/reset/{token?}', 'Auth\OrganizationPasswordController@getReset');
    Route::post('password_organization/reset', 'Auth\OrganizationPasswordController@reset');

    /**
     *  Volunteer Authentication (register/login/logout)
     */
    Route::auth();

    /**
     *  Volunteer Registeration Page.
     */
    Route::get('register', ['middleware' => 'guest', function(){
        return view('auth.register');
    }]);

    /**
     *  Volunteer Login Page.
     */
    Route::get('login', ['middleware' => 'guest', function(){
        return view('auth.login');
    }]);


/*
|==========================================================================
| Functional Routes
|==========================================================================
|
| These routes are related to the main actions of the applications
| associated with volunteers, organizations or events.
*/

    /*
    |-----------------------
    | Organization Routes
    |-----------------------
    */

    /**
     * Organization Subscription.
     */
    Route::get('organization/{id}/subscribe', 'Organization\OrganizationController@subscribe');
    Route::get('organization/{id}/unsubscribe', 'Organization\OrganizationController@unsubscribe');

    /**
     * Organization Recommendation.
     */
    Route::get('organization/{id}/recommend' , 'Organization\OrganizationController@recommend');
    Route::post('organization/{id}/recommend' , 'Organization\OrganizationController@storeRecommendation');
    Route::get('organization/{id}/recommendations', 'Organization\OrganizationController@viewRecommendations');

    /**
     * Organization Reviewing.
     */
    Route::get('organization/{id}/reviews' , 'Organization\OrganizationReviewController@index');
    Route::get('organization/{id}/review/{r_id}/report', 'Organization\OrganizationReviewController@report');
    Route::resource('organization/{id}/review', 'Organization\OrganizationReviewController');

    /**
     * Organizaton Blocking.
     */
    Route::get('organization/{id}/block','Organization\OrganizationController@block');
    Route::get('organization/{id}/unblock','Organization\OrganizationController@unblock');

    /**
     * Organization Events.
     */
    Route::get('organization/{id}/events', 'Event\EventController@index');

    /**
     * Organization CRUD.
     */
    Route::resource('organization', 'Organization\OrganizationController', ['only' => [
        'show', 'edit', 'update', 'destroy'
    ]]);

    /*
    |-----------------------
    | Volunteer Routes
    |-----------------------
    */

    /**
     *  Volunteer challenges.
     */
    Route::get('volunteer/challenge', 'Volunteer\ChallengeController@index');
    Route::get('volunteer/challenge/create', 'Volunteer\ChallengeController@create');
    Route::post('volunteer/challenge', 'Volunteer\ChallengeController@store');
    Route::get('volunteer/challenge/edit', 'Volunteer\ChallengeController@edit');
    Route::patch('volunteer/challenge', 'Volunteer\ChallengeController@update');
    Route::get('volunteer/challenge/achieved',
                'Volunteer\ChallengeController@viewCurrentYearAttendedEvents');

    /**
     * Volunteer notifications.
     */
    Route::get('notifications', 'Volunteer\VolunteerController@showNotifications');
    Route::post('notifications', 'Volunteer\VolunteerController@unreadNotification');

    /**
     * Send feedback to the admin.
     */
    Route::get('feedback' , 'Volunteer\VolunteerController@createFeedback');
    Route::post('feedback' , 'Volunteer\VolunteerController@storeFeedback');


    /**
     * Volunteer dashboard.
     */
    Route::get('dashboard/subscribed_organizations', 'Volunteer\VolunteerController@showSubscribedOrganizations');
    Route::get('dashboard/events','Volunteer\VolunteerController@showAllEvents');
    Route::get('dashboard', 'Volunteer\VolunteerController@showDashboard');

     /**
      * Volunteer assign locations.
      */
    Route::post('locations','Volunteer\VolunteerController@assignLocations');

    /**
     * Volunteer CRUD.
     */
    Route::resource('volunteer','Volunteer\VolunteerController', ['only' => [
        'show', 'edit', 'update'
    ]]);

    /*
    |-----------------------
    | Event Routes
    |-----------------------
    */

    /**
     *	Event Following.
     */
    Route::get('event/{id}/follow', 'Event\EventController@follow');
    Route::get('event/{id}/unfollow', 'Event\EventController@unfollow');

    /**
     *	Event Registeration.
     */
    Route::get('event/{id}/register', 'Event\EventController@register');
    Route::get('event/{id}/unregister', 'Event\EventController@unregister');

    /**
     * Event Attendance Confirmation.
     */
    Route::get('event/{id}/attend' , 'Event\EventController@attend');
    Route::get('event/{id}/unattend' , 'Event\EventController@unattend');

    /**
     * Event Post.
     */
    Route::resource('/event/{id}/post','Event\EventPostController');

    /**
     * Event Question.
     */
    Route::get('event/{id}/question/answer', 'Event\EventQuestionController@viewUnansweredQuestions');
    Route::post('event/{id}/question/{question}/answer', 'Event\EventQuestionController@answer');
    Route::resource('event/{id}/question', 'Event\EventQuestionController');

    /**
     * Event Gallery
     */
    Route::get('event/{id}/gallery/upload','Event\EventGalleryController@add');
    Route::post('event/{id}/gallery/upload','Event\EventGalleryController@upload');
    Route::post('event/{id}/gallery','Event\EventGalleryController@store');
    Route::get('event/{id}/gallery/{photo_id}/edit','Event\EventGalleryController@edit');
    Route::patch('event/{id}/gallery/{photo_id}','Event\EventGalleryController@update');
    Route::delete('event/{id}/gallery/{photo_id}','Event\EventGalleryController@destroy');

    /**
     * Event Reviewing.
     */
    Route::get('event/{id}/review/{r_id}/report', 'Event\EventReviewController@report');
    Route::resource('event/{id}/review','Event\EventReviewController');

    /**
     *  Event CRUD.
     */
    Route::delete('event/{id}/cancel', 'Event\EventController@cancel');
    Route::resource('event','Event\EventController', ['except' => 'index']);


/*
|==========================================================================
| Search Routes
|==========================================================================
|
| These routes are related to search on organizations or events.
*/

    Route::post('search', 'SearchController@searchAll');


/*
|==========================================================================
| Control Routes
|==========================================================================
|
| These routes are related to admins and validators to control the
| interactions on the website. Admins are considered as validators.
*/

    /**
     * Admin assign validator.
     */
    Route::get('volunteer/{id}/validate','AdminController@assignValidator');

    /**
     * Validator view org   anizations.
     */
    Route::get('organizations', 'AdminController@viewOrganizations');

    /**
     * Validator ban volunteer.
     */
    Route::get('volunteer/{id}/ban','AdminController@banVolunteer');

    /**
     * Valiadator view event review reports.
     */
    Route::get('review/reports/event','AdminController@viewEventReviewReports');

    /**
     * Validator mark event review report "viewed"
     */
    Route::post('review/reports/event/{id}/{viewed?}','AdminController@setEventReviewReportViewed');

    /**
     * Volunteer view feedbacks.
     */
    Route::get('feedbacks', 'AdminController@viewFeedbacks');

/*
|==========================================================================
| API Routes
|==========================================================================
|
| API routes are used by mobile applications.
|   - Authentication API Routes
|   - Organization API Routes
|   - Volunteer API Routes
|   - Event API Routes
*/
    /*
    |--------------------------
    | Authentication API Routes
    |--------------------------
    */

    /**
     * Volunteer Authentication
     */
    Route::post('api/register', 'API\AuthAPIController@register');
    Route::post('api/login', 'API\AuthAPIController@login');
    Route::post('api/logout', 'API\AuthAPIController@logout');

    /**
     * Organization Authentication
     */
    Route::post('api/register_organization', 'API\OrganizationAuthAPIController@register');
    Route::post('api/login_organization', 'API\OrganizationAuthAPIController@login');
    Route::post('api/logout_organization', 'API\OrganizationAuthAPIController@logout');

    /*
    |--------------------------
    | Organizations API Routes
    |--------------------------
    */

    /**
     * Organization Subscription.
     */
    Route::post('api/organization/{id}/subscribe', 'API\OrganizationAPIController@subscribe');
    Route::post('api/organization/{id}/unsubscribe', 'API\OrganizationAPIController@unsubscribe');

    /**
     * Organization Recommendation.
     */
    Route::post('api/organization/{id}/recommend' , 'API\OrganizationAPIController@storeRecommendation');
    Route::get('api/organization/{id}/recommendations', 'API\OrganizationAPIController@viewRecommendations');

    /**
     * Organization Reviewing.
     */
    Route::post('api/organization/{id}/review' , 'API\OrganizationReviewAPIController@store');
    Route::get('api/organization/{id}/review/{r_id}/report','API\OrganizationReviewAPIController@report');

    /**
     * Organizaton Blocking.
     */
    Route::post('api/organization/{id}/block','API\OrganizationAPIController@block');
    Route::post('api/organization/{id}/unblock','API\OrganizationAPIController@unblock');

    /**
     *  Organization API CRUD.
     */
    Route::resource('api/organization','API\OrganizationAPIController', ['only' => [
        'index', 'show', 'update',
    ]]);


    /*
    |-----------------------
    | Volunteer API Routes
    |-----------------------
    */

    /**
     *  Volunteer challenges.
     */
    Route::get('api/volunteer/challenge', 'API\ChallengeAPIController@index');
    Route::post('api/volunteer/challenge', 'API\ChallengeAPIController@store');
    Route::patch('api/volunteer/challenge', 'API\ChallengeAPIController@update');
    Route::get('api/volunteer/challenge/achieved',
                'API\ChallengeAPIController@viewCurrentYearAttendedEvents');

    /**
     * Volunteer notifications.
     */
     Route::get('api/notifications', 'API\VolunteerAPIController@showNotifications');
     Route::post('api/notifications', 'API\VolunteerAPIController@unreadNotification');

     /**
      * Send feedback to the admin.
      */
    Route::post('api/feedback' , 'API\VolunteerAPIController@storeFeedback');

    /**
    * Volunteer API CRUD.
    */
    Route::resource('api/volunteer','API\VolunteerAPIController', ['only' => [
        'show', 'update',
    ]]);

    /*
    |--------------------------
    | Events API Routes
    |--------------------------
    */

    /**
     *	Event Following.
     */
     Route::get('api/event/follow/{id}' , 'API\EventAPIController@follow');
     Route::get('api/event/unfollow/{id}' , 'API\EventAPIController@unfollow');


    /**
     *  Event Registeration.
     */
     Route::get('api/event/register/{id}' , 'API\EventAPIController@register');
     Route::get('api/event/unregister/{id}' , 'API\EventAPIController@unregister');

    /**
     *  Event Attendance Confirmation.
     */
     Route::get('api/event/attend/{id}' , 'API\EventAPIController@attend');
     Route::get('api/event/unattend/{id}' , 'API\EventAPIController@unattend');

     /**
      * Event Post.
      */
     Route::post('api/event/{id}/post','API\EventPostAPIController@store');
     Route::patch('api/event/{id}/post/{post_id}','API\EventPostAPIController@update');

     /**
      * Event Question.
      */
     Route::get('api/event/{id}/question/answer', 'API\EventQuestionAPIController@viewUnansweredQuestions');
     Route::post('api/event/{id}/question/{question}/answer', 'API\EventQuestionAPIController@answer');
     Route::resource('api/event/{id}/question', 'API\EventQuestionAPIController', ['only' => ['store']]);

    /**
     * Event Reviewing.
     */
     Route::get('api/event/{id}/review/{r_id}/report' , 'API\EventReviewAPIController@report');
     Route::post('api/review/event' , 'API\EventReviewAPIController@store');

    /**
     * Event API CRUD.
     */
     Route::delete('api/event/{id}/cancel', 'API\EventAPIController@cancel');
     Route::resource('api/event','API\EventAPIController');
});

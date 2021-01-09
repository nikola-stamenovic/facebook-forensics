<?php

namespace App\Http\Controllers;

use App\User;
use Facebook\Facebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacebookUser extends Controller
{

    public function __construct()
    {
        $this->middleware('facebook_auth')->only('show');
    }

    public function store(Facebook $fb, Request $request) //method injection
    {
        // retrieve form input parameters
        $uid = $request->uid;
        $access_token = $request->access_token;
        $permissions = $request->permissions;

        // assuming we have a User model already set up for our database
        // and assuming facebook_id field to exist in users table in database
//        $user = User::firstOrCreate(['facebook_id' => $uid]);

        // get long term access token for future use
        $oAuth2Client = $fb->getOAuth2Client();

        // assuming access_token field to exist in users table in database
//        $user->access_token = $oAuth2Client->getLongLivedAccessToken($access_token)->getValue();
//        $user->save();

        $access_token = $oAuth2Client->getLongLivedAccessToken($access_token)->getValue();

        // set default access token for all future requests to Facebook API
        $fb->setDefaultAccessToken($access_token);

        $fields = "id,cover,name,first_name,last_name,age_range,link,gender,locale,picture,timezone,updated_time,verified";
        $fb_user = $fb->get('/me?fields='.$fields)->getGraphNode();
//        $request->session()->put('access_token', $access_token);

        $user = new User();
        $user->name = $fb_user['name'];
        $user->email = $access_token;

        Auth::login($user);

        return redirect('show');
    }

    public function show(Facebook $fb, Request $request)
    {
        dd(Auth::user());

        $access_token = $value = $request->session()->get('access_token');;

        $fb->setDefaultAccessToken($access_token);

        // call api to retrieve person's public_profile details
        $fields = "id,cover,name,first_name,last_name,age_range,link,gender,locale,picture,timezone,updated_time,verified";
        $fb_user = $fb->get('/me/friends?fields='.$fields)->getDecodedBody();
        dump($fb_user);
    }
}

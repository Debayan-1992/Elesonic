<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class FrontendNoAuthController extends Controller
{
    //After login for seller, customer this controller should be hit if hitting FrontendController then it'll keep looping
    public function dashboard()
    {
        $user_details = auth()->user();
        return view('frontend.dashboard.dashboard')->with(['user_details'=>$user_details]);
    }

    public function contact_us()
    {
        return view('frontend.contact-us');
    }

    public function contact_us_post()
    {

    }
}

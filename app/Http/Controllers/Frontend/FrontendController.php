<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use Carbon\Carbon;
use App\Model\City;
use App\Model\OtpVerification;
use App\Model\CustomerDetail;

class FrontendController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth'); //For this function i function was not working
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
 

    public function index()
    {
        $data['activemenu']['main'] = 'Home';

        return view('frontend.home', $data);
    }
}

<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use Carbon\Carbon;
use App\Model\City;
use App\Model\OtpVerification;
use App\Model\CustomerDetail;
use App\Model\LandingBanner;
use App\Model\Setting;
use App\Model\Product;
use App\Model\Department;
use App\Model\Service;

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
        $banners = LandingBanner::where('status','A')
        ->get();
        $titles = Setting::where('id','1')
        ->first();
        $products = Product::where('status','A')->where('ispopular','Y')
        ->get();
        $departments = Department::where('status','A')
        ->get();
        $services = Service::where('status','A')->where('popular','1')
        ->get();
       // print_r($products);exit;
        $data['banners'] = $banners;
        $data['titles']  = $titles;
        $data['popularproducts']  = $products;
        $data['departments']  = $departments;
        $data['services']  = $services;
        return view('frontend.home', $data);
    }
}

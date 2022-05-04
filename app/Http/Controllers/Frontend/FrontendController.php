<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    use AuthenticatesUsers;
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

    public function d_index()
    {
        $data['activemenu']['main'] = 'Home';

        return view('frontend.home', $data);
    }

    public function signin()
    {
        //dump($this->username());
        return view('frontend.login');
    }

    public function signin_post(Request $request)
    {
        $rules = array(
            'email' => 'required|email|exists:users',
            'password' => 'required'
        );

        $validator = \Validator::make($request->all(), $rules);
        if($validator->fails()){
            foreach($validator->errors()->messages() as $key => $value){
                return response()->json(['status' => $value[0]], 400);
            }
        }     
        
        $user = User::where('email', $request->email)->first();
        if(!$user){
            return response()->json(['status' => 'The email address you entered is invalid'], 400);
        }
        if($user->status == 0){
            return response()->json(['status' => 'Your account has been deactivated. To activate your account contact or write to us.'], 400);
        }
    
        if(\Auth::validate(['email' => $request->email, 'password' => $request->password],)){
            if(\Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role_id' => 1])){
                \Session::flash('success', 'Logedin Successfully');
                //return redirect()->route('frontend.d_index');
                return response()->json(['status' => 'Logedin Successfully'], 200);
            }
            elseif(\Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role_id' => 5])) 
            {
                \Session::flash('success', 'Customer Loggin in');
                return response()->json(['status' => 'Customer Logging in'], 200);
            }
            elseif(\Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role_id' => 6])) 
            {
                \Session::flash('success', 'Customer Loggin in');
                return response()->json(['status' => 'Seller Logging in'], 200);
            }
            else{
                return response()->json(['status' => 'Account may be blocked'], 400);
            }
        } else{
            return response()->json(['status' => 'Invalid credentials.'], 400);
        }
    }

    public function signup()
    {
        return view('frontend.sign-up');
    }

    public function signup_post()
    {

    }

    public function contact_us()
    {
        return view('frontend.contact-us');
    }

    public function contact_us_post()
    {

    }

}

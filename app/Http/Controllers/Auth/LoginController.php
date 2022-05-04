<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;

use App\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;


    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    //protected $redirectTo = '/home';
    protected $redirectTo = RouteServiceProvider::ADMIN_HOME;
    
    

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function redirectTo()
    {
        $user=Auth::user();

        if($user->account_type == 1){
            return '/admin';
        }else{
            return '/home';
        }

    }
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $post){
        $rules = array(
            'email' => 'required|email|exists:users',
            'password' => 'required'
        );

        $validator = \Validator::make($post->all(), $rules);
        if($validator->fails()){
            foreach($validator->errors()->messages() as $key => $value){
                return response()->json(['status' => $value[0]], 400);
            }
        }

        $user = User::where('email', $post->email)->first();
        if(!$user){
            return response()->json(['status' => 'The email address you entered is invalid'], 400);
        }

        if($user->status == 0){
            return response()->json(['status' => 'Your account has been deactivated. To activate your account contact or write to us.'], 400);
        }

        if(\Auth::validate(['email' => $post->email, 'password' => $post->password])){
            if(\Auth::attempt(['email' => $post->email, 'password' => $post->password])){
                \Session::flash('success', 'Logedin Successfully');
                return response()->json(['status' => 'Logedin Successfully'], 200);
            } else{
                return response()->json(['status' => 'Account may be blocked'], 400);
            }
        } else{
            return response()->json(['status' => 'Invalid credentials.'], 400);
        }
    }
}

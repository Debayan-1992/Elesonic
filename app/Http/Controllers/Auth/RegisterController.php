<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Model\Role;
use App\Model\OtpVerification;
use App\Model\DefaultPermission;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;

use Carbon\Carbon;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function register(Request $post){
        if(!$post->has('type')){
            $post['type'] = 'customer'; // For Default Registration
        }

        switch ($post->type) {
            case 'resendotp':
                if(!\Session::has('registerdata')){
                    return response()->json(['status' => 'Your registration data has expired. Please continue the process again'], 400);
                }

                $post['name'] = session('registerdata')['name'];
                $post['email'] = session('registerdata')['email'];
                $post['mobile'] = session('registerdata')['mobile'];
                $post['password'] = session('registerdata')['password'];
            break;

            case 'customer':
                if($post->has('otp')){
                    $rules = array(
                        'otp' => 'required|digits:6',
                    );
                } else{
                    $rules = array(
                        'name' => 'required',
                        'email' => 'required|unique:users',
                        'mobile' => 'required|unique:users|digits:10',
                        'password' => 'required|confirmed',
                        'terms_and_conditions' => 'accepted',
                    );
                }
            break;

            default:
                return response()->json(['status' => 'Invalid Request'], 400);
            break;
        }

        if(isset($rules)){
            $validator = \Validator::make($post->all(), $rules);
            if($validator->fails()){
                foreach($validator->errors()->messages() as $key => $value){
                    return response()->json(['status' => $value[0]], 400);
                }
            }
        }

        if(!$post->has('otp')){
            $post['otp'] = rand(111111, 999999);

            $body = "Dear $post->name, your verification code is $post->otp. Team ".config('app.name').".";
            if(\Myhelper::sms($post->mobile, $body)){
                OtpVerification::where('mobile', $post->mobile)->where('email', $post->email)->delete(); #delete prev records

                $action = OtpVerification::create([
                    'email' => $post->email,
                    'mobile' => $post->mobile,
                    'otp' => $post->otp,
                ]);

                if($action){
                    \Session::put('registerdata', $post->all());
                    return response()->json(['status' => 'An OTP has been successfully sent to your Mobile Number.', 'statuscode' => 'OTPSENT'], 200);
                } else{
                    return response()->json(['status' => 'Internal server error. Please try again later.'], 400);
                }
            } else{
                return response()->json(['status' => 'OTP cannot be sent. Please try again later.'], 400);
            }
        } else{
            if(!\Session::has('registerdata')){
                return response()->json(['status' => 'Your registration data has expired. Please continue the process again'], 400);
            }

            $post['name'] = session('registerdata')['name'];
            $post['email'] = session('registerdata')['email'];
            $post['mobile'] = session('registerdata')['mobile'];
            $post['password'] = session('registerdata')['password'];

            $verfication = OtpVerification::where('mobile', $post->mobile)->whereBetween('created_at', [Carbon::now()->subMinutes(15)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->first(); #valiate only otp with mobile number
            if($verfication){
                if(!\Hash::check($post->otp, $verfication->otp)){
                    return response()->json(['status' => "The otp you entered doesn't matched"], 400);
                }

                $post['mobile_verified_at'] = Carbon::now()->format('Y-m-d H:i:s');
                $verfication->delete();
            } else{
                return response()->json(['status' => 'The otp you entered is invalid or may have been expired'], 400);
            }
        }

        $role = Role::where('slug', $post->type)->first();

        $post['role_id'] = $role->id;
        $post['password'] = bcrypt($post->password);

        $rege = User::create($post->all());
        if($rege){
            $body = "Dear $rege->name, welcome to ".config('app.name').", Thanks for registering with us.";
            \Myhelper::sms($rege->mobile, $body);

            $subject = "Greetings from ".config('app.name');
            \Mail::send('mails.welcome', array('user' => $rege), function($msg) use ($subject, $rege) {
                $msg->from(config('mail.from.address'), config('mail.from.name'));
                $msg->to($rege->email)->subject($subject);
            });

            \Session::flush('registerdata');
            \Session::flash('success', 'Registered Successfully');

            $permissions = DefaultPermission::where('role_id', $role->id)->get();
            foreach ($permissions as $key => $value) {
                UserPermission::insert([
                    'user_id' => $rege->id,
                    'permission_id' => $value->permission_id
                ]);
            }

            \Auth::loginUsingId($rege->id);

            return response()->json(['status' => 'Registration successfully completed', 'statuscode' => 'TXN'], 200);
        } else{
            return response()->json(['status' => $role->name.' registration cannot be completed.'], 400);
        }
    }
}

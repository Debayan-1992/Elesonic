<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\Myhelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\UserCreateOTPMail;
use App\User;
use Carbon\Carbon;
use App\Model\City;
use App\Model\OtpVerification;
use App\Model\CustomerDetail;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Stripe;

class HomeController extends Controller
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

    public function landing()
    {
        //dd('dsfsdfs');
        return view('frontend.index');
    }

    public function welcome()//This method needs to go in a controller where their's is no middleware verification 
    {
        return view('frontend.welcome');
    }

    public function index()//This method needs to go in a controller where their's is no middleware verification
    {
        $data['activemenu']['main'] = 'dashboard';

        return view('dashboard.home', $data);
    }

    public function profile($id = 'none'){
        $data['activemenu']['main'] = 'profile';

        if($id == 'none'){
            $data['user'] = \Auth::user();
        } else{
            $data['user'] = User::findorfail(base64_decode($id));

            if(!\Myhelper::can('edit_'.$data['user']->role->slug)){
                abort(401);
            }
        }

        // dd($data['user']->toArray());

        $data['cities'] = City::all();
        return view('dashboard.profile', $data);
    }

    public function updateProfile(Request $post){
        $user_data = auth()->user();
        if(!$post->has('id')){
            $post['id'] = \Auth::id();
        }

        $userdata = User::findorfail($post->id);
        if($userdata->id != \Auth::id()){
            if(!\Myhelper::can('edit_'.$userdata->role->slug)){
                return response()->json(['status' => 'Permission Denied'], 400);
            }
        }

        switch ($post->type) {
            case 'basicdetails':
                $rules = [
                    'name' => 'required',
                    'email' => 'required|unique:users,email,'.$post->id,
                    'mobile' => 'nullable|digits:10|unique:users,mobile,'.$post->id,
                ];

                if($userdata->role->slug == 'customer'){
                    $rules['pancard'] = 'required|string|max:10|min:10|regex:^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$^|unique:users,pancard,'.$post->id;
                    $rules['gender'] = 'nullable|in:male,female,others';
                    $rules['date_of_birth'] = 'nullable';
                    $rules['pincode'] = 'nullable|digits:6';
                    $rules['city'] = 'nullable';
                }
            break;

            case 'profileimage':
                $rules = [
                    'profile_image' => 'required',
                ];
            break;

            case 'changepassword':
                $rules = [
                    'new_password' => 'required|confirmed',
                ];

                if($userdata->id == \Auth::id()){
                    $rules['current_password'] = 'required';
                }
            break;

            case 'verifymobile':
                if(!$post->has('otp') || !in_array($post->otp, ['send','resend'])){
                    $rules = [
                        'otp' => 'required|digits:6',
                    ];
                }
            break;

            case 'businessdoc':
                $rules = [
                    'pancard_image' => 'nullable',
                    'aadharcard_image' => 'nullable',
                    'cancelled_cheque_image' => 'nullable',
                ];
            break;

            default:
                return response()->json(['status' => 'Unsupported request'], 400);
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

        switch ($post->type) {
            case 'basicdetails':
                $update['name'] = $post->name;
                $update['email'] = $post->email;
                $update['mobile'] = $post->mobile;

                if($userdata->role->slug == 'customer'){
                    $update['pancard'] = $post->pancard;
                    $update['gender'] = $post->gender;
                    $update['dob'] = Carbon::parse($post->date_of_birth)->format('Y-m-d');
                    $update['pincode'] = $post->pincode;
                    $update['city_id'] = $post->city;
                }

                $action = User::where('id', $post->id)->update($update);

                if($action){
                    return response()->json(['status' => 'Profile updated successfully'], 200);
                } else{
                    return response()->json(['status' => 'Task failed. Please try again later'], 400);
                }
            break;

            case 'profileimage':
                $file = $post->file('profile_image');
                $filename = Carbon::now()->timestamp.'_'.$file->getClientOriginalName();

                if($userdata->profile_image != NULL){
                    $deletefile = 'uploads/profile/'.$userdata->profile_image;
                }

                //Resizing and compressing the image
                if(\Image::make($file->getRealPath())->resize(160, 160)->save('uploads/profile/'.$filename, 60)){
                    $update['profile_image'] = $filename;

                    if(isset($deletefile)){
                        \File::delete($deletefile);
                    }
                } else{
                    return response()->json(['status' => 'File cannot be saved to server.'], 400);
                }

                $action = User::where('id', $post->id)->update($update);

                if($action){
                    \Session::flash('success', 'Profile updated successfully.');
                    return response()->json(['status' => 'Profile updated successfully'], 200);
                } else{
                    return response()->json(['status' => 'Task failed. Please try again later'], 400);
                }
            break;

            case 'changepassword':
                if($userdata->id == \Auth::id()){
                    if(!\Hash::check($post->current_password, $userdata->password)){
                        return response()->json(['status' => 'Current password didnnot matched'], 400);
                    }
                }

                $update['password'] = bcrypt($post->new_password);

                $action = User::where('id', $post->id)->update($update);

                if($action){
                    return response()->json(['status' => 'Profile updated successfully'], 200);
                } else{
                    return response()->json(['status' => 'Task failed. Please try again later'], 400);
                }
            break;

            case 'verifymobile': 
                if(in_array($post->otp, ['send','resend'])){ //If pressed Send OTP button, coming from dashboard/profile.blade
                    //dd($post->all(), session()->all(), $user_data->email);
                    $post['otp'] = Myhelper::otp_get();
                    $mailFromId = config()->get('mail.from.address');
                    Mail::to($user_data->email)->send(new UserCreateOTPMail($user_data->name, $mailFromId, $post['otp']));
                    DB::table('otps')
                    ->insert([
                        'email' => $user_data->email,
                        'phone' => '',
                        'otp' => $post['otp'],
                        'is_active' => 1,
                        'expiry' => Carbon::now()->addMinutes(10)->format('Y-m-d h:i:s'), //Adding 10 mins as expiry
                        'created_at' => Carbon::now()->format('Y-m-d h:i:s'),
                    ]);
                    return response()->json(['status' => 'An OTP has been successfully sent to your Email.'], 200);
                    // $post['otp'] = rand(111111, 999999);
                    // $body = "Dear $userdata->name, your verification code is $post->otp. Team ".config('app.name').".";
                    // if(\Myhelper::sms($userdata->mobile, $body)){
                    //     OtpVerification::where('mobile', $userdata->mobile)->where('email', $userdata->email)->delete(); #delete prev records

                    //     $action = OtpVerification::create([
                    //         'email' => $userdata->email,
                    //         'mobile' => $userdata->mobile,
                    //         'otp' => $post->otp,
                    //     ]);

                    //     if($action){
                    //         \Session::put('registerdata', $post->all());
                    //         return response()->json(['status' => 'An OTP has been successfully sent to your Mobile Number.'], 200);
                    //     } else{
                    //         return response()->json(['status' => 'Internal server error. Please try again later.'], 400);
                    //     }
                    // } 
                   // else{return response()->json(['status' => 'OTP cannot be sent. Please try again later.'], 400);}
                } else{ //coming from layouts/app.blade, when submitting OTP
                    $record = DB::table('otps')
                        ->where('email', $user_data->email)
                        ->where('is_active', 1)
                        ->where('expiry', '>=', Carbon::now()->format('Y-m-d H:i:s'))
                        ->orderBy('created_at', 'desc')->first();
                        //dd(session()->all(), $user_data, $post->all(), $record, Carbon::now()->format('Y-m-d H:i:s'));
                    if($record != null && $record->otp == $post->otp)
                    {
                        DB::table('otps')
                        ->where('id', $record->id)
                        ->update([
                            'is_active' => 0,
                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        ]);

                        DB::table('users')
                        ->where('id', $user_data->id)
                        ->update([
                            'mobile_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        ]);

                        return response()->json(['status' => 'Mobile number verified successfully'], 200);
                    }
                    elseif($record == null){ //if expired
                        return response()->json(['status' => 'The otp you entered is invalid or may have been expired'], 400);
                    }
                        
                    
                    // $verfication = OtpVerification::where('mobile', $userdata->mobile)->whereBetween('created_at', [Carbon::now()->subMinutes(15)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->first(); #valiate only otp with mobile number
                    // if($verfication){
                    //     if(!\Hash::check($post->otp, $verfication->otp)){
                    //         return response()->json(['status' => "The otp you entered doesn't matched"], 400);
                    //     }

                    //     $update = array();
                    //     $update['mobile_verified_at'] = Carbon::now()->format('Y-m-d H:i:s');
                    //     $action = User::where('id', $post->id)->update($update);
                    //     if($action){
                    //         $verfication->delete();
                    //         \Session::flash('success', 'Mobile number verified successfully');
                    //         return response()->json(['status' => 'Mobile number verified successfully'], 200);
                    //     } else{
                    //         return response()->json(['status' => 'Task failed. Please try again later'], 400);
                    //     }
                    // } 
                    
                    //else{return response()->json(['status' => 'The otp you entered is invalid or may have been expired'], 400);}
                }
            break;

            case 'businessdoc':
                $update = array();
                $update['user_id'] = $userdata->id;

                if($post->file('pancard_image')){
                    $file = $post->file('pancard_image');
                    $filename = Carbon::now()->timestamp.'_'.$file->getClientOriginalName();

                    //Resizing and compressing the image
                    if(\Image::make($file->getRealPath())->save('uploads/profile/customers/'.$filename, 60)){
                        $update['pancardimage'] = $filename;
                    } else{
                        return response()->json(['status' => 'Pancard image cannot be saved to server.'], 400);
                    }
                }

                if($post->file('aadharcard_image')){
                    $file = $post->file('aadharcard_image');
                    $filename = Carbon::now()->timestamp.'_'.$file->getClientOriginalName();

                    //Resizing and compressing the image
                    if(\Image::make($file->getRealPath())->save('uploads/profile/customers/'.$filename, 60)){
                        $update['aadharcardimage'] = $filename;
                    } else{
                        return response()->json(['status' => 'Aadharcard image cannot be saved to server.'], 400);
                    }
                }

                if($post->file('cancelled_cheque_image')){
                    $file = $post->file('cancelled_cheque_image');
                    $filename = Carbon::now()->timestamp.'_'.$file->getClientOriginalName();

                    //Resizing and compressing the image
                    if(\Image::make($file->getRealPath())->save('uploads/profile/customers/'.$filename, 60)){
                        $update['cancelledchequeimage'] = $filename;
                    } else{
                        return response()->json(['status' => 'Cancelled cheque image cannot be saved to server.'], 400);
                    }
                }

                $action = CustomerDetail::updateorcreate(['user_id' => $userdata->id], $update);
                if($action){
                    \Session::flash('success', 'Profile updated successfully.');
                    return response()->json(['status' => 'Profile updated successfully.'], 200);
                } else{
                    return response()->json(['status' => 'Task failed. Please try again later.'], 400);
                }
            break;

            default:
                return response()->json(['status' => 'Unsupported request'], 400);
            break;
        }
    }

    public function validate_user(Request $request) //This method needs to go in a controller where their's is no middleware verification
    {
        $d_token = decrypt($request->_token);
        $user = User::where('email', $d_token)->firstOrFail();
        if($user->email_verified_at == null)
        {
            $user->email_verified_at = date("Y-m-d h:i:s");
            $user->save();
            if($user->role_id == 1 || $user->role_id == 2) //true or false
            {
                
                return redirect()->route('admin_login_form')->with('success', 'Success! User created');
                //dd('Email verified');
            }
            elseif($user->role_id == 5 || $user->role_id == 6) //true or false
            {
                if($user->role_id == 6) //If Seller
                {
                    try
                    {
                        $stripe = new Stripe\StripeClient(config()->get('stripe.secret_key'));
                        $stripe->accounts->create([
                        'type' => 'custom',
                        'country' => $user->country,
                        'email' => $user->email,
                        'capabilities' => [
                            'card_payments' => ['requested' => true],
                            'transfers' => ['requested' => true],
                        ],
                        ]);
                    }
                    catch(Stripe\Exception\CardException $e) {
                        // Since it's a decline, \Stripe\Exception\CardException will be caught
                        $err = '';
                        $err += 'Status is:' . $e->getHttpStatus() . '\n';
                        $err += 'Type is:' . $e->getError()->type . '\n';
                        $err += 'Code is:' . $e->getError()->code . '\n';
                        // param is '' in this case
                        $err += 'Param is:' . $e->getError()->param . '\n';
                        $err += 'Message is:' . $e->getError()->message . '\n';
                      } catch (Stripe\Exception\RateLimitException $e) {
                        $err = 'Too many requests made to the API too quickly';
                      } catch (Stripe\Exception\InvalidRequestException $e) {
                        $err = 'Invalid parameters were supplied to Stripe API';
                      } catch (Stripe\Exception\AuthenticationException $e) {
                        $err = 'Authentication with Stripe API failed';
                      } catch (Stripe\Exception\ApiConnectionException $e) {
                        $err = 'Network communication with Stripe failed';
                      } catch (Stripe\Exception\ApiErrorException $e) {
                        $err =  'Stripe API error';
                      } catch (Exception $e) {
                        $err =  'Something else happened, completely unrelated to Stripe';
                      }

                    User::where('id', $user->id)->update([
                        'stripe_account_id' => $user->stripe_account_id,
                        'updated_at' => date('Y-m-d h:i:s'),
                    ]);

                    return redirect()->route('login')->with('success', 'Success! Seller acoount created and Stripe account initialized');
                }
                
                return redirect()->route('login')->with('success', 'Success! User created'); //For customer
                //dd('Email verified');
            }
            
        }    
        else
            return redirect()->route('login')->with('success', 'Email was already verified');
        //dd('Email was already verified at '.$val->email_verified_at); //Paste view here
    }

    public function logout()//This method needs to go in a controller where their's is no middleware verification
    {
        
        \Auth::logout();
        return redirect()->route('login');
    }
}

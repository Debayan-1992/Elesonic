<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\Myhelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\FrontEndPasswordResetMail;
use App\Mail\OnlyTextMail;
use App\Mail\UserCreateMail;
use App\Mail\UserCreateOTPMail;
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
use App\Model\Brand;
use App\Model\Category as Categorys;
use App\Utility\CategoryUtility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use App\Model\Product_related_images;

class FrontendController extends Controller
{
    use AuthenticatesUsers;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     //$this->middleware('auth'); //For this function i function was not working
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
 
    public function __construct()
    {
        $this->middleware('frontendguest')->except('logout');
    }


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

    function product_list(Request $request,$slug,$id){
        $cat_id = base64_decode($id);
        $all_brand  = [];
        $parentCategory = Categorys::where('status','A')->where('parent_id',0)
        ->get();
        $category      = Categorys::select('parent_id','id', 'name', 'icon','slug','status')->where('id',@$cat_id)->where('status','A')->first();
        $subCategories = Categorys::select('parent_id','id', 'name', 'icon','slug','status')->where('parent_id',@$cat_id)->where('status','A')->get();
        if(!empty($subCategories)){
            $category_ids = CategoryUtility::children_ids($cat_id);
            $category_ids[] = $cat_id;
        }
        else{
            $category_ids[] = $cat_id;
        }
       
        $products  =  Product::whereIn('products.category_id', $category_ids)->with('category');
        $products  =  $products->where('products.status','A')->orderBy('id','DESC');
        $products  =  $products->paginate(10);

        $productsCount  =  Product::whereIn('products.category_id', $category_ids)->with('category');
        $productsCount  =  $productsCount->where('products.status','A')->orderBy('id','DESC');
        $productsCount  =  $productsCount->get();
        if(!empty($productsCount)){
            foreach ($productsCount as $key => $product){
            if($product->brand_id != null) {
                    $brandName = Brand::where('id',$product->brand_id)->first();
                    if(!in_array($brandName->id.'- '.$brandName->name, $all_brand)){
                        array_push($all_brand,$brandName->id.'- '.$brandName->name);
                    }
                }
            }
        }
        $data['subCategories']          = $subCategories;
        $data['products']               = $products;
        $data['category']               = $category;
        $data['cat_id']                 = $cat_id;
        $data['parentCategory']         = $parentCategory;
        $data['productsCount']          = $productsCount;
        $data['filterBrands']           = $all_brand;
        return view('frontend.product_list',$data);
    }
    function product_details(Request $request,$slug,$id){
        $pro_id = base64_decode($id);
        $productsCount              =  Product::where('products.id', $pro_id);
        $productsCount              =  $productsCount->where('products.status','A');
        $productsCount              =  $productsCount->first();
        $data['product']            =  $productsCount;
        $relatedImage = Product_related_images::where('product_id',$pro_id)
        ->get();
        $relatedProducts = Product::where('products.status','A')->where('products.category_id',$productsCount->category_id)->where('products.id','!=',$pro_id)->with('category')
        ->get();
        
        $data['relatedImage']       = $relatedImage;
        $data['relatedProducts']    = $relatedProducts;
        return view('frontend.product_details',$data);
    }

    function get_filter_data(Request $request){
        $cat_id  = $request->catId;
        $brandId = $request->brandId;
        $priceShort = $request->priceShort;
        $max_price =  $request->max_price;
        $min_price =  $request->min_price;
        $subCategories = Categorys::select('parent_id','id', 'name', 'icon','slug','status')->where('parent_id',@$cat_id)->where('status','A')->get();
        if(!empty($subCategories)){
            $category_ids = CategoryUtility::children_ids($cat_id);
            $category_ids[] = $cat_id;
        }
        else{
            $category_ids[] = $cat_id;
        }
       
        $products  =  Product::whereIn('products.category_id', $category_ids)->with('category');
        if(!empty($brandId)){
            $products  =  $products->whereIn('products.brand_id',$brandId);
        }
        if($priceShort == "h_t_l"){
            $products  =  $products->orderBy('products.unit_price','DESC');
        }
        if($priceShort == "l_t_h"){
            $products  =  $products->orderBy('products.unit_price','ASC');
        }
        if($max_price != "" && $min_price != ""){
            $products  =  $products->where('products.unit_price','<=',$max_price);
            $products  =  $products->where('products.unit_price','>=',$min_price);
        }
        $products  =  $products->where('products.status','A')->orderBy('id','DESC');
        $products  =  $products->get();
        $data['products']               = $products;
        return view('frontend.filter_data',$data);
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
                \Session::flash('success', 'customer');
                return response()->json(['status' => 'Customer Logging in', 'user_type' => 'customer'], 200);
            }
            elseif(\Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role_id' => 6])) 
            {
                \Session::flash('success', 'seller');
                return response()->json(['status' => 'Seller Logging in', 'user_type' => 'seller'], 200);
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

    public function signup_post(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email', //Meaning unique in users table and email column
            'password' => 'required|string|min:6|confirmed',
            'mobile' => 'required|min:10|unique:users,mobile', //Meaning unique phone no. in users table
            'role_id' => 'required',
        ]);

        if ($validator->fails()) {
            
            $arr = array('status' => implode(",",$validator->errors()->all()));
            return response()->json($arr, 400);
        }

        $request->request->add(['status'=>1]);
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $val = User::create($input);
        
        $post['otp'] = Myhelper::otp_get();
        $mailFromId = config()->get('mail.from.address');
        Mail::to($request->email)->send(new UserCreateOTPMail($request->name, $mailFromId, $post['otp']));
        DB::table('otps')
        ->insert([
            'email' => $request->email,
            'phone' => $request->mobile,
            'otp' => $post['otp'],
            'is_active' => 1,
            'expiry' => Carbon::now()->addMinutes(10)->format('Y-m-d h:i:s'), //Adding 10 mins as expiry
            'created_at' => Carbon::now()->format('Y-m-d h:i:s'),
        ]);

        Mail::to($request->email)->send(new UserCreateMail($request->name, $request->email, $request->password));
        
        return response()->json(['status' => 'OTP and verification mail has been successfully sent to your Email.'], 200);
    }

    public function show_passwordreset_form()
    {
        return view('frontend.password_reset');
    }

    public function passwordreset_post(Request $request)
    {
        
        $user = DB::table('users')->where('email', $request->email)->whereIn('role_id', [5,6])->first();
        //Check if the user exists
        if (empty($user)) {
            return response()->json(['status' => 'No Customer/Seller records found with that email'], 400);
        }

        //Create Password Reset Token
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => str_random(60),
            'created_at' => Carbon::now()
        ]);

        //Get the token just created above
        $tokenData = DB::table('password_resets')
        ->where('email', $request->email)->first();

        if ($this->sendResetEmail($request->email, $tokenData->token)) {
            return response()->json(['status' => 'Email reset link has been sent'], 200);
        } else {
            return response()->json(['status' => 'A Network Error occurred. Please try again.'], 400);
        }
    }

    private function sendResetEmail($email, $token)
    {
        //Retrieve the user from the database
        $user = DB::table('users')->where('email', $email)->select('name', 'email')->first();
        //Generate, the password reset link. The token generated is embedded in the link
        $link = URL::to("password-reset-form/?token=".$token."&email=".$user->email);
       
        try 
        {
            //Here send the link with CURL with an external email API 
            $mailFromId = config()->get('mail.from.address');
            Mail::to($user->email)->send(new FrontEndPasswordResetMail($user->name, $mailFromId, $link));
            return response()->json(['status' => 'Email reset link has been sent'], 200);
        } catch (\Exception $e) 
        {
            return false;
        }
    }

    public function pass_reset_form_show()
    {
        return view('frontend.password_reset_form')->with(['email'=>request()->email, 'token'=>request()->token]);
    }

    public function resetPassword(Request $request)
    {
        //Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed',
            'token' => 'required' ]);

        //check if payload is valid before moving on
        
        if ($validator->fails()) {
            return response()->json(['status' => 'Password reset form fields not filled properly'], 400);
        }

        $password = $request->password;

        // Validate the token
        $tokenData = DB::table('password_resets')
        ->where('token', $request->token)->first();
        // Redirect the user back to the password reset request form if the token is invalid
        // if (!$tokenData) return view('auth.passwords.email');

        $user = User::where('email', $tokenData->email)->first();
        // Redirect the user back if the email is invalid
        if (!$user) return response()->json(['status' => 'Email not found'], 400);
        //Hash and update the new password
        
        $user->password = Hash::make($password);
        $user->update(); //or $user->save();

        // //login the user immediately they change password successfully
        // Auth::login($user);

        //Delete the token
        DB::table('password_resets')->where('email', $user->email)
        ->delete();

        //Send Email Reset Success Email
        $txt = 'You have successfully reset your password';
        $subject = 'Successfully updated Password - Elesonic';
        $mailFromId = config()->get('mail.from.address');
        Mail::to($user->email)->send(new OnlyTextMail($user->name, $mailFromId, $txt, $subject));
        return redirect()->route('login');
        // if ($this->sendSuccessEmail($tokenData->email)) {
        //     return response()->route('login');
        // } else {
        //     return response()->json(['status' => 'Network error occured'], 400);
        // }
    
    }
}

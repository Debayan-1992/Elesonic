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
use App\Model\Brand;
use App\Model\Category as Categorys;
use App\Utility\CategoryUtility;

use App\Model\Product_related_images;
use DB;
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

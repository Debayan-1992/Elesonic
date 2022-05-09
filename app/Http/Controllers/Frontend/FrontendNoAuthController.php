<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Role;
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
use App\Model\CmsContent;
use App\Model\FaqContent;
use App\Model\Service_booking;
use App\Model\Product_related_images;
use App\Model\Category as Categorys;
use App\Utility\CategoryUtility;


use App\Mail\OnlyTextMail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class FrontendNoAuthController extends Controller
{
    //After login for seller, customer this controller should be hit if hitting FrontendController then it'll keep looping

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

    function product_list(Request $request,$slug){
        $catSlug = Categorys::select('parent_id','id', 'name', 'icon','slug','status')->where('slug',$slug)->where('status','A')->first();
        if(empty($catSlug)){
            return redirect()->route('index');
        }
        $cat_id = $catSlug->id;
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
    function product_details(Request $request,$slug){
        $proSlug = Product::select('slug','status','id')->where('slug',$slug)->where('status','A')->first();
       
        if(empty($proSlug)){
            return redirect()->route('index');
        }
        $pro_id = $proSlug->id;
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

    function services(){
        $service = Service::where('status','A')
        ->get();
        $data['cities'] = City::all();
        $data['services'] = $service;
        return view('frontend.services',$data);
    }

    function servicebook(Request $request){
        $Service_booking = new Service_booking;
        $serviceId = $request->serviceId;
        $Service_booking->name = $request->name;
        $Service_booking->phone = $request->mobile;
        $Service_booking->email = $request->email;
        $Service_booking->city = $request->city;
        $Service_booking->information = $request->information;
        $Service_booking->service_id = $serviceId;
        $Service_booking->save();
        $service = Service::where('id',$serviceId)
        ->first();
        $servicename = $service->name;
        $txt = 'Request has been sent successfully for '.$servicename.' ,We will reach out to you as
        soon as we can';
        $subject = 'Service Booking Quote - Elesonic';
        $mailFromId = config()->get('mail.from.address');
        Mail::to($request->email)->send(new OnlyTextMail($request->name, $mailFromId, $txt, $subject));
        return redirect()->route('services')->with('message', 'Request has been sent successfully.');
    }

    function content_details(Request $request,$slug){
        $CmsContent = CmsContent::where('slug',$slug)->first();
        $data['cmsContent'] = $CmsContent;
        return view('frontend.cms',$data);
    }
    function faq(Request $request){
        $FaqContent = FaqContent::where('status','1')->get();
        $data['faqContent'] = $FaqContent;
        return view('frontend.faq',$data);
    }

    public function dashboard()
    {
        $user = auth()->user();
        return view('frontend.dashboard.dashboard')->with(['user'=>$user]);
    }

    public function password_change_form()
    {
        $user = auth()->user();
        return view('frontend.dashboard.password_update_form')->with(['user'=>$user]);
    }

    public function password_change_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
            //return redirect()->back()->with('message', 'Password must have a minimum length of 6 and both passwords should match.');
        }
        User::where('id', decrypt($request->user_id))->update([
            'password' => Hash::make($request->password),
        ]);
        if(auth()->user()->role_id == Role::IS_CUSTOMER)
            return redirect()->route('customer.customer_dashboard')->with('message', 'Password updated successfully.');
        elseif(auth()->user()->role_id == Role::IS_SELLER)
            return redirect()->route('seller.seller_dashboard')->with('message', 'Password updated successfully.');
    }

    public function my_account_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
            //return redirect()->back()->with('message', 'Password must have a minimum length of 6 and both passwords should match.');
        }
        User::where('id', decrypt($request->user_id))->update([
            'name' => $request->name,
        ]);
        if(auth()->user()->role_id == Role::IS_CUSTOMER)
            return redirect()->route('customer.customer_dashboard')->with('message', 'Account details updated successfully.');
        elseif(auth()->user()->role_id == Role::IS_SELLER)
            return redirect()->route('seller.seller_dashboard')->with('message', 'Account details updated successfully.');
    }

    public function contact_us()
    {
        return view('frontend.contact-us');
    }

    public function contact_us_post()
    {

    }
}

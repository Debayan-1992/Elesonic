<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Countries;
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
use App\Model\Blog;
use App\Model\Service;
use App\Model\Brand;
use App\Model\CmsContent;
use App\Model\FaqContent;
use App\Model\Cart_item;
use App\Model\Delivery_address;
use App\Model\State;
use App\Model\Order;
use App\Model\Order_details;
use App\Model\Service_booking;
use App\Model\Product_related_images;
use App\Model\Category as Categorys;
use App\Utility\CategoryUtility;
use PDF;
use Excel;

use App\Model\Subscribers;
use App\Mail\OnlyTextMail;
use App\Models\Setting as ModelsSetting;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderGenerationMail;
use App\Exports\OrderReportsExportByRange;
use Illuminate\Support\Facades\URL;
use Stripe;

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
        $bestproducts = Product::where('status','A')->where('isbest','Y')->with('category')
        ->get();
      
        $departments = Department::where('status','A')
        ->get();
        $services = Service::where('status','A')->where('popular','1')
        ->get();
       // print_r($products);exit;
        $data['banners'] = $banners;
        $data['titles']  = $titles;
        $data['popularproducts']  = $products;
        $data['bestproducts']  = $bestproducts;
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
        if(\Auth::check() == false){
            $user_id= "";
            $role_id= "";
            $userdata = [];
         }else{
            $user_id= auth()->user()->id;
            $role_id= auth()->user()->role_id;
         }
         $data['role_id']       = $role_id;
       
        return view('frontend.product_details',$data);
    }

    function get_search_data(Request $request){
        $value    = $request->val;
        $category = $request->cat_id;
        $pro_arr=array();
        $pro = Product::where('status', 'A')->where('name','LIKE','%'.$value.'%')->skip(0)->take(10)->get();
      
        $arr=$pro;
        if(!empty($arr)){
            foreach($arr as $row){
            $pro_arr[]=$row->name;
            }
        }
        echo json_encode($pro_arr);
    }

    function search_product(Request $request){
        $search = $request->search;
        $proSlug = Product::select('slug','status','id','name')->where('name',$search)->where('status','A')->first();
       
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
        if(\Auth::check() == false){
            $user_id= "";
            $role_id= "";
            $userdata = [];
         }else{
            $user_id= auth()->user()->id;
            $role_id= auth()->user()->role_id;
         }
         $data['role_id']       = $role_id;
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

    function departments(){
       
        $departments = Department::where('status','A')
        ->get();
        $data['departments'] = $departments;
        return view('frontend.departments',$data);
    }
    function blogs(){
       
        $blogs = Blog::where('status','1')
        ->get();
        $data['blogs'] = $blogs;
        return view('frontend.blogs',$data);
    }

   

    function servicebook(Request $request){
        $user_id= auth()->user()->id;
        $user = User::where('id',$user_id)
        ->first();
        $Service_booking = new Service_booking;
        $serviceId = $request->serviceId;
        $Service_booking->name = $user->name;
        $Service_booking->phone = $user->mobile;
        $Service_booking->email = $user->email;
       // $Service_booking->city = $request->city;
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
        Mail::to($user->email)->send(new OnlyTextMail($request->name, $mailFromId, $txt, $subject));
        $res = 1;
        echo json_encode($res);
        //return redirect()->route('services')->with('message', 'Request has been sent successfully.');
    }

    function subscribeEmail(Request $request){
        $Subscribers = new Subscribers;
        $chkEmail = Subscribers::select('email')->where('email',$request->subscribermail)->where('status','A')->count();
       
        if($chkEmail > 0){
            return redirect()->route('index')->with('subsmessage', 'Already subscribed.');
        }
        $Subscribers->email = $request->subscribermail;
        $Subscribers->save();
        $txt = 'E-mail has been subscribed successfully';
        $subject = 'Subscription E-mail - Elesonic';
        $mailFromId = config()->get('mail.from.address');
        Mail::to($request->subscribermail)->send(new OnlyTextMail("", $mailFromId, $txt, $subject));
        return redirect()->route('index')->with('subsmessage', 'Successfully subscribed.');
    }

    function buy_now(Request $request){

        $product_id = $request->Id;

        $product_quantity = $request->product_quantity;

        $cart_sess_id=\Session::get('cart_session_id');

        if(\Auth::check() == false){
            $user_id= "";
        }else{
            $user_id= auth()->user()->id;
        }
        $proQty = Product::select('unit_price','purchase_price','discount','slug','status','id','name','quantity')->where('id',$product_id)->where('status','A')->first();

        if($proQty->quantity == 0) {
            $res = 0;
            $mycartsItem = array();
        }
        else{
            if(@$cart_sess_id=='') {
                $cart_id = 'Elesonic- '.time().rand(0000,9999);
                $cart_sess = \Session::put('cart_session_id', $cart_id);
                $cart_sess_id= \Session::get('cart_session_id');
            }
            else{
                $cart_sess_id= \Session::get('cart_session_id');
            }
            $product_price = $proQty->purchase_price;

            $product_discount = $proQty->discount;

            $product_net_price = $proQty->unit_price;

            $product_net_quantity =$proQty->quantity;
            if($user_id==''){
                $carts= Cart_item::where('cart_session_id',$cart_sess_id)->where('cart_item_id',$product_id)->count();
                if($carts==0) {
                $cartItem = new Cart_item;
                $cartItem->cart_session_id = $cart_sess_id;
                $cartItem->cart_item_id    = $product_id;
                $cartItem->cart_item_qty   = $product_quantity;
                $cartItem->cart_item_price = $product_price;
                $cartItem->cart_item_price_disc = $product_discount;
                $cartItem->cart_item_net_price = $product_net_price;
                $cartItem->save();
            }
            else{
                $cartsItem= Cart_item::where('cart_session_id',$cart_sess_id)->where('cart_item_id',$product_id)->first();
                $p_qty=$cartsItem->cart_item_qty;
                $new_p_qty=$product_quantity+$p_qty;
                $cart_data= array(

                            'cart_session_id'=>$cart_sess_id,                              

                            'cart_item_qty'=>$new_p_qty,                                

                            'cart_item_price'=>$product_price,

                            'cart_item_price_disc'=>$product_discount,

                            'cart_item_net_price'=>$product_net_price,

                        );
                    DB::table('cart_item')
                    ->where('cart_item_id', $product_id)  
                    ->where('cart_session_id', $cart_sess_id)  
                    ->update($cart_data); 
                    }
                }
                else{
                $carts= Cart_item::where('user_id',$user_id)->where('cart_item_id',$product_id)->count();
                    if($carts==0){
                        $cartItem = new Cart_item;
                        $cartItem->user_id = $user_id;
                        $cartItem->cart_item_id    = $product_id;
                        $cartItem->cart_item_qty   = $product_quantity;
                        $cartItem->cart_item_price = $product_price;
                        $cartItem->cart_item_price_disc = $product_discount;
                        $cartItem->cart_item_net_price = $product_net_price;
                        $cartItem->save();
                }
                else{
                    $cartsItem= Cart_item::where('user_id',$user_id)->where('cart_item_id',$product_id)->first();
                    $p_qty=$cartsItem->cart_item_qty;
                    $new_p_qty=$product_quantity+$p_qty;
                    $cart_data= array(       

                        'cart_item_id'=>$product_id,

                        'cart_item_qty'=>$new_p_qty,                                

                        'cart_item_price'=>$product_price,

                        'cart_item_price_disc'=>$product_discount,

                        'cart_item_net_price'=>$product_net_price,

                    );

                    DB::table('cart_item')
                    ->where('cart_item_id', $product_id)  
                    ->where('user_id', $user_id)  
                    ->update($cart_data);
                    }
                }
                if($user_id==''){
                    $mycartsItem= Cart_item::where('cart_session_id',$cart_sess_id)->get();
                    $totalQty = 0;
                    foreach($mycartsItem as $row){
                        $totalQty = $totalQty+$row->cart_item_qty;
                    }
                }
                else{
                    $mycartsItem= Cart_item::where('user_id',$user_id)->get();
                    $totalQty = 0;
                    foreach($mycartsItem as $row) {
                        $totalQty = $totalQty+$row->cart_item_qty;
                    }
                }
                $res = 1;
            }
        echo json_encode($totalQty);
    }

    function add_cart(Request $request){

        $product_id = $request->Id;

        $product_quantity = $request->product_quantity;

        $cart_sess_id=\Session::get('cart_session_id');

        if(\Auth::check() == false){
            $user_id= "";
        }else{
            $user_id= auth()->user()->id;
        }
        $proQty = Product::select('unit_price','purchase_price','discount','slug','status','id','name','quantity')->where('id',$product_id)->where('status','A')->first();

        if($proQty->quantity == 0) {
            $res = 0;
            $mycartsItem = array();
        }
        else{
            if(@$cart_sess_id=='') {
                $cart_id = 'Elesonic- '.time().rand(0000,9999);
                $cart_sess = \Session::put('cart_session_id', $cart_id);
                $cart_sess_id= \Session::get('cart_session_id');
            }
            else{
                $cart_sess_id= \Session::get('cart_session_id');
            }
            $product_price = $proQty->purchase_price;

            $product_discount = $proQty->discount;

            $product_net_price = $proQty->unit_price;

            $product_net_quantity =$proQty->quantity;
            if($user_id==''){
                $carts= Cart_item::where('cart_session_id',$cart_sess_id)->where('cart_item_id',$product_id)->count();
                if($carts==0) {
                $cartItem = new Cart_item;
                $cartItem->cart_session_id = $cart_sess_id;
                $cartItem->cart_item_id    = $product_id;
                $cartItem->cart_item_qty   = $product_quantity;
                $cartItem->cart_item_price = $product_price;
                $cartItem->cart_item_price_disc = $product_discount;
                $cartItem->cart_item_net_price = $product_net_price;
                $cartItem->save();
            }
            else{
                $cartsItem= Cart_item::where('cart_session_id',$cart_sess_id)->where('cart_item_id',$product_id)->first();
                $p_qty=$cartsItem->cart_item_qty;
                $new_p_qty=$product_quantity+$p_qty;
                $cart_data= array(

                            'cart_session_id'=>$cart_sess_id,                              

                            'cart_item_qty'=>$new_p_qty,                                

                            'cart_item_price'=>$product_price,

                            'cart_item_price_disc'=>$product_discount,

                            'cart_item_net_price'=>$product_net_price,

                        );
                    DB::table('cart_item')
                    ->where('cart_item_id', $product_id)  
                    ->where('cart_session_id', $cart_sess_id)  
                    ->update($cart_data); 
                    }
                }
                else{
                $carts= Cart_item::where('user_id',$user_id)->where('cart_item_id',$product_id)->count();
                    if($carts==0){
                        $cartItem = new Cart_item;
                        $cartItem->user_id = $user_id;
                        $cartItem->cart_item_id    = $product_id;
                        $cartItem->cart_item_qty   = $product_quantity;
                        $cartItem->cart_item_price = $product_price;
                        $cartItem->cart_item_price_disc = $product_discount;
                        $cartItem->cart_item_net_price = $product_net_price;
                        $cartItem->save();
                }
                else{
                    $cartsItem= Cart_item::where('user_id',$user_id)->where('cart_item_id',$product_id)->first();
                    $p_qty=$cartsItem->cart_item_qty;
                    $new_p_qty=$product_quantity+$p_qty;
                    $cart_data= array(       

                        'cart_item_id'=>$product_id,

                        'cart_item_qty'=>$new_p_qty,                                

                        'cart_item_price'=>$product_price,

                        'cart_item_price_disc'=>$product_discount,

                        'cart_item_net_price'=>$product_net_price,

                    );

                    DB::table('cart_item')
                    ->where('cart_item_id', $product_id)  
                    ->where('user_id', $user_id)  
                    ->update($cart_data);
                    }
                }
                if($user_id==''){
                    $mycartsItem= Cart_item::where('cart_session_id',$cart_sess_id)->get();
                    $totalQty = 0;
                    foreach($mycartsItem as $row){
                        $totalQty = $totalQty+$row->cart_item_qty;
                    }
                }
                else{
                    $mycartsItem= Cart_item::where('user_id',$user_id)->get();
                    $totalQty = 0;
                    foreach($mycartsItem as $row) {
                        $totalQty = $totalQty+$row->cart_item_qty;
                    }
                }
                $res = 1;
            }
        echo json_encode($totalQty);
    }
    function carts(Request $request){
        $user_id= auth()->user()->id;
        $mycartsItem= Cart_item::where('cart_item.user_id',$user_id)->leftjoin('products','products.id','=','cart_item.cart_item_id')->get();
        if(count($mycartsItem) == 0){
            return redirect()->route('index');
        }
        $data['cartDetails']=$mycartsItem;
        return view('frontend.carts',$data);
    }
    function update_product_cart(Request $request){
        $aid = $request->aid;
        $qty = $request->qty;
        $mycartsItem= Cart_item::where('cart_id',$aid)->first();
        $existingQty  =  $mycartsItem->cart_item_qty;
        $newQty       =  $qty;
        $cart_data = array('cart_item_qty'=>$newQty);
        DB::table('cart_item')
        ->where('cart_id', $aid)  
        ->update($cart_data);
        $res = 1;
        echo json_encode($res);
    }
    function del_product_cart(Request $request){
        $user_id= auth()->user()->id;
        $aid = $request->aid;
        $res1=Cart_item::where('cart_id',$aid)->delete();
        $countPro=Cart_item::where('user_id',$user_id)->count();
        $res = $countPro;
        echo json_encode($res);
    }

    function address(){
        $user_id= auth()->user()->id;
        $user = auth()->user();
        $delivery_address = Delivery_address::where('user_id',$user_id)->get();
        $usercountry = User::where('id',$user_id)->first();
        $country = Countries::where('sortName',$usercountry->country)->first();
        $state = State::where('countryId',$country->id)->get();
        $data['shippingAddress'] = $delivery_address;
        $data['user'] = $user;
        $data['state'] = $state;
        return view('frontend.delivery_address',$data);
    }
    function get_city(Request $request){
        $stateId= $request->state;
        $city = City::where('state_id',$stateId)->get();
        echo json_encode($city);
    }
    function makeDefault(Request $request){
        $addressId = $request->addressId;
        $user_id= auth()->user()->id;
        $dataN = array('is_default'=>'No');
        DB::table('delivery_address')
                    ->where('user_id', $user_id)   
                    ->update($dataN); 
        $dataY = array('is_default'=>'Yes');
        DB::table('delivery_address')
                    ->where('address_id', $addressId)   
                    ->update($dataY); 
        $res = 1;
        echo json_encode($res);
    }
    function makeDelete(Request $request){
        $addressId = $request->addressId;
        $user_id= auth()->user()->id;
        $res1=Delivery_address::where('address_id',$addressId)->delete();
        $res = 1;
        echo json_encode($res);
    }
    function addaddress(Request $request){
        $delivery_address = New Delivery_address;
        $user_id= auth()->user()->id;
        $cityName = City::where('id',$request->delcity)->first();
        $stateName = State::where('id',$request->state)->first();
        $delivery_address->user_first_name = $request->first_name;
        $delivery_address->user_id=$user_id;
        $delivery_address->user_last_name = $request->last_name;
        $delivery_address->user_phone_no = $request->phone;
        $delivery_address->user_email = $request->email;
        $delivery_address->user_state = $stateName->name;
        $delivery_address->user_city = $cityName->name;
        $delivery_address->user_pincode = $request->postcode;
        $delivery_address->user_address = $request->address;
        $delivery_address->save();
        return redirect()->route('customer.address')->with('message', 'Address saved successfully.');
    }
    function addaddressdef(Request $request){
        $delivery_address = New Delivery_address;
        $user_id= auth()->user()->id;
        $dataN = array('is_default'=>'No');
        DB::table('delivery_address')
                    ->where('user_id', $user_id)   
                    ->update($dataN); 
        $cityName = City::where('id',$request->delcity)->first();
        $stateName = State::where('id',$request->state)->first();
        $delivery_address->user_first_name = $request->first_name;
        $delivery_address->user_id=$user_id;
        $delivery_address->user_last_name = $request->last_name;
        $delivery_address->user_phone_no = $request->phone;
        $delivery_address->user_email = $request->email;
        $delivery_address->user_state = $stateName->name;
        $delivery_address->user_city = $cityName->name;
        $delivery_address->user_pincode = $request->postcode;
        $delivery_address->user_address = $request->address;
        $delivery_address->is_default = 'Yes';
        $delivery_address->save();
        return redirect()->route('customer.confirm-order')->with('message', 'Address saved successfully.');
    }
    function confirm_order(Request $request){
        $user_id= auth()->user()->id;
        $user = auth()->user();
        $delivery_address = Delivery_address::where('user_id',$user_id)->get();
        $usercountry = User::where('id',$user_id)->first();
        $country = Countries::where('sortName',$usercountry->country)->first();
        $state = State::where('countryId',$country->id)->get();
        $member_dtl = User::where('id',$user_id)->first();
        $mystate = State::where('id',$member_dtl->state_id)->first();
        $mycity = City::where('id',$member_dtl->city_id)->first();
        $data['shippingAddress'] = $delivery_address;
        $data['member_dtl']      = $member_dtl;
        $data['mystate']         = $mystate;
        $data['mycity']          = $mycity;
        $data['state'] = $state;
        return view('frontend.confirm_order',$data);
    }

    function place_order(Request $request){
        $user_id= auth()->user()->id;
        $mycartsItem= Cart_item::where('cart_item.user_id',$user_id)->leftjoin('products','products.id','=','cart_item.cart_item_id')->get();
        $delivery_address = Delivery_address::where('user_id',$user_id)->where('is_default','Yes')->first();
        $data['cartDetails']=$mycartsItem;
        $data['shippingAddress']=$delivery_address;

       
        $subTotal = 0;
        $setting = Setting::where('id',1)->first();
        $orderBelow = $setting->order_amount;
        $charges    = $setting->charges;
        foreach($mycartsItem as $row){
            $subTotal = $subTotal + ($row->cart_item_qty * $row->cart_item_net_price);
        }

        if($subTotal < $orderBelow){
            $shippingCharges = $charges;
            $subTotal = $subTotal + $shippingCharges;
        }else{
            $shippingCharges = 0;
            $subTotal = $subTotal;
        }
        $data['subTotal']=$subTotal;
        $data['shippingCharges']=$shippingCharges;
        if(empty($delivery_address)){
            return redirect()->route('customer.confirm-order')->with('message', 'Add shipping address');
        }
        return view('frontend.place_order',$data);
    }
    function order_now(Request $request){

     
        if($request->radio_button)
        {
            $status = $this->order_online();
        }
        $user_id= auth()->user()->id;
        $mycartsItem      = Cart_item::where('cart_item.user_id',$user_id)->leftjoin('products','products.id','=','cart_item.cart_item_id')->get();
        $delivery_address = Delivery_address::where('user_id',$user_id)->where('is_default','Yes')->first();
        $billing_address = User::where('id',$user_id)->first();
        $subTotal = 0;
        $setting = Setting::where('id',1)->first();
        $orderBelow = $setting->order_amount;
        $charges    = $setting->charges;
        foreach($mycartsItem as $row){
            $subTotal = $subTotal + ($row->cart_item_qty * $row->cart_item_net_price);
        }

        if($subTotal < $orderBelow){
            $shippingCharges = $charges;
            $subTotal = $subTotal + $shippingCharges;
        }else{
            $shippingCharges = 0;
            $subTotal = $subTotal;
        }

        $order = New Order;
        $ordrId= str_replace(".", "", microtime()).rand(000,999);
        $order_unique_id = str_replace(" ", "-", $ordrId);
        $order->order_unique_id = $order_unique_id;
        $order->shipping_charge = $shippingCharges;
        
        $order->order_customer_id = $user_id;
        $order->order_total_price = $subTotal;
        $order->payment_mode = 'cod';
        $order->created_at = date('Y-m-d H:i:s');
        $order->payment_status = 'Paid';
        $order->orderaddress = json_encode($delivery_address);
        $order->save();

        foreach($mycartsItem as $row){
            $order_details = New Order_details;
            $commPercentage = $setting->commission;
            

            if($row->type == "old"){
                $admin_commission = ($row->cart_item_net_price * $commPercentage)/100;
                $seller_commission = ($row->cart_item_net_price - $admin_commission);
            }else{
                $admin_commission = 0;
                $seller_commission = 0;
            }

            $order_details->order_id = $order->id;
            $order_details->order_product_id = $row->cart_item_id;
            $order_details->product_seller_id = $row->user_id;
            $order_details->cart_item_pro_qty = $row->cart_item_qty;
            $order_details->cart_item_price = $row->cart_item_price;
            $order_details->cart_item_price_disc = $row->cart_item_price_disc;
            $order_details->cart_item_net_price = $row->cart_item_net_price;
            $order_details->created_at =  date('Y-m-d H:i:s');

            $order_details->admin_commission = $admin_commission;
            $order_details->seller_commission = $seller_commission;
            $order_details->save();

            
            $existingQTY = $row->quantity;
            $newQTY = $existingQTY - $row->cart_item_qty;
            $dataQTY = array('quantity'=>$newQTY);
            DB::table('products')
                    ->where('id', $row->cart_item_id)   
                    ->update($dataQTY); 

        }
        $data['orderCode'] = $order_unique_id;
        $pdf = \PDF::loadView('frontend/orderpdf',$data);
        $path = public_path('uploads/order/');
        $fileName = 'Order-'.$order_unique_id.'.pdf';
        $pdf->save($path.'/'.$fileName);

        $code = $order_unique_id;
        $subject = 'Order Place E-mail - Elesonic';
        $mailFromId = config()->get('mail.from.address');
        Mail::to($billing_address->email)->send(new OrderGenerationMail($mailFromId, $subject,$code,$fileName));
        Cart_item::where('user_id',$user_id)->delete();
        $res = 1;
        echo json_encode($res);
    }

    public function order_online() //For transfer ring to sub account
    {

        $transfer = \Stripe\Transfer::create([
            'amount' => 7000,
            'currency' => 'inr',
            'destination' => '{{CONNECTED_STRIPE_ACCOUNT_ID}}',
            'transfer_group' => '{ORDER10}',
          ]);
    }

    function pdfdown(){
        $pdf = \PDF::loadView('frontend/orderpdf');
        $path = public_path('uploads/order/');
        $fileName = 'Order-1'.'.pdf';
        $pdf->save($path.'/'.$fileName);
        return $pdf->download($path.'/'.$fileName);
    }
    function my_order(){
        $user_id= auth()->user()->id;
        $orders = Order::where('order_customer_id',$user_id)->orderBy('order_id', 'DESC')->get();
        $data['orders'] = $orders;
        return view('frontend.my_order',$data);
    }
    function my_services(){
        $user = auth()->user();
        $user_id = $user->id;
        //$services = Service_booking::where('service_booking.email',$users->email)->leftjoin('services','services.id','=','service_booking.service_id')->orderBy('Service_booking.id', 'DESC')->get();
        $services = Service_booking::leftJoin('services', 'service_booking.service_id', '=', 'services.id')
        ->where('service_booking.email', $user->email)
        ->select('service_booking.id', 'service_booking.created_at', 'services.name', 'service_booking.service_acceptance_status', 'service_booking.service_offered_price', 'service_booking.payment_status', 'service_booking.message', 'services.id as service_id')
        ->orderBy('service_booking.id', 'DESC')
        ->get();
    
        foreach($services as $key =>$service){
            $payment_link[$key] = URL::to('service_payment_form/?_tkn='.encrypt($user->email.','.$service->id.','.$service->service_id.','.$service->service_offered_price));
        }

        $data['services'] = $services;
        $data['payment_link'] = $payment_link;
        return view('frontend.my_services',$data);
    }
    function order_details(Request $request,$id){
        $user_id= auth()->user()->id;
        $order_details = Order_details::where('order_id',$id)->leftjoin('products','products.id','=','order_details.order_product_id')->get();
        $billing = User::where('id',$user_id)->first();
        $order =   Order::where('order_id',$id)->first();
        $shipping = json_decode($order->orderaddress);
        $data['order_details'] = $order_details;
        $mystate = State::where('id',$billing->state_id)->first();
        $mycity  = City::where('id',$billing->city_id)->first();
        $data['mystate']         = $mystate;
        $data['mycity']          = $mycity;
        $data['billingAddress']=$billing;
        $data['shippingAddress']=$shipping;
        return view('frontend.order_details',$data);
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
        $user_id= auth()->user()->id;
        $usercountry = User::where('id',$user_id)->first();
        $country = Countries::where('sortName',$usercountry->country)->first();
        $state = State::where('countryId',$country->id)->get();
        $city = City::where('id',$usercountry->city_id)->first();
        return view('frontend.dashboard.dashboard')->with(['user'=>$user,'state'=>$state,'city'=>$city]);
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
        $update1 = array();
        $update1['name'] = $request->name;
        $update1['state_id'] = $request->state;
        $update1['city_id'] = $request->delcity;
        $update1['pincode'] = $request->pincode;
        $update1['address'] = $request->address;
        if($request->file('image')){
            $file = $request->file('image');
            $ext = substr(strrchr($file->getClientOriginalName(), '.'), 1);
            $new_name1 = str_replace(".", "", microtime());
            $new_name = str_replace(" ", "_", $new_name1);
            $filename = $new_name.'.'.$ext;
            \Image::make($file->getRealPath())->save('uploads/profile/'.$filename); 
            $update1['profile_image'] = $filename;
        }
        User::updateorcreate(['id' => decrypt($request->user_id)], $update1);
        $update = array();
        $update['user_id'] = decrypt($request->user_id);

        if($request->file('pancard_image')){
            $file = $request->file('pancard_image');
            $filename = Carbon::now()->timestamp.'_'.$file->getClientOriginalName();

            //Resizing and compressing the image
            if(\Image::make($file->getRealPath())->save('uploads/profile/customers/'.$filename, 60)){
                $update['pancardimage'] = $filename;
            } else{
                return response()->json(['status' => 'Pancard image cannot be saved to server.'], 400);
            }
        }

        if($request->file('aadharcard_image')){
            $file = $request->file('aadharcard_image');
            $filename = Carbon::now()->timestamp.'_'.$file->getClientOriginalName();

            //Resizing and compressing the image
            if(\Image::make($file->getRealPath())->save('uploads/profile/customers/'.$filename, 60)){
                $update['aadharcardimage'] = $filename;
            } else{
                return response()->json(['status' => 'Aadharcard image cannot be saved to server.'], 400);
            }
        }

        if($request->file('cancelled_cheque_image')){
            $file = $request->file('cancelled_cheque_image');
            $filename = Carbon::now()->timestamp.'_'.$file->getClientOriginalName();

            //Resizing and compressing the image
            if(\Image::make($file->getRealPath())->save('uploads/profile/customers/'.$filename, 60)){
                $update['cancelledchequeimage'] = $filename;
            } else{
                return response()->json(['status' => 'Cancelled cheque image cannot be saved to server.'], 400);
            }
        }

       
       
        CustomerDetail::updateorcreate(['user_id' => decrypt($request->user_id)], $update);
        if(auth()->user()->role_id == Role::IS_CUSTOMER)
            return redirect()->route('customer.customer_dashboard')->with('message', 'Account details updated successfully.');
        elseif(auth()->user()->role_id == Role::IS_SELLER)
            return redirect()->route('seller.seller_dashboard')->with('message', 'Account details updated successfully.');
    }

    public function contact_us()
    {
        $setting = Setting::all([
        'address1',
        'address2',
        'address3',
        'map_embed_link',
        'site_email',
        'site_link',
        'site_number',
        'site_number_office_name'])->first();
      
        return view('frontend.contact-us')->with(['setting'=>$setting]);
    }

    public function contact_us_post(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required|min:10',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
            //return redirect()->back()->with('message', 'Password must have a minimum length of 6 and both passwords should match.');
        }

        DB::table('contacts')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->mobile,
            'message' => $request->message,
            'created_at' => date('Y-m-d h:i:s'),
        ]);

        // \Config::set([
        //     #Mail Configuration
        //     'mail.host' => 'smtp.mailtrap.io',
        //     'mail.port' => 2525,
        //     'mail.encryption' => 'tls',
        //     'mail.username' => '',
        //     'mail.password' => '',
        // ]);

        $txt = '';
        $txt .= '<p>The following response has been submited by an user</p>';
        $txt .= '<p><strong>Name: </strong>'.$request->name.'</p>';
        $txt .= '<p><strong>Email: </strong>'.$request->email.'</p>';
        $txt .= '<p><strong>Mobile: </strong>'.$request->mobile.'</p>';
        $txt .= '<p><strong>Message: </strong>'.$request->message.'</p>';
        $subject = 'Contact Us Response - Elesonic';
        //$mailFromId = config()->get('mail.from.address');
        $mailFromId = 'debo2696@gmail.com';
        //dd(config()->all());
        //return (new OnlyTextMail($request->name, $mailFromId, $txt, $subject))->render();
        //dd($txt, $subject, $mailFromId);
        try{
            Mail::to($mailFromId)->send(new OnlyTextMail('Admin', $mailFromId, $txt, $subject));
            $txt = "You have successfully filled up the Contact us form.";
            Mail::to($request->email)->send(new OnlyTextMail($request->name, $mailFromId, $txt, $subject));
        }
        catch(Exception $e1){
            
        }
        if(isset($e1)){dd($e1);}
        return redirect()->back()->with('message', 'Response submitted successfully!');

    }

    function products(){
        $user_id= auth()->user()->id;
        return view('frontend.seller.products');
    }
    function add_product(){
        $categories = Categorys::where('parent_id', 0)->where('status','A')
        ->with('childrenCategories')
        ->get();
        $brand = Brand::where('status','A')
        ->get();
        $data['categories'] = $categories;
        $data['brands'] = $brand;
        return view('frontend.seller.create', $data);
    }
    function productstore(Request $request){
        $user_id= auth()->user()->id;
        $product = new Product;
        $product->name = $request->name;
        $product->category_id  = $request->category_id;
        $product->brand_id  = $request->brand_id;
        $product->quantity  = $request->quantity;
        $product->type  = 'old';
        $product->added_by  = 'seller';
        if($request->net_price == ""){
            $request->net_price = $request->mrp - ($request->mrp * $request->discount)/100;
        }else{
            $request->net_price = $request->net_price;
        }
        $request->net_price = number_format((float)$request->net_price, 2, '.', '');
        $product->unit_price  = $request->net_price;
        $product->purchase_price  = $request->mrp;
        $product->discount  = $request->discount;
        $product->meta_title = $request->meta_title;
        $product->meta_description = $request->meta_description;
        $product->meta_keyword = $request->meta_keyword;
        $product->user_id = $user_id;
        $chkSlug = Product::where('slug',$request->slug)->count();
        if($chkSlug > 0){
            return redirect()->route('seller.products')->with('message', 'duplicate slug.');
        }
        if ($request->slug != null) {
            $product->slug = $request->slug;
        } else {
            $product->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)) . '-' . Str::random(5);
        }
       
        if($request->file('image')){
            $file = $request->file('image');
            
            $ext = substr(strrchr($file->getClientOriginalName(), '.'), 1);
            $new_name1 = str_replace(".", "", microtime());
            $new_name = str_replace(" ", "_", $new_name1);
            $filename = $new_name.'.'.$ext;

            if(\Image::make($file->getRealPath())->save('uploads/products/'.$filename)){
                $product->photos = $filename;
            } else{
                return redirect()->route('seller.products')->with('message', 'Error image uload.');
            }
        }
       
        $product->description = $request->prodescription;
        $product->save();
        $multiImages = $request->file('related_image');
        if($multiImages){
           
            for($i=0;$i<count($multiImages);$i++){
                $Product_related_images = new Product_related_images;
                $ext = substr(strrchr($multiImages[$i]->getClientOriginalName(), '.'), 1);
                $new_name1 = str_replace(".", "", microtime());
                $new_name = str_replace(" ", "_", $new_name1);
                $multifilename = $new_name.'.'.$ext;
                if(\Image::make($multiImages[$i]->getRealPath())->save('uploads/products/'.$multifilename)){
                $Product_related_images->image = $multifilename;
                $Product_related_images->product_id = $product->id;
                $Product_related_images->save();
                }
            }
        }
        return redirect()->route('seller.products')->with('message', 'Product Added.');
    }
    function productupdate(Request $request){
        $id = $request->id;
        $product = Product::findOrFail($id);
        $product->name = $request->name;
        $product->category_id  = $request->category_id;
        $product->brand_id  = $request->brand_id;
        $product->quantity  = $request->quantity;
        if($request->net_price == ""){
            $request->net_price = $request->mrp - ($request->mrp * $request->discount)/100;
        }else{
            $request->net_price = $request->net_price;
        }
        $request->net_price = number_format((float)$request->net_price, 2, '.', '');
        $product->unit_price  = $request->net_price;
        $product->purchase_price  = $request->mrp;
        $product->discount  = $request->discount;
        $product->meta_title = $request->meta_title;
        $product->meta_description = $request->meta_description;
        $product->meta_keyword = $request->meta_keyword;
        $chkSlug = Product::where('slug',$request->slug)->count();
        
        if ($request->slug != null) {
            $product->slug = $request->slug;
        } else {
            $product->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)) . '-' . Str::random(5);
        }
       
        if($request->file('image')){
            $file = $request->file('image');
            
            $ext = substr(strrchr($file->getClientOriginalName(), '.'), 1);
            $new_name1 = str_replace(".", "", microtime());
            $new_name = str_replace(" ", "_", $new_name1);
            $filename = $new_name.'.'.$ext;

            if(\Image::make($file->getRealPath())->save('uploads/products/'.$filename)){
                $product->photos = $filename;
            } else{
                return redirect()->route('seller.products')->with('message', 'Error in image upload.');
            }
        }
        $product->description = $request->description;
        $product->save();
        $multiImages = $request->file('related_image');
        if($multiImages){
           
            for($i=0;$i<count($multiImages);$i++){
                $Product_related_images = new Product_related_images;
                $ext = substr(strrchr($multiImages[$i]->getClientOriginalName(), '.'), 1);
                $new_name1 = str_replace(".", "", microtime());
                $new_name = str_replace(" ", "_", $new_name1);
                $multifilename = $new_name.'.'.$ext;
                if(\Image::make($multiImages[$i]->getRealPath())->save('uploads/products/'.$multifilename)){
                $Product_related_images->image = $multifilename;
                $Product_related_images->product_id = $product->id;
                $Product_related_images->save();
                }
            }
        }
        return redirect()->route('seller.products')->with('message', 'Product Updated.');
    }
    function productedit(Request $request, $id){
        $id =  request()->segment(4);
        $product = Product::findOrFail($id);
        $categories = Categorys::where('parent_id', 0)->where('status','A')
        ->with('childrenCategories')
        ->get();

        $categories = Categorys::where('parent_id', 0)
        ->with('childrenCategories')
        ->get();

        $multiImage = Product_related_images::where('product_id', $id)
        ->get();
 
        $brand = Brand::where('status','A')
        ->get();
        $data['categories'] = $categories;
        $data['brands'] = $brand;
        $data['product'] = $product;
        $data['multiImage'] = $multiImage;
        return view('frontend.seller.edit', $data);
    }
    function imageDelete(Request $request){
        $id = $request->id;
        if($id){
            $res=Product_related_images::where('id',$id)->delete();
        }
        $res = 1;
        echo json_encode($res);
    }
    function seller_order(){
        $user_id= auth()->user()->id;
        $orders = Order_details::where('product_seller_id',$user_id)->leftjoin('order','order.order_id','=','order_details.order_id')->leftjoin('products','order_details.order_product_id','=','products.id')->get();
        $data['orders'] = $orders;
        return view('frontend.seller.my_order',$data);
    }
    function seller_order_details(Request $request,$id){
        $user_id= auth()->user()->id;
        $order_details = Order_details::where('order_details.order_id',$id)->where('order_details.product_seller_id',$user_id)->leftjoin('products','products.id','=','order_details.order_product_id')->get();
        $order =   Order::where('order_id',$id)->first();
        $billing = User::where('id',$order->order_customer_id)->first();
        $shipping = json_decode($order->orderaddress);
        $data['order_details'] = $order_details;
        $data['billingAddress']=$billing;
        $data['shippingAddress']=$shipping;
        $data['orderid'] = $id;
        $data['path'] = asset('public/uploads/order/Order-'.$order->order_unique_id.'.pdf');
        $mystate = State::where('id',$billing->state_id)->first();
        $mycity  = City::where('id',$billing->city_id)->first();
        $data['mystate']         = $mystate;
        $data['mycity']          = $mycity;
        return view('frontend.seller.order_details',$data);
    }

    function seller_reports(Request $request){
        return view('frontend.seller.reports');
    }
    function generate_excel_revenue(Request $request){
        $startdate = $request->startDate;
        $enddate = $request->endDate;
        $user_id= auth()->user()->id;
        $orders = Order_details::whereBetween('order_details.created_at', [$startdate, $enddate])->where('product_seller_id',$user_id)->leftjoin('order','order.order_id','=','order_details.order_id')->leftjoin('products','order_details.order_product_id','=','products.id')->get();
        
        $titles = [
            'Start Date',
            'End Date',
            'Total Revenue',
        ];
        $excelData = [];
        $cntr = 1;
        $name = 'Revenue' . '.xlsx';
        $revenue = 0;
        foreach ($orders as $key => $data) {
            $revenue = $revenue + $data->seller_commission;
        }
        $output['Start Date'] = $request->startDate;
        $output['End Date']   = $request->endDate;
        $output['Revenue']    = $revenue;
        array_push($excelData, $output);
        return Excel::download(new OrderReportsExportByRange($titles, $excelData), $name);
    }
    function generate_excel(Request $request){
        $startdate = $request->startDate;
        $enddate = $request->endDate;
        $user_id= auth()->user()->id;
        $orders = Order_details::whereBetween('order_details.created_at', [$startdate, $enddate])->where('product_seller_id',$user_id)->leftjoin('order','order.order_id','=','order_details.order_id')->leftjoin('products','order_details.order_product_id','=','products.id')->get();
        
        $titles = [
            'Sl',
            'Order Date',
            'Order Code',
            'Status',
            'Customer',
            'Product',
            'QTY',
            'MRP',
            'Discount',
            'Net Price',
            'Shipping',
            'Sub Total',
            'Commission',
        ];
        $excelData = [];
        $cntr = 1;
        $name = 'Order' . '.xlsx';
        foreach ($orders as $key => $data) {
            $billing = User::where('id',$data->order_customer_id)->first();
            $output = array();
            $output['Sl'] = $cntr++;
            $output['Order Date'] = $data->created_at;
            $output['Order Code'] = $data->order_unique_id;
            $output['Status'] = $data->order_product_status;
            $output['Customer'] = $billing->name;
            $output['Product'] = $data->name;
            $output['QTY'] = $data->cart_item_pro_qty;
            $output['MRP'] = $data->cart_item_price;
            $output['Discount'] = $data->cart_item_price_disc;
            $output['Net Price'] = $data->cart_item_net_price;
            $output['Shipping'] = $data->shipping_charge;
            $output['Sub Total'] = $data->cart_item_pro_qty * $data->cart_item_net_price;
            $output['Commission'] = $data->seller_commission;
            array_push($excelData, $output);
        }
        return Excel::download(new OrderReportsExportByRange($titles, $excelData), $name);
    }
    public function fetchData($type, $fetch='all', $id='none', Request $request){
        $user_id= auth()->user()->id;
        switch($type){
            case 'product':
                $query = Product::query();
                $query->where('status','!=','D');
                $query->where('user_id',$user_id);
                $request['searchdata'] = [];
            break;
            default:
                abort(404, 'Invalid request recieved');
        }
        $input = $request->all();
        foreach($request->searchdata as $key => $value){
            if(isset($input[$value]) && $input[$value] != ''){
                $query->where($value, $input[$value]);
            }
        }
        switch ($fetch) {
            case 'single':
                return response()->json(['result' => $query->first()], 200);
            break;
        }
        if(request()->ajax()){
            return datatables()->of($query)->make(true);
        }
    }

    public function statusChange(Request $request){
        switch($request->type){
            case 'statusChange':
                $status = Product::findorfail($request->id);
                if($status->status == 'A' ){
                    $request['status'] = 'I';
                } else{
                    $request['status'] = 'A';
                }
                Product::where('id', $request->id)->update($request->except(['_token','type']));
            break;
            case 'delete':
                $request['status'] = 'D';
                $brand = Product::findorfail($request->id);
                Product::where('id', $request->id)->update($request->except(['_token','type']));
            break;
            case 'popular':
                $status = Product::findorfail($request->id);
                if($status->ispopular == 'Y' ){
                    $request['ispopular'] = 'N';
                } else{
                    $request['ispopular'] = 'Y';
                }
                Product::where('id', $request->id)->update($request->except(['_token','type']));
            break;
        }
    }
    
    public function service_payment_form(Request $request)
    {
        $token = $request->_tkn;
        $dec_arr = explode(",",decrypt($token));

        $service_booking = Service_booking::where('id', $dec_arr[1])->firstOrFail();
        // dd($service_booking->toArray(), date('Y-m-d h:i:s'), Carbon::createFromFormat('Y-m-d h:i:s', $service_booking->service_request_acceptance_date)->diffInMinutes(Carbon::now()));
        if(Carbon::createFromFormat('Y-m-d h:i:s',$service_booking->service_request_acceptance_date)->diffInMinutes(Carbon::now()) > 120)
        {
            dump('Payment link expired');
            //return redirect()->back()->with('error', 'Payment link expired');
        } 
        
        $data['tk'] = $token;
        $data['price'] = $service_booking->service_offered_price; 
        $data['stripe_publishable_key'] = config()->get('stripe.publishable_key');
        return view('frontend.service_stripe_payment', $data);
    }

    public function service_payment_post(Request $request)
    {
        $dec_arr = explode(",",decrypt($request->tk));
        $service_booking = Service_booking::where('id', $dec_arr[1])->firstOrFail();
        $user_id = User::where('email', $service_booking->email)->value('id');
        try{
            Stripe\Stripe::setApiKey(config()->get('stripe.secret_key'));
            $charge = Stripe\Charge::create ([
                "amount" => $dec_arr[3] * 100,
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Service payment from Customer" 
            ]);
            //dd($request->all(), $dec_arr, $charge);
        }
        catch(\Stripe\Exception\CardException $e) {
            // Since it's a decline, \Stripe\Exception\CardException will be caught
            $err = '';
            $err += 'Status is:' . $e->getHttpStatus() . '\n';
            $err += 'Type is:' . $e->getError()->type . '\n';
            $err += 'Code is:' . $e->getError()->code . '\n';
            // param is '' in this case
            $err += 'Param is:' . $e->getError()->param . '\n';
            $err += 'Message is:' . $e->getError()->message . '\n';
          } catch (\Stripe\Exception\RateLimitException $e) {
            $err = 'Too many requests made to the API too quickly';
          } catch (\Stripe\Exception\InvalidRequestException $e) {
            $err = 'Invalid parameters were supplied to Stripe API';
          } catch (\Stripe\Exception\AuthenticationException $e) {
            $err = 'Authentication with Stripe API failed';
          } catch (\Stripe\Exception\ApiConnectionException $e) {
            $err = 'Network communication with Stripe failed';
          } catch (\Stripe\Exception\ApiErrorException $e) {
            $err =  'Stripe API error';
          } catch (Exception $e) {
            $err =  'Something else happened, completely unrelated to Stripe';
          }

        try{        
        DB::table('service_payment_history')->insert([
            'stripe_token' => $request->stripeToken,
            'user_token' => $request->tk,
            'service_id' => $service_booking->service_id,
            'user_id' => $user_id,
            'amount' => $dec_arr[3],
            'payment_date' => $charge->created, //UNIX timestamp
            'payment_json' => $charge,
            'charge_id' => $charge->id,
            'txn_id' => $charge->balance_transaction,
            'status' => $charge->status,
            'comment' => 'service payment from customer',
            'created_at' => date('Y-m-d h:i:s'),
        ]);

        Service_booking::where('service_id', $service_booking->service_id)->update([
            'payment_status' => 'paid',
            'updated_at' => date('Y-m-d h:i:s'),
        ]);
        }
        catch(Exception $e1)
        {
            $err = $e1->getMessage();
        }
        if(isset($e) || isset($e1)){
            dd($err);
        }

        return redirect()->route('customer.my-services');
    }
}

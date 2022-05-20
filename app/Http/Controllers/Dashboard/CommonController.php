<?php

namespace App\Http\Controllers\Dashboard;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use App\Model\Blog;
use App\Model\Role;
use App\Model\Permission;
use App\Model\FaqContent;
use App\Model\CmsContent;
use App\Model\LandingBanner;
use App\Model\MembershipPlan;
use App\Model\TestimonialContent;
use App\Model\LoanType;
use App\Model\Scheme;
use App\Model\Category;
use App\Model\Attribute;
use App\Model\Brand;
use App\Model\Product;
use App\Model\Service;
use App\Model\Department;
use App\Model\Service_booking;
use App\Model\Order;
use App\Model\Order_details;
use DB;

class CommonController extends Controller
{
    public function fetchData($type, $fetch='all', $id='none', Request $request){
       
        switch($type){
            case 'roles':
                $query = Role::query();
                $request['searchdata'] = [];
            break;

            case 'permissions':
                $query = Permission::query();
                $request['searchdata'] = [];
            break;

            case 'category':
                $query = Category::query();
                $query->where('status','!=','D');
                $request['searchdata'] = [];
            break;
            case 'attributes':
                $query = Attribute::query();
                $query->where('status','!=','D');
                $request['searchdata'] = [];
            break;

            case 'brand':
                $query = Brand::query();
                $query->where('status','!=','D');
                $request['searchdata'] = [];
            break;
            case 'orders':
                $query = Order::query();
                $query->leftJoin('users', 'users.id', '=', 'order.order_customer_id');
                $request['searchdata'] = [];
                $request['searchdata'] = [];
            break;
            case 'product':
                $query = Product::query();
                $query->where('status','!=','D');
                $request['searchdata'] = [];
            break;

            case 'faqs':
                $query = FaqContent::query();
                $request['searchdata'] = [];
            break;

            case 'contents':
                $query = CmsContent::query();
                $request['searchdata'] = [];
            break;

            case 'testimonials':
                $query = TestimonialContent::query();
                $request['searchdata'] = [];
            break;

            case 'blogs':
                $query = Blog::query();
                $request['searchdata'] = [];
            break;

            case 'customer':
                $query = User::whereHas('role', function($q){
                    $q->where('slug', 'customer');
                });

                $request['searchdata'] = [];
            break;

            case 'seller':
                $query = User::whereHas('role', function($q){
                    $q->where('slug', 'seller');
                });

                $request['searchdata'] = [];
            break;

            case 'agent':
                $query = User::whereHas('role', function($q){
                    $q->where('slug', 'agent');
                });

                $request['searchdata'] = [];
            break;

            case 'bank':
                $query = User::whereHas('role', function($q){
                    $q->where('slug', 'bank');
                });

                $request['searchdata'] = [];
            break;

            case 'admin':
                $query = User::whereHas('role', function($q){
                    $q->where('slug', 'admin');
                });

                $request['searchdata'] = [];
            break;

            case 'notificationusers':
                $query = User::whereHas('role', function($q){
                    $q->whereIn('slug', ['customer','agent','bank']);
                });

                $request['searchdata'] = ['role_id', 'status'];
            break;

            case 'membershipplans':
                $query = MembershipPlan::query();
                $request['searchdata'] = ['role_id'];
            break;

            case 'loantypes':
                $query = LoanType::query();
                $request['searchdata'] = [];
            break;

            case 'schemes':
                $query = Scheme::query();
                $request['searchdata'] = ['role_id'];
            break;

            case 'banners':
                //$query = LandingBanner::whereNotIn('status',['D'])->get();
                $query = LandingBanner::query();
                $query->whereNotIn('status',['D']);
                $request['searchdata'] = []; //Mention with what a search can be done
            break;

            case 'services':
                //$query = LandingBanner::whereNotIn('status',['D'])->get();
                $query = Service::query();
                $query->whereNotIn('status',['D']);
                $request['searchdata'] = []; //Mention with what a search can be done
            break;
            case 'departments':
                //$query = LandingBanner::whereNotIn('status',['D'])->get();
                $query = Department::query();
                $query->whereNotIn('status',['D']);
                $request['searchdata'] = []; //Mention with what a search can be done
            break;
            case 'request_service':
                $query = Service_booking::query();
                $query->leftJoin('services', 'service_booking.service_id', '=', 'services.id')->whereNotIn('service_booking.status',['D'])->select('service_booking.id', 'services.name as service_name', 'service_booking.name', 'service_booking.email', 'service_booking.phone', 'service_booking.status', 'service_booking.created_at', 'service_booking.service_acceptance_status');
                $request['searchdata'] = [];
            break;

            case 'service_payment_history':
                $query = \DB::table('service_payment_history')->leftJoin('services', 'service_payment_history.service_id', '=', 'services.id')->Join('users', 'service_payment_history.user_id', '=', 'users.id')
                ->select('service_payment_history.id', 'users.email', 'services.name', 'service_payment_history.amount', 'service_payment_history.status',  'service_payment_history.charge_id', 'service_payment_history.created_at', 'service_payment_history.receipt_url');
                $request['searchdata'] = [];
            break;
            default:
                abort(404, 'Invalid request recieved');
        }

        if($id != 'none'){
            // dd($query->getQuery()->joins);
            // if($query->getQuery()->joins == null)
                $query->where('id', $id);
            //else{}
                
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
}

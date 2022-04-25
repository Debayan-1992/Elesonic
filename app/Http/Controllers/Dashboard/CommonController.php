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
use App\Model\MembershipPlan;
use App\Model\TestimonialContent;
use App\Model\LoanType;
use App\Model\Scheme;

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

            default:
                abort(404, 'Invalid request recieved');
        }

        if($id != 'none'){
            $query->where('id', $id);
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

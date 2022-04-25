<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\User;
use App\Model\Role;
use App\Model\Scheme;
use App\Model\LoanType;
use App\Model\LoanSlab;
use App\Model\Commission;
use App\Model\MembershipPlan;

class ResourcesController extends Controller
{
    public function packages($type){
        $data['activemenu'] = array(
            'main' => 'resources',
            'sub' => 'packages',
        );

        switch ($type) {
            case 'agent':
                $data['activemenu']['child'] = 'agent';
                $permission = 'view_agent_membership_packages';
            break;

            case 'bank':
                $data['activemenu']['child'] = 'bank';
                $permission = 'view_bank_membership_packages';
            break;

            default:
                abort(404);
            break;
        }

        if(isset($permission) && !\Myhelper::can($permission)){
            abort(401);
        }

        $data['role'] = Role::where('slug', $type)->first();
        if(!$data['role']){
            abort(404);
        }

        return view('dashboard.resources.packages', $data);
    }

    public function packagesubmit(Request $post){
        switch ($post->type){
            case 'new':
                $rules = array(
                    'name' => 'required',
                    'slug' => 'required|unique:membership_plans,role_id,'.$post->role_id,
                    'original_price' => 'required|numeric',
                    'offered_price' => 'nullable|numeric',
                    'validity' => 'required|numeric',
                    'description' => 'required',
                    'role_id' => 'required',
                );

                if($post->has('role_id') && $post->role_id != null){
                    $role = Role::where('id', $post->role_id)->whereIn('slug', ['agent','bank'])->first();
                    $permission = 'add_'.$role->slug.'_membership_package';
                }
            break;

            case 'edit':
                $rules = array(
                    'id' => 'required',
                    'name' => 'required',
                    'original_price' => 'required|numeric',
                    'offered_price' => 'nullable|numeric',
                    'validity' => 'required|numeric',
                    'description' => 'required',
                    'role_id' => 'required',
                );

                if($post->has('role_id') && $post->role_id != null){
                    $role = Role::where('id', $post->role_id)->whereIn('slug', ['agent','bank'])->first();
                    $permission = 'edit_'.$role->slug.'_membership_package';
                }
            break;

            case 'changeaction':
            case 'changefeatured':
                $rules = array(
                    'id' => 'required',
                );

                if($post->has('role_id') && $post->role_id != null){
                    $role = Role::where('id', $post->role_id)->whereIn('slug', ['agent','bank'])->first();
                    $permission = 'edit_'.$role->slug.'_membership_package';
                }
            break;

            default:
                return response()->json(['status' => 'Invalid Request'], 400);
            break;
        }

        if(isset($permission) && !\Myhelper::can($permission)){
            return response()->json(['status' => 'Access Denied'], 401);
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
            case 'changeaction':
                $plan = MembershipPlan::findorfail($post->id);
                if($plan->status){
                    $post['status'] = '0';
                } else{
                    $post['status'] = '1';
                }

                $action = MembershipPlan::where('id', $post->id)->update($post->except(['_token','type']));
            break;

            case 'changefeatured':
                $plan = MembershipPlan::findorfail($post->id);
                if($plan->featured){
                    $post['featured'] = '0';
                } else{
                    $post['featured'] = '1';
                }

                $action = MembershipPlan::where('id', $post->id)->update($post->except(['_token','type']));
            break;

            case 'new':
            case 'edit':
                $action = MembershipPlan::updateorcreate(['id' => $post->id], $post->all());
            break;
        }

        if($action){
            return response()->json(['status' => 'Task completed successfully'], 200);
        } else{
            return response()->json(['status' => 'Task failed. Please try again'], 400);
        }
    }

    public function schemes($type){
        $data['activemenu'] = array(
            'main' => 'resources',
            'sub' => 'schemes',
        );

        switch ($type) {
            case 'agent':
                $data['activemenu']['child'] = 'agent';
                $permission = 'view_agent_schemes';
            break;

            default:
                abort(404);
            break;
        }

        if(isset($permission) && !\Myhelper::can($permission)){
            abort(401);
        }

        $data['role'] = Role::where('slug', $type)->first();
        if(!$data['role']){
            abort(404);
        }

        $data['loantypes'] = LoanType::all();
        $data['loanslabs'] = LoanSlab::all();

        return view('dashboard.resources.schemes', $data);
    }

    public function schemesubmit(Request $post){
        switch ($post->type){
            case 'new':
                $rules = array(
                    'name' => 'required',
                    'role_id' => 'required',
                );

                if($post->has('role_id') && $post->role_id != null){
                    $role = Role::where('id', $post->role_id)->whereIn('slug', ['agent'])->first();
                    $permission = 'add_'.$role->slug.'_scheme';
                }
            break;

            case 'edit':
                $rules = array(
                    'id' => 'required',
                    'name' => 'required',
                    'role_id' => 'required',
                );

                if($post->has('role_id') && $post->role_id != null){
                    $role = Role::where('id', $post->role_id)->whereIn('slug', ['agent'])->first();
                    $permission = 'edit_'.$role->slug.'_scheme';
                }
            break;

            case 'changeaction':
                $rules = array(
                    'id' => 'required',
                );

                if($post->has('role_id') && $post->role_id != null){
                    $role = Role::where('id', $post->role_id)->whereIn('slug', ['agent'])->first();
                    $permission = 'edit_'.$role->slug.'_scheme';
                }
            break;

            case 'delete':
                $rules = array(
                    'id' => 'required',
                );
            break;

            default:
                return response()->json(['status' => 'Invalid Request'], 400);
            break;
        }

        if(isset($permission) && !\Myhelper::can($permission)){
            return response()->json(['status' => 'Access Denied'], 401);
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
            case 'changeaction':
                $scheme = Scheme::findorfail($post->id);
                if($scheme->status){
                    $post['status'] = '0';
                } else{
                    $post['status'] = '1';
                }

                $action = Scheme::where('id', $post->id)->update($post->except(['_token','type']));
            break;

            case 'new':
            case 'edit':
                $action = Scheme::updateorcreate(['id' => $post->id], $post->all());
            break;

            case 'delete':
                $action = Scheme::where('id', $post->id)->delete();
            break;
        }

        if($action){
            return response()->json(['status' => 'Task completed successfully'], 200);
        } else{
            return response()->json(['status' => 'Task failed. Please try again'], 400);
        }
    }

    public function getcommission(Request $post){
        $rules = array(
            'scheme_id' => 'required|numeric',
            'type_id' => 'required|numeric',
        );

        if(isset($rules)){
            $validator = \Validator::make($post->all(), $rules);
            if($validator->fails()){
                foreach($validator->errors()->messages() as $key => $value){
                    return response()->json(['status' => $value[0]], 400);
                }
            }
        }

        return Commission::where('scheme_id', $post->scheme_id)->where('type_id', $post->type_id)->get(['slab', 'type', 'value'])->toJson();
    }

    public function commissionsubmit(Request $post){
        $rules = array(
            'scheme_id' => 'required|numeric',
            'role_id' => 'required|numeric',
            'type_id' => 'required|numeric',
        );

        if(isset($rules)){
            $validator = \Validator::make($post->all(), $rules);
            if($validator->fails()){
                foreach($validator->errors()->messages() as $key => $value){
                    return response()->json(['status' => $value[0]], 400);
                }
            }
        }

        $role = Role::where('id', $post->role_id)->whereIn('slug', ['agent'])->first();
        if(!\Myhelper::can('manage_'.$role->slug.'_commission')){
            return response()->json(['status' => 'Permission not allowed'], 400);
        }

        foreach ($post->slab as $key => $value) {
            $loanslab = LoanSlab::where('id', $value)->first();
            $pass = true;

            if($post->value[$key] < 0 ){
                $pass = false;
                $update[$post->slab[$key]] = "value should be greater than zero";
            }


            if($pass){
                $update[$value] = Commission::updateOrCreate(
                    [
                        'scheme_id' => $post->scheme_id,
                        'slab'      => $post->slab[$key],
                        'role_id'   => $post->role_id,
                        'type_id'   => $post->type_id,
                    ],
                    [
                        'scheme_id' => $post->scheme_id,
                        'slab'      => $post->slab[$key],
                        'type'      => $post->type[$key],
                        'value'     => $post->value[$key],
                        'role_id'   => $post->role_id,
                        'type_id'   => $post->type_id,
                    ]
                );
            }
        }

        return response()->json(['status'=>$update], 200);
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\Myhelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\UserCreateMail;
use App\Mail\UserCreateOTPMail;
use Carbon\Carbon;

use App\Model\Role;
use App\Model\Permission;
use App\Model\UserPermission;
use App\Model\DefaultPermission;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MembersController extends Controller
{
    public function index($type){
        // dd(Myhelper::otp_get());
        $data['activemenu']['main'] = 'members';

        $users = User::query();
        $role = Role::query();
        switch ($type) {
            case 'customer':
                $data['activemenu']['sub'] = 'customer';
                $permission = 'view_customers';

                $users->whereHas('role', function($q){
                    $q->where('slug', 'customer');
                });

                $role->where('slug', 'customer');
            break;

            case 'seller':
                $data['activemenu']['sub'] = 'seller';
                $permission = 'view_sellers';

                $users->whereHas('role', function($q){
                    $q->where('slug', 'seller');
                });

                $role->where('slug', 'seller');
            break;

            case 'bank':
                $data['activemenu']['sub'] = 'bank';
                $permission = 'view_banks';

                $users->whereHas('role', function($q){
                    $q->where('slug', 'bank');
                });

                $role->where('slug', 'bank');
            break;

            case 'admin':
                $data['activemenu']['sub'] = 'admin';
                $permission = 'view_admins';

                $users->whereHas('role', function($q){
                    $q->where('slug', 'admin');
                });

                $role->where('slug', 'admin');
            break;

            default:
                abort(404);
            break;
        }

        if(isset($permission) && !\Myhelper::can($permission)){
            abort(401);
        }

        $data['users'] = $users->get();
        $data['role'] = $role->first();
        $data['type'] = $type;

        return view('dashboard.members.index', $data);
    }

    public function add($type){
        $data['activemenu']['main'] = 'members';

        $role = Role::query();

        switch ($type) {
            case 'customer':
                $data['activemenu']['sub'] = 'customer';
                $permission = 'add_customer';
                $view = 'addcustomer';

                $role->where('slug', 'customer');
            break;

            case 'seller':
                $data['activemenu']['sub'] = 'seller';
                $permission = 'add_seller';
                $view = 'addseller';

                $role->where('slug', 'seller');
            break;

            case 'bank':
                $data['activemenu']['sub'] = 'bank';
                $permission = 'add_bank';
                $view = 'addbank';

                $role->where('slug', 'bank');
            break;

            case 'admin':
                $data['activemenu']['sub'] = 'admin';
                $permission = 'add_admin';
                $view = 'addadmin';

                $role->where('slug', 'admin');
            break;

            default:
                abort(404);
            break;
        }

        if(isset($permission) && !\Myhelper::can($permission)){
            abort(401);
        }

        $data['role'] = $role->first();
        $data['type'] = $type;

        return view('dashboard.members.'.$view, $data);
    }

    public function create($type, Request $post){
        //return (new UserCreateMail('sdfsd', 'dbo@asd.com', '34534asdfs'))->render();
        switch ($type) {
            case 'admin':
                $rules = array(
                    'name' => 'required',
                    'email' => 'required|unique:users',
                    'mobile' => 'nullable|unique:users',
                );
            break;

            case 'customer':
                $rules = array(
                    'name' => 'required',
                    'email' => 'required|unique:users',
                    'mobile' => 'required|unique:users',
                );
            break;

            case 'seller':
                $rules = array(
                    'name' => 'required',
                    'email' => 'required|unique:users',
                    'mobile' => 'required|unique:users',
                );
            break;

            default:
                return response()->json(['status' => 'Invalid Request'], 404);
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

        if(isset($permission) && !\Myhelper::can($permission)){
            return response()->json(['status' => 'Permission not Allowed'], 401);
        }

        $password = strstr($post->email, '@', true);
        $post['password'] = bcrypt($password);

        $role = Role::where('slug', $type)->first();
        $post['role_id'] = $role->id;

        $action = User::create($post->all());
        if($action){
            $permissions = DefaultPermission::where('role_id', $role->id)->get();
            foreach ($permissions as $key => $value) {
                UserPermission::insert([
                    'user_id' => $action->id,
                    'permission_id' => $value->permission_id
                ]);
            }
           
            if($type == 'seller' || $type == 'customer')
            {
                $otp = Myhelper::otp_get();
                DB::table('otps')
                ->insert([
                    'email' => $post->email,
                    'phone' => $post->mobile,
                    'otp' => $otp,
                    'is_active' => 1,
                    'expiry' => Carbon::now()->addMinutes(10)->format('Y-m-d h:i:s'), //Adding 10 mins as expiry
                    'created_at' => Carbon::now()->format('Y-m-d h:i:s'),
                ]);
                Mail::to($post->email)->send(new UserCreateMail($post->name, $post->email, $password));
                Mail::to($post->email)->send(new UserCreateOTPMail($post->name, $post->email, $otp));


            }

            return response()->json(['status' => 'User created successfully.'], 200);
        } else{
            return response()->json(['status' => 'Task failed. Please try again later.'], 400);
        }
    }

    public function changeaction(Request $post){
        switch ($post->role) {
            case 'admin':
                $rules = array(
                    'id' => 'required',
                );

                $permission = 'edit_admin';
            break;

            case 'bank':
                $rules = array(
                    'id' => 'required',
                );

                $permission = 'edit_bank';
            break;

            case 'customer':
                $rules = array(
                    'id' => 'required',
                );

                $permission = 'edit_customer';
            break;

            case 'seller':
                $rules = array(
                    'id' => 'required',
                );

                $permission = 'edit_seller';
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

        if(isset($permission) && !\Myhelper::can($permission)){
            return response()->json(['status' => 'Permission not Allowed'], 401);
        }

        switch ($post->role) {
            case 'admin':
            case 'bank':
            case 'seller':
            case 'customer':
                $user = User::findorfail($post->id);
                if($user->status){
                    $post['status'] = '0';
                } else{
                    $post['status'] = '1';
                }

                $action = User::updateorcreate(['id' => $post->id], $post->all());
            break;
        }

        if($action){
            return response()->json(['status' => 'Task successfully completed.'], 200);
        } else{
            return response()->json(['status' => 'Task failed. Please try again later'], 400);
        }
    }

    public function permission($id){
        $id = base64_decode($id);

        $user = User::findorfail($id);

        $data['activemenu'] = array(
            'main' => 'members',
            'sub' => $user->role->slug,
        );

        if(!\Myhelper::can('edit_'.$user->role->slug)){
            abort(401);
        }

        $data['default'] = UserPermission::where('user_id', $user->id)->pluck('permission_id')->toArray();

        $arr = Permission::select('type')->distinct()->get()->pluck('type');

        foreach ($arr as $key => $value) {
            $data['permissions'][$value] = Permission::where('type', $value)->where('role_id', 'LIKE', '%"'.$user->role_id.'"%')->get();
        }

        $data['user'] = $user;
        return view('dashboard.members.permissions', $data);
    }

    public function permissionsubmit(Request $post){
        $rules = array(
            'user_id' => 'required',
        );

        if(isset($rules)){
            $validator = \Validator::make($post->all(), $rules);
            if($validator->fails()){
                foreach($validator->errors()->messages() as $key => $value){
                    return response()->json(['status' => $value[0]], 400);
                }
            }
        }

        $oldatas = UserPermission::where('user_id', $post->user_id)->delete();

        if($post->permissions != ''){
            foreach ($post->permissions as $permission_id) {
                UserPermission::insert([
                    'user_id' => $post->user_id,
                    'permission_id' => $permission_id,
                ]);
            }
        }

        return response()->json(['status' => 'Task successfullly completed'], 200);
    }
}

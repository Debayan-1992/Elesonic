<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Role;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class FrontendNoAuthController extends Controller
{
    //After login for seller, customer this controller should be hit if hitting FrontendController then it'll keep looping
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

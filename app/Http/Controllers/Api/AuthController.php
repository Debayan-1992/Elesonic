<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function login(Request $post){
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $validator = \Validator::make($post->all(), $rules);
        if($validator->fails()){
            foreach($validator->errors()->messages() as $key => $value){
                return response()->json([ 'statuscode' => 400, 'status' => 'error', 'message' => $value[0] ]);
            }
        }

        $user = User::where('email', $post->email)->first();
        if(!$user){
            return response()->json([ 'statuscode' => 400, 'status' => 'error', 'message' => 'No account for this Email Address.' ]);
        }

        if(!in_array($user->role->slug, ['customer'])){
            return response()->json([ 'statuscode' => 401, 'status' => 'error', 'message' => 'Unauthorized Action.' ]);
        }

        if($user->status != '1'){
            return response()->json([ 'statuscode' => 400, 'status' => 'error', 'message' => 'Your account has been suspended. Contact our support team to unlock your account.' ]);
        }

        if(\Auth::validate(['email' => $user->email, 'password' => $post->password])){
            if($user->api_token == NULL){
                $user->api_token = \Str::random(60);
                $user->save();
            }

            return response()->json([
                'statuscode' => 200,
                'status' => 'success',
                'message' => 'Logedin Successfully.',
                'data' => [
                    'user' => $user
                ]
            ]);
        } else{
            return response()->json([ 'statuscode' => 400, 'status' => 'error', 'message' => 'The password you entered is invalid.' ]);
        }
    }

    public function getUser(){
        dd(\Auth::user());
    }
}

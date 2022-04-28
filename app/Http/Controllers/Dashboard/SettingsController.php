<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Setting;

class SettingsController extends Controller
{
    public function index(){
        $data['activemenu'] = array(
            'main' => 'settings',
            'sub' => 'settings'
        );

        $data['settings'] = Setting::findorfail(1);

        return view('dashboard.settings.index', $data);
    }

    public function submit(Request $post){
        $rules = array(
            'name' => 'required',
            'title' => 'required',
            'smsflag' => 'required|in:1,0',
        );

        if(isset($rules)){
            $validator = \Validator::make($post->all(), $rules);
            if($validator->fails()){
                foreach($validator->errors()->messages() as $key => $value){
                    return response()->json(['status' => $value[0]], 400);
                }
            }
        }

        $post['id'] = 1;

        $action = Setting::updateorcreate(['id' => $post->id], $post->all());
        if($action){
            return response()->json(['status' => 'Task successfully completed'], 200);
        } else{
            return response()->json(['status' => 'Task failed. Please try again later'], 400);
        }
    }
}

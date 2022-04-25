<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\LoanType;

class SetupController extends Controller
{
    public function index($type){
        $data['activemenu'] = array(
            'main' => 'setup'
        );

        switch ($type){
            case 'loantypes':
                $permission = "view_loantypes";
                $view = 'loantypes';
                $data['activemenu']['sub'] = 'loantypes';
            break;

            default:
                abort(404);
            break;
        }

        if(isset($permission) && !\Myhelper::can($permission)){
            abort(401);
        }

        return view('dashboard.setup.'.$view, $data);
    }

    public function submit(Request $post){
        switch($post->operation){
            case 'loantypenew':
                $rules = array(
                    'name' => 'required',
                );

                $permission = 'add_loantype';
            break;

            case 'loantypeedit':
                $rules = array(
                    'id' => 'required',
                    'name' => 'required',
                );

                $permission = 'edit_loantype';
            break;

            case 'loantypechangeaction':
                $rules = array(
                    'id' => 'required',
                );

                $permission = 'edit_loantype';
            break;

            case 'loantypedelete':
                $rules = array(
                    'id' => 'required',
                );

                $permission = 'delete_loantype';
            break;

            default:
                return response()->json(['status' => 'Invalid Request'], 400);
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
            return response()->json(['status' => 'Access Denied'], 401);
        }

        switch ($post->operation) {
            case 'loantypechangeaction':
                $content = LoanType::findorfail($post->id);
                if($content->status){
                    $post['status'] = '0';
                } else{
                    $post['status'] = '1';
                }
            case 'loantypenew':
            case 'loantypeedit':
                $action = LoanType::updateorcreate(['id' => $post->id], $post->all());
            break;

            case 'loantypedelete':
                $content = LoanType::findorfail($post->id);
                $action = $content->delete();
            break;
        }

        if($action){
            return response()->json(['status' => 'Task completed successfully'], 200);
        } else{
            return response()->json(['status' => 'Task failed. Please try again'], 400);
        }
    }
}

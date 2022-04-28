<?php

namespace App\Http\Controllers\Dashboard;

use App\Model\LandingBanner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Role;


class LandingBannerController extends Controller
{
    //
    public function index()
    {
        $data['activemenu'] = array(
            'main' => 'settings',
            'sub' => 'banners',
        );

        $data['banners'] = LandingBanner::all();
        $data['statuses'] = ['A', 'I', 'D'];

        return view('dashboard.banner.index', $data);
    }

    public function store(Request $request)
    {

        switch ($request->operation) {
            case 'new': //For create
                $rules = array(
                    'title' => 'required',
                    'description' => 'required',
                    'image' => 'required',
                    'status' => 'required',
                );
            break;

            case 'edit': //For edit
                $rules = array(
                    'id' => 'required',
                    'title' => 'required',
                    'description' => 'required',
                    'image' => 'required',
                    'status' => 'required',
                );
            break;
        }

        if(isset($rules)){
            $validator = \Validator::make($request->all(), $rules);
            if($validator->fails()){
                foreach($validator->errors()->messages() as $key => $value){
                    return response()->json(['status' => $value[0]], 400);
                }
            }
        }
        
        switch ($request->operation) {
            case 'edit':     
            //break; //Purposely doing fall through, uncomment to stop
            case 'new':

                $file=$request->file('image');
                $imgname=time().'_'.$file->getClientOriginalName();
                
                $action = LandingBanner::updateOrCreate(['id' => $request->id], [
                    'b_title' => $request->title,
                    'b_description' => $request->description,
                    'image' => $imgname,
                    'status' => $request->status,
                ]);
                $file->move(public_path('uploads/banners'),$imgname);
                //If id exist in request then update against id or create record by taking the 2nd param
            break;
        }

        if($action){
            return response()->json(['status' => 'Task completed successfully'], 200);
        } else{
            return response()->json(['status' => 'Task failed. Please try again'], 400);
        }
    }

    public function statusChange(Request $request){
        switch($request->type){
            case 'statusChange':
                $status = LandingBanner::findorfail($request->id);
                if($status->status == 'A' ){
                    $request['status'] = 'I';
                } else{
                    $request['status'] = 'A';
                }
                LandingBanner::where('id', $request->id)->update($request->except(['_token','type']));
            break;
            case 'delet':
                $request['status'] = 'D';
                LandingBanner::where('id', $request->id)->update($request->except(['_token','type']));
            break;
        }
    }
}

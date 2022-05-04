<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Service;

class ServiceController extends Controller
{
    //
    public function index()
    {
        $data['activemenu'] = array(
            'main' => 'services',
            'sub' => 'services',
        );

        $data['services'] = Service::all();
        $data['statuses'] = ['A', 'I', 'D'];

        return view('dashboard.service.index', $data);
    }

    public function store(Request $request)
    {
        switch ($request->operation) {
            case 'new': //For create
                $rules = array(
                    'name' => 'required',
                    'slug' => 'required',
                    'description' => 'required',
                    'image' => 'required',
                    'popular' => 'required',
                    
                );
            break;

            case 'edit': //For edit
                $rules = array(
                    'id' => 'required',
                    'name' => 'required',
                    'slug' => 'required',
                    'description' => 'required',
                    'popular' => 'required',
                  
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

               if(@$request->file('image')){
                $file = $request->file('image');
                
                $ext = substr(strrchr($file->getClientOriginalName(), '.'), 1);
                $new_name1 = str_replace(".", "", microtime());
                $new_name = str_replace(" ", "_", $new_name1);
                $filename = $new_name.'.'.$ext;

                if(\Image::make($file->getRealPath())->save('uploads/services/'.$filename)){
                    $imgname = $filename;
                } else{
                    return response()->json(['status' => 'File cannot be saved to server.'], 400);
                }
                $file->move(public_path('uploads/services'),$imgname);
            }else{
                $imgname = $request->hidimage;
            }
                
                $action = Service::updateOrCreate(['id' => $request->id], [
                    'name' => $request->name,
                    'slug' => strtolower(str_replace(' ', '_', $request->slug)),
                    'description' => $request->description,
                    'image' => @$imgname,
                    'popular' => $request->popular,
                    'status' => 'A',
                ]);
               
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
                $status = Service::findorfail($request->id);
                if($status->status == 'A' ){
                    $request['status'] = 'I';
                } else{
                    $request['status'] = 'A';
                }
                Service::where('id', $request->id)->update($request->except(['_token','type']));
            break;
            case 'popular_st_change':
                $status = Service::findorfail($request->id);
                if($status->popular == true ){
                    $request['popular'] = false;
                } else{
                    $request['popular'] = true;
                }
                Service::where('id', $request->id)->update($request->except(['_token','type']));
            break;
            case 'delet':
                $request['status'] = 'D';
                Service::where('id', $request->id)->update($request->except(['_token','type']));
            break;
        }
    }
}

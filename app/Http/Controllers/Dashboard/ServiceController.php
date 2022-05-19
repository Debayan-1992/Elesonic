<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\RequestServiceSubmitMail;
use App\Model\Service;
use App\Model\Service_booking;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

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

    public function r_service_index()
    {
        $data['activemenu'] = array(
            'main' => 'services',
            'sub' => 'request-service',
        );

        $data['services'] = Service_booking::leftJoin('services', 'service_booking.service_id', '=', 'services.id')->select('service_booking.id', 'services.name as service_name', 'service_booking.name', 'service_booking.email', 'service_booking.phone', 'service_booking.status', 'service_booking.created_at')->get();
        $data['statuses'] = ['A', 'I', 'D'];

        return view('dashboard.service.request_service_index', $data);
    }

    public function r_service_statusChange(Request $request)
    {
        switch($request->type){
            case 'delete': //In activating
                $request['service_acceptance_status'] = 'I';
                Service_booking::where('id', $request->id)->update($request->except(['_token','type']));
            break;
        }
    }
 
    public function r_service_submit(Request $request)
    {
        $rules = array(
            'serviceBookingId' => 'required|numeric',
            'service_offered_price' => 'required|numeric',
            'message' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules);
        if($validator->fails()){
            foreach($validator->errors()->messages() as $key => $value){
                return response()->json(['status' => $value[0]], 400);
            }
        }

        $service_booking = Service_booking::where('id', $request->serviceBookingId)->first();
        $mailFromId = config()->get('mail.from.address');
        $payment_link = URL::to('service_payment_form/?_tkn='.encrypt($service_booking->email.','.$request->serviceBookingId.','.$service_booking->service_id.','.$request->service_offered_price));
        Mail::to($service_booking->email)->send(new RequestServiceSubmitMail($service_booking->name, $mailFromId, $request->service_offered_price, $request->message, $payment_link));
        // $service_booking->acceptance_status = 'A';
        // $service_booking->service_request_acceptance_date = date('Y-m-d h:i:s');
        // $service_booking->save();
        $service_booking->update([
            'service_acceptance_status' => 'A',
            'message' => $request->message,
            'service_offered_price' => $request->service_offered_price,
            'service_request_acceptance_date' => date('Y-m-d h:i:s'),
        ]);

        return response()->json(['status' => 'Mail sent successfully'], 200);

    }

    // public function r_service_decrypt(Request $request)
    // {

    // }
}

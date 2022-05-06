<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\model\Brand;
use Carbon\Carbon;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sort_search = null;
        $data['activemenu']['sub'] = 'index';
        $data['activemenu']['main'] = 'products';
        return view('dashboard.brand.index', $data);
    }

      /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $brand = new Brand;
        $brand->name = $request->name;
        $brand->meta_title = $request->meta_title;
        $brand->meta_description = $request->meta_description;
        $brand->meta_keyword = $request->meta_keyword;
        if($request->file('brand_image')){
            $file = $request->file('brand_image');
            $filename = Carbon::now()->timestamp.'_'.$file->getClientOriginalName();

            if(\Image::make($file->getRealPath())->save('uploads/brands/'.$filename)){
                $brand->logo = $filename;
            } else{
                return response()->json(['status' => 'File cannot be saved to server.'], 400);
            }
        }
        $brand->save();
        return response()->json(['status' => 'Brand added'], 200);
    }



    public function statusChange(Request $request){
        switch($request->type){
            case 'statusChange':
                $status = Brand::findorfail($request->id);
                if($status->status == 'A' ){
                    $request['status'] = 'I';
                } else{
                    $request['status'] = 'A';
                }
                Brand::where('id', $request->id)->update($request->except(['_token','type']));
            break;
            case 'delete':
                $request['status'] = 'D';
                $brand = Brand::findorfail($request->id);
                @unlink(config('app.url').'/uploads/brands/'.$brand->logo);
                Brand::where('id', $request->id)->update($request->except(['_token','type']));
            break;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $data['activemenu']['main'] = 'products';
        $data['activemenu']['sub'] = 'edit';
        $id =  request()->segment(5);
        $brand = Brand::findOrFail($id);
        $data['brand'] = $brand;
        return view('dashboard.brand.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = $request->id;
        $brand = Brand::findOrFail($id);
        $brand->name = $request->name;
        $brand->meta_title = $request->meta_title;
        $brand->meta_description = $request->meta_description;
        $brand->meta_keyword = $request->meta_keyword;
        if($request->file('brand_image') != NULL){
            $deletefile = 'uploads/blogs/'.$brand->logo;
        }
        if($request->file('brand_image') != ""){
            $file = $request->file('brand_image');
            $filename = Carbon::now()->timestamp.'_'.$file->getClientOriginalName();

            if(\Image::make($file->getRealPath())->save('uploads/brands/'.$filename)){
                @unlink(config('app.url').'/uploads/brands/'.$brand->logo);
                $brand->logo = $filename;
               
            } else{
                return response()->json(['status' => 'File cannot be saved to server.'], 400);
            }
        }
        $brand->save();
        return response()->json(['status' => 'Brand updated'], 200);
    }
}

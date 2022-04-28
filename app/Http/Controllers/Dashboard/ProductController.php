<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\model\Product;
use Carbon\Carbon;
use App\model\Category;
use App\model\Brand;
use Auth;
class ProductController extends Controller
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
        return view('dashboard.product.index', $data);
    }

    function create(){
        $data['activemenu']['sub'] = 'index';
        $data['activemenu']['main'] = 'products';
        $category = Category::where('status','A')
        ->get();
        $brand = Brand::where('status','A')
        ->get();
        $data['categories'] = $category;
        $data['brands'] = $brand;
        return view('dashboard.product.create', $data);
    }

      /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { 
        $product = new Product;
        $product->name = $request->name;
        $product->category_id  = $request->category_id;
        $product->brand_id  = $request->brand_id;
        if($request->net_price == ""){
            $request->net_price = $request->mrp - ($request->mrp * $request->discount)/100;
        }else{
            $request->net_price = $request->net_price;
        }
        $request->net_price = number_format((float)$request->net_price, 2, '.', '');
        $product->unit_price  = $request->net_price;
        $product->purchase_price  = $request->mrp;
        $product->discount  = $request->discount;
        $product->meta_title = $request->meta_title;
        $product->meta_description = $request->meta_description;
        $product->meta_keyword = $request->meta_keyword;
        $product->user_id = 1;
        $chkSlug = Product::where('slug',$request->slug)->count();
        if($chkSlug > 0){
            return response()->json(['status' => 'Slug exist'], 400);
        }
        if ($request->slug != null) {
            $product->slug = $request->slug;
        } else {
            $product->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)) . '-' . Str::random(5);
        }
        if($request->file('image')){
            $file = $request->file('image');
            $filename = Carbon::now()->timestamp.'_'.$file->getClientOriginalName();

            if(\Image::make($file->getRealPath())->save('uploads/products/'.$filename)){
                $product->photos = $filename;
            } else{
                return response()->json(['status' => 'File cannot be saved to server.'], 400);
            }
        }
        $product->save();
        return response()->json(['status' => 'Product added'], 200);
    }



    public function statusChange(Request $request){
        switch($request->type){
            case 'statusChange':
                $status = Product::findorfail($request->id);
                if($status->status == 'A' ){
                    $request['status'] = 'I';
                } else{
                    $request['status'] = 'A';
                }
                Product::where('id', $request->id)->update($request->except(['_token','type']));
            break;
            case 'delete':
                $request['status'] = 'D';
                $brand = Product::findorfail($request->id);
                Product::where('id', $request->id)->update($request->except(['_token','type']));
            break;
            case 'popular':
                $status = Product::findorfail($request->id);
                if($status->ispopular == 'Y' ){
                    $request['ispopular'] = 'N';
                } else{
                    $request['ispopular'] = 'Y';
                }
                Product::where('id', $request->id)->update($request->except(['_token','type']));
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
        $id =  request()->segment(4);
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

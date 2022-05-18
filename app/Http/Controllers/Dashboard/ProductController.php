<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Product;
use Carbon\Carbon;
use App\Model\Category;
use App\Model\Brand;
use Auth;
use App\Model\Product_related_images;
use App\Utility\CategoryUtility;
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
        $categories = Category::where('parent_id', 0)->where('status','A')
        ->with('childrenCategories')
        ->get();
        $brand = Brand::where('status','A')
        ->get();
        $data['categories'] = $categories;
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
        $product->quantity  = $request->quantity;
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
        $product->user_id = Auth()->user()->id;
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
            
            $ext = substr(strrchr($file->getClientOriginalName(), '.'), 1);
            $new_name1 = str_replace(".", "", microtime());
            $new_name = str_replace(" ", "_", $new_name1);
            $filename = $new_name.'.'.$ext;

            if(\Image::make($file->getRealPath())->save('uploads/products/'.$filename)){
                $product->photos = $filename;
            } else{
                return response()->json(['status' => 'File cannot be saved to server.'], 400);
            }
        }
       
        $product->description = $request->prodescription;
        $product->save();
        $multiImages = $request->file('related_image');
        if($multiImages){
           
            for($i=0;$i<count($multiImages);$i++){
                $Product_related_images = new Product_related_images;
                $ext = substr(strrchr($multiImages[$i]->getClientOriginalName(), '.'), 1);
                $new_name1 = str_replace(".", "", microtime());
                $new_name = str_replace(" ", "_", $new_name1);
                $multifilename = $new_name.'.'.$ext;
                if(\Image::make($multiImages[$i]->getRealPath())->save('uploads/products/'.$multifilename)){
                $Product_related_images->image = $multifilename;
                $Product_related_images->product_id = $product->id;
                $Product_related_images->save();
                }
            }
        }
        return response()->json(['status' => 'Product added'], 200);
    }

    function imageDelete(Request $request){
        $id = $request->id;
        if($id){
            $res=Product_related_images::where('id',$id)->delete();
        }
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
            case 'best':
                $status = Product::findorfail($request->id);
                if($status->isbest == 'Y' ){
                    $request['isbest'] = 'N';
                } else{
                    $request['isbest'] = 'Y';
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
        $id =  request()->segment(5);
        $product = Product::findOrFail($id);
        $categories = Category::where('parent_id', 0)->where('status','A')
        ->with('childrenCategories')
        ->get();

        $categories = Category::where('parent_id', 0)
        ->with('childrenCategories')
        ->get();

        $multiImage = Product_related_images::where('product_id', $id)
        ->get();
 
        $brand = Brand::where('status','A')
        ->get();
        $data['categories'] = $categories;
        $data['brands'] = $brand;
        $data['product'] = $product;
        $data['multiImage'] = $multiImage;
        return view('dashboard.product.edit', $data);
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
        $product = Product::findOrFail($id);
        $product->name = $request->name;
        $product->category_id  = $request->category_id;
        $product->brand_id  = $request->brand_id;
        $product->quantity  = $request->quantity;
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
        $product->user_id = Auth()->user()->id;
        $chkSlug = Product::where('slug',$request->slug)->count();
        
        if ($request->slug != null) {
            $product->slug = $request->slug;
        } else {
            $product->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)) . '-' . Str::random(5);
        }
       
        if($request->file('image')){
            $file = $request->file('image');
            
            $ext = substr(strrchr($file->getClientOriginalName(), '.'), 1);
            $new_name1 = str_replace(".", "", microtime());
            $new_name = str_replace(" ", "_", $new_name1);
            $filename = $new_name.'.'.$ext;

            if(\Image::make($file->getRealPath())->save('uploads/products/'.$filename)){
                $product->photos = $filename;
            } else{
                return response()->json(['status' => 'File cannot be saved to server.'], 400);
            }
        }
        $product->description = $request->description;
        $product->save();
        $multiImages = $request->file('related_image');
        if($multiImages){
           
            for($i=0;$i<count($multiImages);$i++){
                $Product_related_images = new Product_related_images;
                $ext = substr(strrchr($multiImages[$i]->getClientOriginalName(), '.'), 1);
                $new_name1 = str_replace(".", "", microtime());
                $new_name = str_replace(" ", "_", $new_name1);
                $multifilename = $new_name.'.'.$ext;
                if(\Image::make($multiImages[$i]->getRealPath())->save('uploads/products/'.$multifilename)){
                $Product_related_images->image = $multifilename;
                $Product_related_images->product_id = $product->id;
                $Product_related_images->save();
                }
            }
        }
        return response()->json(['status' => 'Product updated'], 200);
    }
}

<?php

namespace App\Http\Controllers\Dashboard;
use App\Model\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Utility\CategoryUtility;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $data['activemenu']['sub'] = 'index';
        $data['activemenu']['main'] = 'products';
        $categories = Category::where('parent_id', 0)->where('status','A')
            ->with('childrenCategories')
            ->get();
        $data['categories'] = $categories;
       
        return view('dashboard.category.index', $data);
    }
   
 
   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $category = new Category;
        $category->name = $request->name;
        $category->description = $request->description;
        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;
        $category->meta_keyword = $request->meta_keyword;
        $chkSlug = Category::where('slug',$request->slug)->count();
        if($chkSlug > 0)
        {
            return response()->json(['status' => 'Slug exist'], 400);
        }

      
        if ($request->parent_id != "0") {
            $category->parent_id = $request->parent_id;
            $parent = Category::find($request->parent_id);
            $category->level = $parent->level + 1;
        }
        if ($request->slug != null) {
            $category->slug = $request->slug;
        } else {
            $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)) . '-' . Str::random(5);
        }
       
        $category->save();
       
        return response()->json(['status' => 'category added'], 200);
    }



    public function statusChange(Request $request){
        switch($request->type){
            case 'statusChange':
                $status = Category::findorfail($request->id);
                if($status->status == 'A' ){
                    $request['status'] = 'I';
                } else{
                    $request['status'] = 'A';
                }
                Category::where('id', $request->id)->update($request->except(['_token','type']));
            break;
            case 'delete':
                $request['status'] = 'D';
                Category::where('id', $request->id)->update($request->except(['_token','type']));
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
        $category = Category::findOrFail($id);
        $categories = Category::where('parent_id', 0)->where('status','A')
            ->with('childrenCategories')
            ->whereNotIn('id', CategoryUtility::children_ids($category->id, true))->where('id', '!=', $category->id)
            ->get();
        $data['categories'] = $categories;
        $data['category'] = $category;
      
        return view('dashboard.category.edit', $data);
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
        $category = Category::findOrFail($id);
        $category->name = $request->name;
        $category->description = $request->description;
        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;
        $category->meta_keyword = $request->meta_keyword;
        if ($request->parent_id != "0") {
            $category->parent_id = $request->parent_id;
            $parent = Category::find($request->parent_id);
            $category->level = $parent->level + 1;
        }else{
            $category->parent_id = $request->parent_id;
        }
        if ($request->slug != null) {
            $category->slug = $request->slug;
        } else {
            $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)) . '-' . Str::random(5);
        }
        $category->save();
        return response()->json(['status' => 'category updated'], 200);
    }
}

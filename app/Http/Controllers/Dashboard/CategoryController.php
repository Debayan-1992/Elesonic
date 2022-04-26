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
        $data['activemenu']['sub'] = 'category';
        $data['activemenu']['main'] = 'category';
        $categories = Category::where('parent_id', 0)
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
        $cat = Category::findorfail($request->id);
        if($cat->status == 'A' ){
            $request['status'] = 'I';
        } else{
            $request['status'] = 'A';
        }

        $action = Category::where('id', $request->id)->update($request->except(['_token','type']));
    }
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $id =  request()->segment(4);
        $category = Category::findOrFail($id);
        $categories = Category::where('parent_id', 0)
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
    public function update(Request $request, $id)
    {
        echo "ok";exit;
        $category = Category::findOrFail($id);
        if ($request->lang == env("DEFAULT_LANGUAGE")) {
            $category->name = $request->name;
        }
        $category->digital = $request->digital;
        $category->banner = $request->banner;
        $category->icon = $request->icon;
        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;
        $category->meta_keyword = $request->meta_keyword;
        $category->meta_og = $request->meta_og;
        $category->app_banner = $request->app_banner;
        $category->cat_size = $request->size_chart;
        $category->gradientimage = $request->gradientimage;
        $category->bannerone = $request->bannerone;
        $category->bannertwo = $request->bannertwo;
        $category->bannerthree = $request->bannerthree;
        $category->bannerfour = $request->bannerfour;
        $category->feature_image = $request->feature_image;
        $category->banneronelink = $request->banneronelink;
        $category->bannertwolink = $request->bannertwolink;
        $category->bannerthreelink = $request->bannerthreelink;
        $category->bannerfourlink = $request->bannerfourlink;
        // dd($request->all());
        if ($request->parent_id != "0") {
            $category->parent_id = $request->parent_id;
            $parent = Category::find($request->parent_id);
            $category->level = $parent->level + 1;
        }
        else
        {
             $category->parent_id = $request->parent_id;
        }



        if ($request->banner == "" || $request->icon == "" || $request->feature_image=="") {
            flash(translate('Banner/Icon/Feature Image required'))->error();
            return back();
        }
        if ($request->slug != null) {
            $category->slug = strtolower($request->slug);
        } else {
            $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)) . '-' . Str::random(5);
        }
        if ($request->commision_rate != null) {
            $category->commision_rate = $request->commision_rate;
        }
        $category->save();
        Categoryspecification::where('category_id', $id)->delete();
        if (!empty($request->specification) && isset($request->specification[0]) && !empty($request->specification[0])) {
            foreach (json_decode($request->specification[0]) as $key => $specifications) {
                $category_specification = new Categoryspecification;
                $category_specification->specification = $specifications->value;
                $category_specification->category_id = $category->id;
                $category_specification->save();
            }
        }
        $category_translation = CategoryTranslation::firstOrNew(['lang' => $request->lang, 'category_id' => $category->id]);
        $category_translation->name = $request->name;
        $category_translation->save();
        flash(translate('Category has been updated successfully'))->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        // Category Translations Delete
        // foreach ($category->category_translations as $key => $category_translation) {
        //     $category_translation->delete();
        // }
        // foreach (Product::where('category_id', $category->id)->get() as $product) {
        //     $product->category_id = null;
        //     $product->save();
        // }
        // CategoryUtility::delete_category($id);
        flash(translate('Category has been deleted successfully'))->success();
        return redirect()->route('categories.index');
    }

    public function restoreCategory($id)
    {
        $category = Category::withTrashed()->where('id', $id)->first();
        if (!$category) {
            abort(404);
        }
        
        $category->restore();

        flash(translate('Category has been restore successfully'))->success();
        return redirect()->back();
    }

    public function updateFeatured(Request $request)
    {
        $category = Category::findOrFail($request->id);
        $category->featured = $request->status;
        if ($category->save()) {
            return 1;
        }
        return 0;
    }

    public function updateHomeFeatured(Request $request)
    {
        $category = Category::findOrFail($request->id);
        $category->home_featured = $request->status;
        if ($category->save()) {
            return 1;
        }
        return 0;
    }

    public function updateStatus(Request $request)
    {
        $category = Category::findOrFail($request->id);
        $category->status = $request->status;
        if ($category->save()) {
            return 1;
        }
        return 0;
    }
}

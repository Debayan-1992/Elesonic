<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Categoryspecification;
use App\HomeCategory;
use App\Product;
use App\Language;
use App\CategoryTranslation;
use App\Utility\CategoryUtility;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        echo 'okk';
        // $sort_search = null;
        // $categories = Category::orderBy('name', 'asc');
        // if ($request->has('search')) {
        //     $sort_search = $request->search;
        //     $categories = $categories->where('name', 'like', '%' . $sort_search . '%');
        // }
        // // $categories = $categories->paginate(15);
        // $categories = $categories->get();
        // return view('backend.product.categories.index', compact('categories', 'sort_search'));
    }
     public function indexajax(Request $request)
    {
        $sort_search = null;
        $condition=[];
        $categories = Category::orderBy('name', 'asc');
        if (!empty($request->search['value'])) {
            $sort_search =$request->search['value'];
            $condition[]=['name','like','%' . $sort_search . '%'];
        }
      
       $start=$request->start;
       $end=$request->length;
       $total=$categories->where($condition)->get()->count();
       $categories = $categories->where($condition)->offset($start)->limit($end)->get();
       $sl_no=$start+1;
       foreach ($categories as $category)
       {
           $parent =Category::where('id', $category->parent_id)->first();
           $getChild =Category::where('parent_id', $category->id)->count();
           $getProduct =Product::where('category_id', $category->id)->count();
           $banner=!is_null($category->banner)?'<img src="'.uploaded_asset($category->banner).'" alt="'.translate('Banner').'" class="h-50px">':'—';
           $icon=!is_null($category->icon)?'<span class="avatar avatar-square avatar-xs">
                                            <img src="'.uploaded_asset($category->icon).'" alt="'.translate('icon').'">
                                        </span>':' —';
           $featured_checked=$category->featured?'checked':'';
           $featured='<label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="checkbox" onchange="update_featured(this)" value="'.$category->id.'" '.$featured_checked.'>
                                        <span></span>
                                    </label>';
           $home_featured_checked=$category->home_featured?'checked':'';
           $home_featured='<label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="checkbox" onchange="update_home_featured(this)" value="'.$category->id.'" '.$home_featured_checked.' >
                                        <span></span>
                                    </label>';
           $status_checked=$category->status?'checked':'';
           $status='<label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="checkbox" onchange="update_status(this)" value="'.$category->id.'" '.$status_checked.'>
                                        <span></span>
                                    </label>';
           $edit_link=route('categories.edit', ['id'=>$category->id, 'lang'=>env('DEFAULT_LANGUAGE')] );
           $delete_link=route('categories.destroy', $category->id);

           if($getChild > 0)
           {
            $action='<a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="'.$edit_link.'" title="'.translate('Edit').'">
                                        <i class="las la-edit"></i>
                                    </a>
                                  '; 
           }
           elseif($getProduct > 0)
           {
             $action='<a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="'.$edit_link.'" title="'.translate('Edit').'">
                                        <i class="las la-edit"></i>
                                    </a>
                                  '; 
           }
           else
           {
            $action='<a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="'.$edit_link.'" title="'.translate('Edit').'">
                                        <i class="las la-edit"></i>
                                    </a>
                                    <a onclick="return confirm("Are you sure to delete?")" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" href="'.$delete_link.'" title="'.translate('Delete').'">
                                        <i class="las la-trash"></i>
                                    </a>'; 
           }
           
           $data[]=array(
           $sl_no,$category->getTranslation('name'),!is_null($parent)?$parent->getTranslation('name'):'—', $category->slug,$banner,$icon,$featured,$home_featured,$status,
           number_format($category->commision_rate).' %',$action    
           );
           $sl_no++;
       }
       $response=array('recordsTotal'=>$total,'recordsFiltered'=>$total,'draw'=>$request->draw,'data'=>!empty($data)?$data:array());
       die(json_encode($response));
       

    }
    public function trashedCategories(Request $request)
    {
        $categories = Category::onlyTrashed()->orderBy('name', 'asc')->get();
        return view('backend.product.categories.trashed', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::where('parent_id', 0)
            ->with('childrenCategories')
            ->get();
        return view('backend.product.categories.create', compact('categories'));
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
        $category->digital = $request->digital;
        $category->banner = $request->banner;
        $category->app_banner = $request->app_banner;
        $category->icon = $request->icon;
        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;
        $category->meta_keyword = $request->meta_keyword;
        $category->meta_og = $request->meta_og;
        $category->cat_size = $request->size_chart;


        $category->feature_image = $request->feature_image;
        $category->gradientimage = $request->gradientimage;
        $category->bannerone = $request->bannerone;
        $category->bannertwo = $request->bannertwo;
        $category->bannerthree = $request->bannerthree;
        $category->bannerfour = $request->bannerfour;

        $category->banneronelink = $request->banneronelink;
        $category->bannertwolink = $request->bannertwolink;
        $category->bannerthreelink = $request->bannerthreelink;
        $category->bannerfourlink = $request->bannerfourlink;

        $chkSlug = Category::where('slug',$request->slug)->count();


        if($chkSlug > 0)
        {
        	flash(translate('Slug should be unique'))->error();
            return redirect()->route('categories.create');
        }

        if ($request->banner == "" || $request->icon == "" || $request->feature_image=="") {
            flash(translate('Banner/Icon/Feature Image required'))->error();
            return redirect()->route('categories.create');
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
        if ($request->commision_rate != null) {
            $category->commision_rate = $request->commision_rate;
        }
        /* $tags = array();*/
        $category->save();
        if (!empty($request->specification) && @$request->specification[0] != "") {
            foreach (json_decode(@$request->specification[0]) as $key => $specifications) {
                $category_specification = new Categoryspecification;
                $category_specification->specification = $specifications->value;
                $category_specification->category_id = $category->id;
                $category_specification->save();
            }
        }
        $category_translation = CategoryTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'category_id' => $category->id]);
        $category_translation->name = $request->name;
        $category_translation->save();
        flash(translate('Category has been inserted successfully'))->success();
        return redirect()->route('categories.index');
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
        $lang = $request->lang;
        $category = Category::findOrFail($id);
        //echo "<pre>"; print_r($category);exit;
        $categoryspecification = Categoryspecification::where('category_id', $id)->get();
        $categories = Category::where('parent_id', 0)
            ->with('childrenCategories')
            ->whereNotIn('id', CategoryUtility::children_ids($category->id, true))->where('id', '!=', $category->id)
            ->get();
        return view('backend.product.categories.edit', compact('category', 'categories', 'lang', 'categoryspecification'));
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

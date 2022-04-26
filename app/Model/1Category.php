<?php
namespace App\model;

use Illuminate\Database\Eloquent\Model;




class Category extends Model
{
   

    protected $appends = ['iconpath','gradientimagepath','featureimagepath'];
    protected $table   = 'categories';

    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $category_translation = $this->hasMany(CategoryTranslation::class)->where('lang', $lang)->first();
        return $category_translation != null ? $category_translation->$field : $this->$field;
    }

    public function category_translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function productswhthcategory()
    {
        return $this->hasMany(Product::class, 'category_id')->select(['id', 'name', 'thumbnail_img', 'category_id', 'discount', 'discount_type', 'published'])->where('published', '1');
    }

    public function classified_products()
    {
        return $this->hasMany(CustomerProduct::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function childrenCategories()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('categories');
    }
    public function childrenCategoriesNew()
    {
        return $this->hasMany(Category::class, 'parent_id')->where('status',1)->with('categories');
    } 
    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id')->select(['id', 'parent_id', 'name', 'icon']);
    }

    public function getIconpathAttribute()
    {
        return uploaded_asset($this->icon);
    }

    public function getFeatureimagepathAttribute()
    {
        return uploaded_asset($this->feature_image);
    }

     public function getgradientimagepathAttribute()
    {
        return uploaded_asset($this->gradientimage);
    }

    public function categoryattribute()
    {
        return belogsTo(Categoryattribute::class);
    }

    public function abc()
    {
    }

    public function childs()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id')->select(['id', 'name', 'level']);
    }
}

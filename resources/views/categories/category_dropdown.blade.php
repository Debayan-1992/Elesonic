@foreach ($categories as $category)

<?php echo trim($category->id.'=>'.$category->name); ?>,@foreach ($category->childrenCategories as $childCategory)@include('categories.child_category_array', ['child_category' => $childCategory])

@endforeach



@endforeach
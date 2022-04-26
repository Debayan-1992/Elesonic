@php

    $txt='';

    $value = null;

    for ($i=0; $i < $child_category->level; $i++){

        $value .= '--';

    }

@endphp

<?php echo $txt.=trim($child_category->id.'=>'.$value.' '.$child_category->name.',') ?>@if ($child_category->categories)@foreach ($child_category->categories as $childCategory)@include('categories.child_category_array', ['child_category' => $childCategory])@endforeach

@endif


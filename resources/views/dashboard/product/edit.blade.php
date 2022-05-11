<?php error_reporting(0) ?>
@section('pageheader', 'Edit Product')
@extends('layouts.app')
<style>
.imageThumb {
  max-height: 75px;
  border: 2px solid;
  padding: 1px;
  cursor: pointer;
}
.pip {
  display: inline-block;
  margin: 10px 10px 0 0;
}
.existingremove{
  display: block;
  background: #444;
  border: 1px solid black;
  color: white;
  text-align: center;
  cursor: pointer;
}
.remove {
  display: block;
  background: #444;
  border: 1px solid black;
  color: white;
  text-align: center;
  cursor: pointer;
}
.remove:hover {
  background: white;
  color: black;
}
</style>
@section('content')
    <section class="content-header">
        <h1>
            Product Management 
            <small>Edit</small>
        </h1>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Edit product</h3>

                <div class="box-tools pull-right">
                    {{-- Tools --}}
                </div>
            </div>
            <form action="{{route('dashboard.product.update')}}" method="POST" id="productform">
                <input type="hidden" value="{{$product->id}}" name="id">
                <div class="box-body">
                    @csrf

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" value="{{$product->name}}" class="form-control" placeholder="Title"  name="name">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Slug <span class="text-danger">*</span></label>
                            <input type="text" value="{{$product->slug}}"  onkeyup="slugname(this.value)" id="slug" class="form-control" placeholder="Slug"  name="slug">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Image <span class="text-danger">*</span></label>
                            <input type="file" accept="image/*" class="form-control" name="image">
                            @if($product->photos != "")
                            <img src="{{config('app.url').'/uploads/products/'.$product->photos}}" height="50px" width="50px">
                            @endif
                           
                        </div>
                        <div class="form-group col-md-6">
                            <label>Related Images (use ctrl+select)</label>
                            <input type="file" id="files" accept="image/*" class="form-control" name="related_image[]" multiple>
                           @if(!empty($multiImage))
                            @foreach($multiImage as $images)
    
                            <span class="pip">
                                <img height="50px" width="50px" class="imageThumb" src="{{config('app.url').'/uploads/products/'.$images->image}}">
                                <br><span style="cursor:pointer" class="existingremove" onclick="removeExistingImg({{ $images->id }})">Remove</span>
                            </span>
                            @endforeach
                           @endif
                        </div>
                         <div class="form-group col-md-6">
                            <label>Category <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="category_id" id="category_id" data-selected="{{ $product->category_id }}" data-live-search="true" required>
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ ( $category->id == $product->category_id) ? 'selected' : '' }}>{{ $category->name }}</option>
                                @foreach ($category->childrenCategories as $childCategory)
                                @include('categories.child_category', ['child_category' => $childCategory,'cat_id' =>$product->category_id])
                                @endforeach
                                @endforeach
                            </select>
                        </div>
                         <div class="form-group col-md-6">
                            <label>Brand <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="brand_id" data-toggle="select2" data-placeholder="Choose ..." data-live-search="true">
                                <option value="">Choose Brand</option>
                                @foreach($brands as $row)
                                <option value="{{$row->id}}" {{ ( $row->id == $product->brand_id) ? 'selected' : '' }}>{{$row->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>MRP ($) <span class="text-danger">*</span></label>
                            <input type="text" value="{{$product->purchase_price}}" onkeyup="calculatePrice(this.value)" placeholder="MRP" class="form-control" id="mrp" name="mrp">
                           
                        </div>
                        <div class="form-group col-md-6">
                            <label>Discount <span class="text-danger">*</span></label>
                            <input value="0" value="{{$product->discount}}" onkeyup="calculatePrice(this.value)" type="text" placeholder="Discount (%)" class="form-control" id="discount" name="discount">
                           
                        </div>
                        <div class="form-group col-md-6">
                            <label>Net Price ($) <span class="text-danger">*</span></label>
                            <input type="text" value="{{$product->unit_price}}" readonly="" placeholder="Net Price" class="form-control" id="net_price" name="net_price">
                           
                        </div>
                        <div class="form-group col-md-6">
                            <label>Quantity <span class="text-danger">*</span></label>
                            <input type="number" value="{{$product->quantity}}" class="form-control" placeholder="Quantity"  name="quantity">
                        </div>
                        <div class="form-group col-md-6">
                            <label> Description </label>
                            <textarea class="description form-control"  name="description">{{$product->description}}</textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Meta Title </label>
                            <input type="text" value="{{$product->meta_title}}" class="form-control"  placeholder="Meta title"  name="meta_title">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Meta keyword </label>
                            <input type="text" value="{{$product->meta_keyword}}" class="form-control" placeholder="Meta Keyword"  name="meta_keyword">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Meta Description </label>
                            <input type="text" value="{{$product->meta_description}}" class="form-control" placeholder="Meta Description"  name="meta_description">
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-md btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('script')
    <script src="//cloud.tinymce.com/stable/tinymce.min.js"></script>
    <script src="//unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        tinymce.init({
            selector:'textarea.description',
            width: 900,
            height: 100
        });
    </script>
    <script>
        $(document).ready(function() {
        if (window.File && window.FileList && window.FileReader) {
            $("#files").on("change", function(e) {
            var files = e.target.files,
                filesLength = files.length;
            for (var i = 0; i < filesLength; i++) {
                var f = files[i]
                var fileReader = new FileReader();
                fileReader.onload = (function(e) {
                var file = e.target;
                $("<span class=\"pip\">" +
                    "<img height=\"50px\" width=\"50px\" class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
                    "<br/><span style=\"cursor:pointer\" class=\"remove\">Remove</span>" +
                    "</span>").insertAfter("#files");
                 $(".remove").click(function(){
                     $(this).parent(".pip").remove();
                 });
                });
                fileReader.readAsDataURL(f);
            }
            });
        } else {
            alert("Your browser doesn't support")
        }
        });
        function removeExistingImg(id){
            swal({
			 title: "Are you sure?",
			 text: "Existing image will be deleted permanantly",
			 icon: "warning",
			 buttons: true,
			 dangerMode: true,
			})
			.then((willDelete) => {
			if (willDelete) {
                Pace.track(function(){
                    $.ajax({
                            url: "{{route('dashboard.product.imageDelete')}}",
                            method: "POST",
                            data: {'_token':'{{csrf_token()}}','type':'delete','id':id},
                            success: function(data){
                                location.reload();
                            }, error: function(errors){
                                showErrors(errors);
                            }
                        });
                    });       // submitting the form when user press yes
				} else {
				  }
				});
        }
        function slugname(value){
            var value = value;
            var new_value =value.toLowerCase();
            var new_value =new_value.replace(/ /g, "-");
            $("#slug").val(new_value);
        }
        function calculatePrice(){
            var mrp      = $("#mrp").val();
            var discount = $("#discount").val();
            console.log(discount);
            var net = parseFloat(mrp) - (parseFloat(mrp)*parseFloat(discount))/100;
            $("#net_price").val(net.toFixed(2));
        }
        function setInputFilter(textbox, inputFilter) {
        ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event) {
            textbox.addEventListener(event, function() {
            if (inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            } else {
                this.value = "";
            }
            });
        });
        }
        setInputFilter(document.getElementById("mrp"), function(value) {
         return /^-?\d*[.,]?\d*$/.test(value); });
        setInputFilter(document.getElementById("discount"), function(value) {
         return /^-?\d*[.,]?\d*$/.test(value); });
        $('#productform').validate({
            rules: {
                name: {
                    required: true,
                },
                slug: {
                    required: true,
                },
                category_id: {
                    required: true,
                },
                brand_id: {
                    required: true,
                },
                mrp: {
                    required: true,
                },
                discount: {
                    required: true,
                },
                net_price: {
                    required: true,
                },
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                if ( element.prop("tagName").toLowerCase() === "select" ) {
                    error.insertAfter( element.closest( ".form-group" ).find(".select2") );
                } else {
                    error.insertAfter( element );
                }
            },
            submitHandler: function() {
                for (instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }

                var form = $('#productform');

                Pace.track(function(){
                    form.ajaxSubmit({
                        dataType:'json',
                        beforeSubmit:function(){
                            form.find('button[type="submit"]').button('loading');
                        },
                        success:function(data){
                            notify(data.status, 'success');
                            form.find('button[type="submit"]').button('reset');

                            @if(!isset($blog))
                                location.reload();
                            @endif
                        },
                        error: function(errors) {
                            form.find('button[type="submit"]').button('reset');
                            showErrors(errors, form);
                        }
                    });
                });
            }
        });
    </script>
@endpush

<?php error_reporting(0) ?>
@section('pageheader', 'Edit Category')
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Category Management
            <small>Edit</small>
        </h1>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Edit category</h3>

                <div class="box-tools pull-right">
                    {{-- Tools --}}
                </div>
            </div>
            <form action="{{route('dashboard.category.update')}}" method="POST" id="categoryform">
                <div class="box-body">
                    @csrf
                    <input type="hidden" name="id" value="{{$category->id}}">
                 <!-- <input type="text" value="{{ $category->parent_id }}"> -->

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control"  name="name" value="{{isset($category) ? $category->name : ''}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control"  name="slug" value="{{isset($category) ? $category->slug : ''}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Parent </label>
                            <select class="select2 form-control" name="parent_id" data-toggle="select2" data-placeholder="Choose ..."data-live-search="true" data-selected="{{ $category->parent_id }}">

                                @if($category->parent_id == 0)
                                <option value="0">No Parent</option>
                                @endif
                          
                            @foreach ($categories as $categorych)

                            <option value="{{ $categorych->id }}" {{ ( $categorych->id == $category->parent_id) ? 'selected' : '' }}>{{ $categorych->name }}</option>

                            @foreach ($categorych->childrenCategories as $childCategory)

                                @include('categories.child_category', ['child_category' => $childCategory])

                            @endforeach

                            @endforeach

                        </select>
                        </div>
                    
                        <div class="form-group col-md-12">
                            <label>Description <span class="text-danger">*</span></label>
                            <textarea class="form-control"  name="description">{!! isset($category) ? $category->description : '' !!}</textarea>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                      
                        <div class="form-group col-md-6">
                            <label>Meta Title</label>
                            <textarea class="form-control" name="meta_title" >{{isset($category) ? $category->meta_title : ''}}</textarea>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Meta Description</label>
                            <textarea class="form-control" name="meta_description" >{{isset($category) ? $category->meta_description : ''}}</textarea>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Meta Keywords</label>
                            <textarea class="form-control" name="meta_keywords">{{isset($category) ? $category->meta_keyword : ''}}</textarea>
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
    <script>
        $('#categoryform').validate({
            rules: {
                name: {
                    required: true,
                },
                slug: {
                    required: true,
                },
                description: {
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

                var form = $('#categoryform');

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

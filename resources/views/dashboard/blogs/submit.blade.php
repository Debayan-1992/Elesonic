<?php error_reporting(0) ?>
@section('pageheader', 'Blogs')
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Blogs Management
            <small>{{isset($blog) ? 'Edit' : 'Add New'}}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{route('dashboard.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class=""><a href="{{route('dashboard.blogs.index')}}">Blogs Management</a></li>
            <li class="active"><small>{{isset($blog) ? 'Edit' : 'Add New'}}</small></li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">{!! isset($blog) ? 'Edit Blog' : 'Add New Blog' !!}</h3>

                <div class="box-tools pull-right">
                    {{-- Tools --}}
                </div>
            </div>
            <form action="{{route('dashboard.blogs.submit')}}" method="POST" id="contentform">
                <div class="box-body">
                    @csrf
                    <input type="hidden" name="operation" value="{{isset($blog) ? 'edit' : 'new'}}">
                    <input type="hidden" name="id" value="{{isset($blog) ? $blog->id : ''}}">

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Blog Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Enter blog tittle" name="title" value="{{isset($blog) ? $blog->title : ''}}">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Feature Image {!! !isset($blog) ? '<span class="text-danger">*</span>' : '' !!}</label>
                            <input type="file" class="form-control" name="blog_image">
                        </div>

                        <div class="form-group col-md-12">
                            <label>Blog Content <span class="text-danger">*</span></label>
                            <textarea id="ck-editor" name="content">{!! isset($blog) ? $blog->content : '' !!}</textarea>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Meta Tags</label>
                            <textarea class="form-control" name="meta_tags" placeholder="SEO Tools">{{isset($blog) ? $blog->meta_tags : ''}}</textarea>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Meta Title</label>
                            <textarea class="form-control" name="meta_title" placeholder="SEO Tools">{{isset($blog) ? $blog->meta_title : ''}}</textarea>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Meta Description</label>
                            <textarea class="form-control" name="meta_description" placeholder="SEO Tools">{{isset($blog) ? $blog->meta_title : ''}}</textarea>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Meta Keywords</label>
                            <textarea class="form-control" name="meta_keywords" placeholder="SEO Tools">{{isset($blog) ? $blog->meta_keywords : ''}}</textarea>
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
        $('#contentform').validate({
            rules: {
                title: {
                    required: true,
                },
                content: {
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

                var form = $('#contentform');

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

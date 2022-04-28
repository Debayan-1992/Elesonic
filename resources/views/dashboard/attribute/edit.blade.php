<?php error_reporting(0) ?>
@section('pageheader', 'Edit Attribute')
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Attributes Management
            <small>Edit</small>
        </h1>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Edit attribute</h3>

                <div class="box-tools pull-right">
                    {{-- Tools --}}
                </div>
            </div>
            <form action="{{route('dashboard.attributes.update')}}" method="POST" id="attributeform">
                <div class="box-body">
                    @csrf
                    <input type="hidden" name="id" value="{{$attribute->id}}">
                 <!-- <input type="text" value="{{ $category->parent_id }}"> -->

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control"  name="name" value="{{isset($attribute) ? $attribute->name : ''}}">
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
        $('#attributeform').validate({
            rules: {
                name: {
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

                var form = $('#attributeform');

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

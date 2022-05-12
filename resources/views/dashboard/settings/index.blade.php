@section('pageheader', 'Site Settings')
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Site Settings
            <small>Manage Portal Settings</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{route('dashboard.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Site Settings</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Update Settings</h3>

                <div class="box-tools pull-right"></div>
            </div>
            <div class="box-body">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs" id="tabs">
                        <li class="bg-gray active"><a href="#siteidentity" data-toggle="tab"><i class="fa fa-gear"></i> Site Identity</a></li>
                        <li class="bg-gray "><a href="#smssettings" data-toggle="tab"><i class="fa fa-comment"></i> SMS Settings</a></li>
                        <li class="bg-gray "><a href="#mailsettings" data-toggle="tab"><i class="fa fa-envelope"></i> Mail Settings</a></li>
                    </ul>
                    <form action="{{route('dashboard.settings.submit')}}" method="POST" id="settingsform">
                        @csrf
                        <div class="tab-content">
                            <div class="tab-pane active" id="siteidentity">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label>Site Name <span class="text-danger">*</span></label>
                                            <input name="name" id="name" value="{{$settings->name}}" class="form-control">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Site Title <span class="text-danger">*</span></label>
                                            <input name="title" value="{{$settings->title}}" class="form-control">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Banner Title One<span class="text-danger">*</span></label>
                                            <input name="banner_title_one" value="{{$settings->banner_title_one}}" class="form-control">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Banner Title Two<span class="text-danger">*</span></label>
                                            <input name="banner_title_two" value="{{$settings->banner_title_two}}" class="form-control">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Banner Description<span class="text-danger">*</span></label>
                                            <input name="banner_description" value="{{$settings->banner_description}}" class="form-control">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Address1<span class="text-danger">*</span></label>
                                            <input name="address1" value="{{$settings->address1}}" class="form-control">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Address2<span class="text-danger">*</span></label>
                                            <input name="address2" value="{{$settings->address2}}" class="form-control">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Address3<span class="text-danger">*</span></label>
                                            <input name="address3" value="{{$settings->address3}}" class="form-control">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Map Embed Link<span class="text-danger">*&nbsp;<i>(Please keep width="100%" height="100%" after src)</i></span></label>
                                            <textarea name="map_embed_link" value="{{$settings->map_embed_link}}" class="form-control">{{$settings->map_embed_link}}</textarea>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Site Email<span class="text-danger">*</span></label>
                                            <input name="site_email" value="{{$settings->site_email}}" class="form-control">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Site Link<span class="text-danger">*</span></label>
                                            <input name="site_link" value="{{$settings->site_link}}" class="form-control">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Site Number<span class="text-danger">*</span></label>
                                            <input name="site_number" value="{{$settings->site_number}}" class="form-control">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Site Number Office Name <span class="text-danger">*</span></label>
                                            <input name="site_number_office_name" value="{{$settings->site_number_office_name}}" class="form-control">
                                        </div>
                                    </div>
                                    <footer class="text-left">
                                        <button type="button" onclick="anchor('smssettings')" class="btn btn-primary btn-md">Next&nbsp;<i class="fa fa-arrow-circle-right"></i></button>
                                    </footer>
                                </form>
                            </div>

                            <div class="tab-pane" id="smssettings">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>SMS Status <span class="text-danger">*</span></label>
                                        <select name="smsflag" class="form-control select2" style="width: 100%">
                                            <option {{($settings->smsflag == 1) ? 'checked' : ''}} value="1">Enable</option>
                                            <option {{($settings->smsflag == 0) ? 'checked' : ''}} value="0">Disable</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>SMS SenderID</label>
                                        <input name="smssender" value="{{$settings->smssender}}" class="form-control">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>SMS Username</label>
                                        <input name="smsuser" value="{{$settings->smsuser}}" class="form-control">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>SMS Password</label>
                                        <input name="smspwd" value="{{$settings->smspwd}}" class="form-control">
                                    </div>
                                </div>
                                <footer class="text-left">
                                    <button type="button" onclick="anchor('siteidentity')" class="btn btn-primary btn-md"><i class="fa fa-arrow-circle-left"></i>&nbsp;Prev</button>
                                    <button type="button" onclick="anchor('mailsettings')" class="btn btn-primary btn-md">Next&nbsp;<i class="fa fa-arrow-circle-right"></i></button>
                                </footer>
                            </div>

                            <div class="tab-pane" id="mailsettings">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Mail Host</label>
                                        <input name="mailhost" value="{{$settings->mailhost}}" class="form-control">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Mail Port</label>
                                        <input name="mailport" value="{{$settings->mailport}}" class="form-control">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Mail Encryption</label>
                                        <input name="mailenc" value="{{$settings->mailenc}}" class="form-control">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Mail Username</label>
                                        <input name="mailuser" value="{{$settings->mailuser}}" class="form-control">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Mail Password</label>
                                        <input name="mailpwd" value="{{$settings->mailpwd}}" class="form-control">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Mail-From Address</label>
                                        <input name="mailfrom" value="{{$settings->mailfrom}}" class="form-control">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Mail-From Name</label>
                                        <input name="mailname" value="{{$settings->mailname}}" class="form-control">
                                    </div>
                                </div>
                                <footer class="text-left">
                                    <button type="button" onclick="anchor('smssettings')" class="btn btn-primary btn-md"><i class="fa fa-arrow-circle-left"></i>&nbsp;Prev</button>
                                    <button type="submit" class="btn btn-primary btn-md">Submit</button>
                                </footer>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        .tab-pane{
            padding: 10px
        }
    </style>
@endpush

@push('script')
    <script>
        $('#settingsform').validate({
            rules: {
                name: {
                    required: true,
                },
                title: {
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
                var form = $('#settingsform');

                Pace.track(function(){
                    form.ajaxSubmit({
                        dataType:'json',
                        beforeSubmit:function(){
                            form.find('button[type="submit"]').button('loading');
                        },
                        success:function(data){
                            form.find('button[type="submit"]').button('reset');
                            notify(data.status, 'success');
                        },
                        error: function(errors) {
                            form.find('button[type="submit"]').button('reset');
                            showErrors(errors, form);
                        }
                    });
                });
            }
        });

        function anchor(id){
            $('a[href="#'+id+'"]').tab('show');
        }
    </script>
@endpush

@section('pageheader', 'Profile')
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Profile
            <small>{{$user->name}}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{route('dashboard.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Profile</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <a href="javascript:;" data-toggle="modal" data-target="#imagemodal">
                            <img class="profile-user-img img-responsive img-circle" src="{{$user->avatar}}">
                        </a>

                        <h3 class="profile-username text-center">{{$user->name}}</h3>

                        <p class="text-muted text-center">{{$user->role->name}}</p>

                        <ul class="list-group list-group-unbordered">
                            @if(in_array($user->role->slug, ['bank']))
                                <li class="list-group-item">
                                    <b>Main Wallet</b> <a class="pull-right"><span class="badge badge-sm bg-blue">{!! config('app.currency.icon') !!} {{$user->mainwallet}}</span></a>
                                </li>
                            @endif
                        </ul>

                        <!-- <a href="{{route('logout')}}" class="btn btn-primary btn-block"><b><i class="fa fa-power-off"></i></b> Logout</a> -->
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="bg-gray active"><a href="#basicdetails" data-toggle="tab"><i class="fa fa-gear"></i> Basic Details</a></li>

                        @if (in_array($user->role->slug, ['customer']))
                            <li class="bg-gray "><a href="#businessdoc" data-toggle="tab"><i class="fa fa-file"></i> Documents</a></li>
                        @endif

                        <li class="bg-gray "><a href="#password" data-toggle="tab"><i class="fa fa-lock"></i> Change Password</a></li>
                    </ul>
                    <div class="tab-content">
                        {{-- Basic Details Tab --}}
                        <div class="tab-pane active" id="basicdetails">
                            <form id="profileform" method="POST" action="{{route('dashboard.profile')}}">
                                @csrf
                                <input type="hidden" name="type" value="basicdetails">
                                <input type="hidden" name="id" value="{{$user->id}}">

                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Name <span class="text-danger">*</span></label>
                                        <input name="name" value="{{$user->name}}" class="form-control" placeholder="Enter full name">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input name="email" value="{{$user->email}}" class="form-control" placeholder="Enter email address">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Mobile {!! $user->role->slug == 'customer' ? '<span class="text-danger">*</span>' : '' !!} </label>
                                        <input name="mobile" value="{{$user->mobile}}" class="form-control" placeholder="Enter mobile number">
                                    </div>

                                    @if($user->role->slug == 'customer')
                                        <div class="form-group col-md-4">
                                            <label>Pancard <span class="text-danger">*</span></label>
                                            <input name="pancard" value="{{$user->pancard}}" class="form-control" placeholder="Enter pancard number">
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label>Gender</label>
                                            <select name="gender" class="form-control select2">
                                                <option value="">Select your Gender</option>
                                                <option {{$user->gender == 'male' ? 'selected' : ''}} value="male">Male</option>
                                                <option {{$user->gender == 'female' ? 'selected' : ''}} value="female">Female</option>
                                                <option {{$user->gender == 'others' ? 'selected' : ''}} value="others">Others</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label>Date of Birth</label>
                                            <input type="text" value="{{$user->dob}}" name="date_of_birth" class="form-control" id="dob-picker" readonly>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label>Pincode</label>
                                            <input type="number" value="{{$user->pincode}}" name="pincode" class="form-control">
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label>City</label>
                                            <select name="city" class="form-control select2">
                                                <option value="">Select City</option>
                                                @foreach ($cities as $item)
                                                    <option {{$item->id == $user->city_id ? 'selected' : ''}} value="{{$item->id}}">{{$item->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                </div>
                                <footer class="text-left">
                                    <button type="submit" class="btn btn-primary btn-md">Submit</button>
                                </footer>
                            </form>
                        </div>

                        @if (in_array($user->role->slug, ['customer']))
                            {{-- Business Documents Tab --}}
                            <div class="tab-pane" id="businessdoc">
                                <form id="businessdocform" method="POST" action="{{route('dashboard.profile')}}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="type" value="businessdoc">
                                    <input type="hidden" name="id" value="{{$user->id}}">

                                    @php $busdocsubmit = false @endphp

                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label>Pancard</label>
                                            @if(Myhelper::can('edit_'.$user->role->slug) || !isset($user->details->pancardimage) || $user->details->pancardimage == null)
                                                <input name="pancard_image" type="file" class="form-control" accept="image/*">

                                                @php $busdocsubmit = true @endphp
                                            @endif

                                            @if($user->details)
                                                <hr class="short">
                                                <img src="{{$user->details->pancardimagepath}}" class="img-responsive img-bordered pad">
                                            @endif
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label>Aadhar Card</label>
                                            @if(Myhelper::can('edit_'.$user->role->slug) || !isset($user->details->aadharcardimage) || $user->details->aadharcardimage == null)
                                                <input name="aadharcard_image" type="file" class="form-control" accept="image/*">

                                                @php $busdocsubmit = true @endphp
                                            @endif

                                            @if($user->details)
                                                <hr class="short">
                                                <img src="{{$user->details->aadharcardimagepath}}" class="img-responsive img-bordered pad">
                                            @endif
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label>Cancelled Cheque</label>
                                            @if(Myhelper::can('edit_'.$user->role->slug) || !isset($user->details->cancelledchequeimage) || $user->details->cancelledchequeimage == null)
                                                <input name="cancelled_cheque_image" type="file" class="form-control" accept="image/*">

                                                @php $busdocsubmit = true @endphp
                                            @endif

                                            @if($user->details)
                                                <hr class="short">
                                                <img src="{{$user->details->cancelledchequeimagepath}}" class="img-responsive img-bordered pad">
                                            @endif
                                        </div>
                                    </div>
                                    <footer class="text-left">
                                        @if($busdocsubmit == true)
                                            <button type="submit" class="btn btn-primary btn-md">Submit</button>
                                        @endif
                                    </footer>
                                </form>
                            </div>
                        @endif

                        {{-- Password Update Tab --}}
                        <div class="tab-pane" id="password">
                            <form id="passwordform" method="POST" action="{{route('dashboard.profile')}}">
                                @csrf
                                <input type="hidden" name="type" value="changepassword">
                                <input type="hidden" name="id" value="{{$user->id}}">

                                <div class="row">
                                    @if($user->id == \Auth::id())
                                        <div class="form-group col-md-4">
                                            <label>Current Password <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-md">
                                                <input type="password" name="current_password" value="" class="form-control" placeholder="Enter your current password">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn btn-info btn-flat eye-password"><i class="fa fa-eye"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="form-group col-md-4">
                                        <label>New Password <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-md">
                                            <input type="password" name="new_password" value="" class="form-control" placeholder="Enter your new password">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-info btn-flat eye-password"><i class="fa fa-eye"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>New Password Confirmation<span class="text-danger">*</span></label>
                                        <div class="input-group input-group-md">
                                            <input type="password" name="new_password_confirmation" value="" class="form-control" placeholder="Re-enter your new password">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-info btn-flat eye-password"><i class="fa fa-eye"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <footer class="text-left">
                                    <button type="submit" class="btn btn-primary btn-md">Submit</button>
                                </footer>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade-in" id="imagemodal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Profile Picture</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{route('dashboard.profile')}}" method="POST" id="imageform" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" value="profileimage">
                    <input type="hidden" name="id" value="{{$user->id}}">

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Picture <span class="text-danger">*</span></label>
                            <input name="profile_image" accept="image/*" class="form-control" type="file">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
    </style>
@endpush

@push('script')
<script>
    $('#profileform').validate({
        rules: {
            name: {
                required: true,
            },
            email: {
                required: true,
                email: true
            },
            mobile: {
                number: true,
                /** @if($user->role->slug == 'customer') **/
                required: true,
                /** @endif **/
                maxlength: 10,
                minlength: 10,
            },
            /** @if($user->role->slug == 'customer') **/
            pancard: {
                required: true,
                maxlength: 10,
                minlength: 10,
            },
            pincode: {
                number: true,
                maxlength: 6,
                minlength: 6,
            },
            /** @endif **/
        },
        messages: {
            name: {
                required: "Please enter your name",
            },
            email: {
                required: "Please enter your email address",
                email: "The inserted email address is invalid"
            },
            mobile: {
                required: "Please enter your mobile number",
                number: "Please enter a valid mobile number",
                maxlength: "Mobile number should be of 10 digits",
                minlength: "Mobile number should be of 10 digits"
            },
            pancard: {
                required: "Please enter your pancard number",
                maxlength: "Pancard number should be of 10 digits",
                minlength: "Pancard number should be of 10 digits"
            }
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
            var form = $('#profileform');

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

    $('#passwordform').validate({
        rules: {
            /** @if($user->id == \Auth::id()) **/
            current_password: {
                required: true,
            },
            /* @endif */
            new_password: {
                required: true,
            },
            new_password_confirmation: {
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
            var form = $('#passwordform');

            Pace.track(function(){
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button[type="submit"]').button('loading');
                    },
                    success:function(data){
                        form[0].reset();
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

    $('#imageform').validate({
        rules: {
            profile_image: {
                required: true,
            },
        },
        profile_image: {
            name: {
                required: "Please select an image",
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
            var form = $('#imageform');

            Pace.track(function(){
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button[type="submit"]').button('loading');
                    },
                    success:function(data){
                        form.find('button[type="submit"]').button('reset');
                        location.reload();
                    },
                    error: function(errors) {
                        form.find('button[type="submit"]').button('reset');
                        showErrors(errors, form);
                    }
                });
            });
        }
    });

    /* @if (in_array($user->role->slug, ['customer'])) */
    $('#businessdocform').validate({
        errorElement: "p",
        errorPlacement: function ( error, element ) {
            if ( element.prop("tagName").toLowerCase() === "select" ) {
                error.insertAfter( element.closest( ".form-group" ).find(".select2") );
            } else {
                error.insertAfter( element );
            }
        },
        submitHandler: function() {
            var form = $('#businessdocform');

            Pace.track(function(){
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button[type="submit"]').button('loading');
                    },
                    success:function(data){
                        form.find('button[type="submit"]').button('reset');
                        location.reload();
                    },
                    error: function(errors) {
                        form.find('button[type="submit"]').button('reset');
                        showErrors(errors, form);
                    }
                });
            });
        }
    });
    /* @endif */

    $('#dob-picker').datepicker({
        endDate: "{{Carbon\Carbon::now()->subYears(18)->format('m/d/Y')}}",
        autoclose: true,
    })
</script>
@endpush

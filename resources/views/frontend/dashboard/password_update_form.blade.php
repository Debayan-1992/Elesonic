@extends('layouts.frontend.app')

@section('content')
<div class="container">
    <div class="main-body">
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">

                        @if(session()->has('message'))
                            <div class="alert alert-success">
                                {{ session()->get('message') }}
                            </div>
                        @endif

                        @if($errors->any())
                        <div class="alert alert-danger">
                            {!! implode('', $errors->all('<div>:message</div>')) !!}
                        </div>
                        @endif

                        <div class="d-flex flex-column align-items-center text-center">
                            @if($user->profile_image ==null)
                            <img src="{{asset('custom_resource/images/profile_no_image.png')}}" alt="User Image empty" class="rounded-circle p-1 bg-primary" width="110">
                            @else
                            <img src="{{asset('uploads/profile/'.$user->profile_image)}}" alt="User Image" class="rounded-circle p-1 bg-primary" width="110">
                            @endif
                            <div class="mt-3">
                                <h4>{{ $user->name }}</h4>
                                <p class="text-secondary mb-1">Some Text here</p>
                            </div>
                        </div>
                        <hr class="my-4">
                        <ul class="list-group list-group-flush">
                            
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                
                                @if($user->role_id  == App\Model\Role::IS_CUSTOMER)
                                <span class="text-secondary"><a href="{{route('customer.customer_dashboard')}}">My Account</a></span>
                                @elseif($user->role_id  == App\Model\Role::IS_SELLER)
                                <span class="text-secondary"><a href="{{route('seller.seller_dashboard')}}">My Account</a></span>
                                @endif
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                
                                @if($user->role_id  == App\Model\Role::IS_CUSTOMER)
                                <span class="text-secondary"><a href="{{route('customer.frontend_change_pass')}}">Change Password</a></span>
                                @elseif($user->role_id  == App\Model\Role::IS_SELLER)
                                <span class="text-secondary"><a href="{{route('seller.frontend_change_pass')}}">Change Password</a></span>
                                @endif
                            </li>
                            @if($user->role_id  == App\Model\Role::IS_CUSTOMER)
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                
                                <span class="text-secondary"><a href="">My Orders</a></span>
                            </li>
                            @endif
                            @if($user->role_id  == App\Model\Role::IS_SELLER)
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                
                                <span class="text-secondary"><a href="">Request Service</a></span>
                            </li>
                            @endif
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                {{-- <h6 class="mb-0"><i class="fa fa-sign-out mr-2"></i></h6> --}}
                                <span class="text-secondary"><a href="{{route('lgt')}}">Logout</a></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        
                        @if($user->role_id  == App\Model\Role::IS_CUSTOMER)
                        <form action="{{route('customer.frontend_pass_upd')}}" method="post" id="pass_form">
                        @endif
                        @if($user->role_id  == App\Model\Role::IS_SELLER)
                        <form action="{{route('seller.frontend_pass_upd')}}" method="post" id="pass_form">
                        @endif
                            @csrf
                            <input type="text" name="user_id" class="form-control" value="{{encrypt($user->id)}}" hidden>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Password</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="password" name="password" class="form-control" value="">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Re-enter Password</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="password" name="password_confirmation" class="form-control" value="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3"></div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="submit" class="btn btn-primary px-4" value="Save Changes">
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </dIv>
        </dIv>
    </dIv>
</dIv>
@endsection
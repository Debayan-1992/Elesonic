@extends('layouts.frontend.app')

@section('content')
<div class="container">
    <div class="main-body">
        <div class="row">
    @if(session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
    @endif
    @include('layouts.frontend.leftpanel')
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
@extends('layouts.frontend.app')
@section('content')
@section('pageheader', 'My Address')
<div class="ptb bd_productlisting">
<div class="container">

    <div class="row ">
        
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
                                
                                <span class="text-secondary"><a href="{{route('customer.address')}}">My Address</a></span>
                            </li>
                            @endif
                            @if($user->role_id  == App\Model\Role::IS_CUSTOMER)
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                
                                <span class="text-secondary"><a href="{{route('customer.carts')}}">My Cart</a></span>
                            </li>
                            @endif
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
            <div class="row justify-content-center align-content-center">
               
                    @if(count($shippingAddress) > 0)
                    @foreach($shippingAddress as $row)

                    <div class="col-md-5 user-card-col col-12 mb-2">
                        <div id="box" class="user-card">
                            <b>Name</b> : {{ $row->user_first_name.' '.$row->user_last_name}}&nbsp;&nbsp;
                        @if($row->is_default == 'Yes')
                            <span style="padding: 10px;color: green;">Default</span>
                         
                        @else
                                 <span onclick="makeDefault('{{$row->address_id}}')" style="cursor:pointer;padding: 10px;color: blue;">Make Default</span>

                                  <span onclick="makeDelete('{{$row->address_id}}')" style="cursor:pointer;padding: 10px;color: red;">Delete</span>
                        @endif

                            <br> 
                            <b>Email</b> : {{$row->user_email}}<br>
                            <b>Phone</b> : {{$row->user_phone_no}}<br>
                            <b>Address</b> : {{$row->user_address}} ,{{$row->user_pincode}}<br>{{$row->user_city}}>,{{$row->user_state}}
                        </div>
                    </div>

               @endforeach
               @endif
            </div>
            
 <section class="main-body mt-4">
        <div class="custom-container">
          
            <form method="post" id="shippingAddress" action="" class="form-page">
                    <div class="col-md-12 p-0">
                        <div class="heading mb-1"><h2>Add New Shipping Details</h2></div>
                        <!-- <h2>Add New Shipping Details</h2> -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="from-group">
                                    <label>First Name</label>
                                    <input type="text" name="first_name" id="first_name" autocomplete="off" value="" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="from-group">
                                    <label>Last Name</label>
                                    <input type="text" name="last_name" id="last_name" autocomplete="off" value="" class="form-control">
                                </div>
                            </div>
                        </div>

                         <div class="row">
                            <div class="col-md-6">
                                <div class="from-group">
                                     <label>Phone</label>
                                    <input type="text" name="phone" onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxlength="11" id="phone" autocomplete="off" value="" class="form-control">
                                </div>
                            </div>
                        
                           

                            </div>
                        <div class="row">

                             <!-- <div class="col-md-6">
                                <div class="from-group">
                                     <label>State</label>
                                    <select class="form-control" onchange="getCity(this.value)" autocomplete="off" name="state" id="state" placeholder="Pick a state...">
                                        <option value="">Choose</option>
                                    </select>
                                </div>
                            </div> -->

                             <!-- <div class="col-md-6">
                                <div class="from-group">
                                     <label>City</label>
                                    <select class="form-control" autocomplete="off" name="city" id="city" placeholder="Pick a state...">
                                    <option value="">Choose</option>
                                    </select>
                                </div>
                            </div> -->
                            </div>
                       
                        <div class="row">

                             <div class="col-md-6">
                                <div class="from-group">
                                     <label>Postcode</label>
                                    <input type="text" name="postcode" id="postcode"  onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxlength="6" class="form-control">
                                </div>
                            </div>
                             
                        
                           
                            <div class="col-md-6">
                                <div class="from-group">
                                     <label>Street address</label>
                                   <textarea class="form-control" id="address" name="address" cols="5" rows="5" cols="5"></textarea>
                                </div>
                            </div>

                             
                        </div>       
        </div>
    </form>
     <div class="col-md-6 p-0">
        <button class="bd_btn btn-save" onclick="saveShipping()">Save</button>
    </div>
    </section>
</div>
</div>
</section>
</div>
</div>
@endsection
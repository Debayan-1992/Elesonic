<div class="col-lg-4">
<div class="card">
    <div class="card-body">
    @if($errors->any())
        <div class="alert alert-danger">
            {!! implode('', $errors->all('<div>:message</div>')) !!}
        </div>
        @endif
@php
$user = auth()->user();
@endphp
      

        <div class="d-flex flex-column align-items-center text-center">
            @if($user->profile_image ==null)
            <img src="{{asset('custom_resource/images/profile_no_image.png')}}" alt="User Image empty" class="rounded-circle p-1 bg-primary" width="110">
            @else
            <img src="{{asset('uploads/profile/'.$user->profile_image)}}" alt="User Image" class="rounded-circle p-1 bg-primary" width="110">
            @endif
            <div class="mt-3">
                <h4>{{ $user->name }}</h4>
               
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
                
                <span class="text-secondary"><a href="{{route('customer.my-order')}}">My Orders</a></span>
            </li>
            @endif
            @if($user->role_id  == App\Model\Role::IS_CUSTOMER)
            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                
                <span class="text-secondary"><a href="{{route('customer.my-services')}}">Requested Services</a></span>
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
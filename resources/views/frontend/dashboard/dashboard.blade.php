@extends('layouts.frontend.app')

@push('header')
    <link rel="stylesheet" href="//adminlte.io/themes/AdminLTE/plugins/iCheck/square/blue.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.css"
        integrity="sha512-8D+M+7Y6jVsEa7RD6Kv/Z7EImSpNpQllgaEIQAtqHcI0H6F4iZknRj0Nx1DCdB+TwBaS+702BGWYC0Ze2hpExQ=="
        crossorigin="anonymous" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"
        integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw=="
        crossorigin="anonymous" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/pace/0.7.8/themes/black/pace-theme-flash.min.css"
        integrity="sha512-0c1cb0LYXVvb9L459008ryNuWW7NuZEFY0ns6fAOfpJhHnTX7Db2vbSrjaLgvUpcl+atb3hkawh2s+eEE3KaLQ=="
        crossorigin="anonymous" />   
@endpush

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
                <div class="card">
                    <div class="card-body">
                        @if($user->role_id  == App\Model\Role::IS_CUSTOMER)
                        <form action="{{route('customer.frontend_acc_upd')}}" method="post" enctype="multipart/form-data">
                        @endif
                        @if($user->role_id  == App\Model\Role::IS_SELLER)
                        <form action="{{route('seller.frontend_acc_upd')}}" method="post" enctype="multipart/form-data">
                        @endif
                        @csrf
                        <input type="text" name="user_id" class="form-control" value="{{encrypt($user->id)}}" hidden>
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Full Name</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Email</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                <input type="text"  class="form-control" value="{{ $user->email }}" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Phone</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                <input type="text"  class="form-control" value="{{ $user->mobile }}" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Role</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                @if($user->role_id  == App\Model\Role::IS_CUSTOMER)
                                <input type="text"  class="form-control" value="Customer" readonly>
                                @elseif($user->role_id  == App\Model\Role::IS_SELLER)
                                <input type="text"  class="form-control" value="Seller" readonly>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Image</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                <input type="file" accept="image/*" class="form-control" id="profile_image" name="image" >
                                <input type="hidden"  class="form-control"  name="old_image" value="{{ $user->profile_image }}" >
                                
                                <img src="" class="previewHolder" style="display:none;" alt="User Image empty" class="rounded-circle p-1 bg-primary" width="110">  
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
                {{-- <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="d-flex align-items-center mb-3">Project Status</h5>
                                <p>Web Design</p>
                                <div class="progress mb-3" style="height: 5px">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p>Website Markup</p>
                                <div class="progress mb-3" style="height: 5px">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 72%" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p>One Page</p>
                                <div class="progress mb-3" style="height: 5px">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 89%" aria-valuenow="89" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p>Mobile Template</p>
                                <div class="progress mb-3" style="height: 5px">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 55%" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p>Backend API</p>
                                <div class="progress" style="height: 5px">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 66%" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="//adminlte.io/themes/AdminLTE/plugins/iCheck/icheck.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
        integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
        crossorigin="anonymous"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
        integrity="sha512-YUkaLm+KJ5lQXDBdqBqk7EVhJAdxRnVdT2vtCzwPHSweCzyMgYV/tgGF4/dCyqtCC2eCphz0lRQgatGVdfR0ww=="
        crossorigin="anonymous"></script>
    <script type="text/javascript" src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js">
    </script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pace/0.7.8/pace.min.js"
        integrity="sha512-t3TewtT7K7yfZo5EbAuiM01BMqlU2+JFbKirm0qCZMhywEbHZWWcPiOq+srWn8PdJ+afwX9am5iqnHmfV9+ITA=="
        crossorigin="anonymous"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"
        integrity="sha512-zlWWyZq71UMApAjih4WkaRpikgY9Bz1oXIW5G0fED4vk14JjGlQ1UmkGM392jEULP8jbNMiwLWdM8Z87Hu88Fw=="
        crossorigin="anonymous"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
@endpush

@push('script')
<script>
function readURL(input) 
{
    if (input.files && input.files[0]) 
    {
        var reader = new FileReader();
        
        reader.onload = function(e) 
        {
            $('.previewHolder').attr('src', e.target.result);
            $('.previewHolder').show();
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$("#profile_image").change(function() {
    readURL(this);
});
</script>
@endpush


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
    @if(session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
    @endif
        <div class="row">
        @include('layouts.frontend.leftpanel')
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
                                @if($user->profile_image)
                                <hr class="short">
            
                                <img class="img-responsive img-bordered pad"  height="150px" width="150px" src="{{config('app.url')}}/uploads/profile/{{ $user->profile_image }}" class="img-responsive img-bordered pad">
                                @endif
                                
                                <img src="" class="previewHolder" style="display:none;" alt="User Image empty" class="rounded-circle p-1 bg-primary" width="110">  
                            </div>
                        </div>
                         @if($user->role_id  == App\Model\Role::IS_CUSTOMER)
                         <div class="row mb-3">
                            <div class="col-sm-3">
                                <h6 class="mb-0">State</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                               <select  onchange="getCity(this.value)" name="state" id="state" class="form-control select2">
                                @if($user->state_id == "")
                                <option value="">Select State</option>
                                @endif
                                @foreach ($state as $item)
                                    <option {{ $user->state_id == $item->id ? 'selected' : '' }} value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                            </div>
                        </div>
                         <div class="row mb-3">
                            <div class="col-sm-3">
                                <h6 class="mb-0">City</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                            <select class="form-control" autocomplete="off" name="delcity" id="delcity" placeholder="Pick a state...">
                            @if($user->city_id == "")
                            <option value="">Select City</option>
                            @else
                            <option value="{{ $user->city_id }}">{{ $city->name }}</option>
                            @endif
                        </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Pincode</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                              
                                <input type="number" name="pincode"  class="form-control" value="{{ $user->pincode }}">
                               
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Address</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                              
                              <textarea rows="5" name="address" cols="65">{{ $user->address }}</textarea>
                               
                            </div>
                        </div>
                        @endif
                        @if($user->role_id  == App\Model\Role::IS_SELLER)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Pancard</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                <input type="file" name="pancard_image" accept="image/*"  class="form-control"  readonly>
                                @if($user->details)
                                <hr class="short">
                                <img class="img-responsive img-bordered pad"  height="150px" width="150px" src="{{$user->details->pancardimagepath}}" class="img-responsive img-bordered pad">
                                @endif
                            </div>
                           
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Aadhar Card</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                <input type="file" name="aadharcard_image" accept="image/*"  class="form-control" readonly>
                                @if($user->details)
                                <hr class="short">
                                <img class="img-responsive img-bordered pad"  height="150px" width="150px" src="{{$user->details->aadharcardimagepath}}" class="img-responsive img-bordered pad">
                            @endif
                            </div>
                          
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Cancelled Cheque</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                <input type="file" name="cancelled_cheque_image" accept="image/*"  class="form-control"  readonly>
                            @if($user->details)
                                <hr class="short">
                                <img class="img-responsive img-bordered pad" height="150px" width="150px" src="{{$user->details->cancelledchequeimagepath}}" class="img-responsive img-bordered pad">
                            @endif
                            </div>
                           
                        </div>

                        @endif
                        
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
<script type="text/javascript">
function getCity(state)
{  
    var city = '<option value="">Select City</option>';
    $.ajax({
    type: "POST",
    dataType: 'Json',
    url: "{{ route('customer.getcity') }}",
    data:{'_token':'{{csrf_token()}}','state':state},
    async: true,
    success: function (data) {
        if(data.length > 0){
            for(var i=0;i<data.length;i++){
                city+='<option value="'+data[i].id+'">'+data[i].name+'</option>';
            }
            $("#delcity").html(city);
        }        
    }
    });  
}
</script>
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


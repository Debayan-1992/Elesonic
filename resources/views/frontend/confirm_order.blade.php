@extends('layouts.frontend.app')
@section('content')
@section('pageheader', 'Shipping Address')

<div class="ptb bd_productlisting">
<div class="container">
<div class="heading text-center"><h2>Shipping Address</h2></div>
    <div class="row justify-content-center align-content-center">
        @if(count($shippingAddress) > 0)
        @foreach($shippingAddress as $row)
            <div class="col-md-4 col-sm-6 col-12 mb-2">

                <div id="box" class="user-card-active">
                   <span><b>Shipping Address</b></span><br>
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
                            <b>Address</b> : {{$row->user_address}} ,{{$row->user_pincode}}<br>{{$row->user_city}},{{$row->user_state}}
                </div>
            </div>
       @endforeach
       @endif
    <div class="col-md-4 col-sm-6 col-12 mb-2">

        <div id="box" class="user-card-active">
            <span><b>Billing Address</b></span><br>
            <b>Name</b> : {{ $member_dtl->name }}<br>
            <b>Email</b> : {{ $member_dtl->email }}
            <br> 
            <b>Phone</b> : {{ $member_dtl->mobile }}
            <br> 
        </div>
    </div>

    </div>
 <section class="main-body mt-4">
    <div class="custom-container">
    <form method="post" id="shippingAddress" action="{{ route('customer.addaddressdef') }}" class="form-page">
            @csrf
    <div class="col-12">
            <h2>Add New Shipping Details</h2>
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
                        <label>E-mail</label>
                        <input type="email" name="email" id="email" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="from-group">
                            <label>Phone</label>
                        <input type="text" name="phone" onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxlength="11" id="phone" autocomplete="off" value="" class="form-control">
                </div>
            </div>
        </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="from-group">
                        <label>State</label>
                        <select  onchange="getCity(this.value)" name="state" id="state" class="form-control select2">
                                <option value="">Select State</option>
                                @foreach ($state as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                        </select>
                </div>
            </div>
             <div class="col-md-6">
                    <div class="from-group">
                            <label>City</label>
                            <select class="form-control" autocomplete="off" name="delcity" id="delcity" placeholder="Pick a state...">
                        <option value="">Select City</option>
                        </select>
                    </div>
                </div>
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
                            <label>Address</label>
                        <textarea class="form-control" id="address" name="address" rows="5" cols="5"></textarea>
                    </div>
                </div>
            </div>       
        </div>
    </form>
     <div class="col-md-6">
        <button class="bd_btn btn-save" onclick="saveShipping()">Save</button>
         <a class="bd_btn btn-save btn-proceed" href="{{route('customer.place-order')}}">Proceed</a>
    </div>
  </div>
</section>
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
<script type="text/javascript">
   function saveShipping()
   {
        if($("#first_name").val()=="")
        {
            swal("First Name Required");
        }
        else if($("#last_name").val()=="")
        {
             swal("Last Name Required");
        }
        else if($("#email").val()=="")
        {
             swal("Email Required");
        }
        else if($("#phone").val()=="")
        {
             swal("Phone Required");
        }
        else if($("#state").val()=="")
        {
             swal("State Required");
        }
        else if($("#delcity").val()=="")
        {
             swal("City Required");
        }
        else if($("#postcode").val()=="")
        {
             swal("Postcode Required");
        }
        else
        {
            $("#shippingAddress").submit();
        }
   }
</script>
<script>
function makeDefault(addressId)
{
    $.ajax(
        {
    type: "POST",
    dataType: 'Json',
    url: "{{ route('customer.makeDefault') }}",
    data:{'_token':'{{csrf_token()}}','addressId':addressId},
    async: true,
     beforeSend: function ()
        {
            swal("Successfully Changed");
        },
    success: function (data) {
      if(data == 1){
        location.reload();
      }
    }
});  
}
function makeDelete(addressId)
{
swal({
 title: "Are you sure?",
 text: "",
 icon: "warning",
 buttons: true,
 dangerMode: true,
})
.then((willDelete) => {
if (willDelete) {
$.ajax(
    {
type: "POST",
dataType: 'Json',
url: "{{ route('customer.makeDelete') }}",
data:{'_token':'{{csrf_token()}}','addressId':addressId},
async: true,
success: function (data) {
    //alert(data)
if(data==1)
{
location.reload();
}        
}
});         // submitting the form when user press yes
} else {
  }
});
}
</script>
@endpush

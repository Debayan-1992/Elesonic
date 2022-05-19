@section('pageheader', 'Order Details')
@extends('layouts.app')
@section('content')

<div class="panel">
  <div class="panel-heading">
     ID # <b>{{$order->order_unique_id}}</b>
  </div>
<div class="panel-body">
  <div class="table-responsive">
    <div class="col-xs-6">
      <form> 
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"> Shipping Details</h3>
            </div>
            
            
            
            <div class="box-body">
              <table id="" class="table table-bordered table-striped">
                
                <tbody>

      
             <tr>
                  <th> Name </th>
                  <td>{{$shippingAddress->user_first_name}} {{$shippingAddress->user_last_name}}</td>
              </tr>
             
               <tr>
                  <th>E-mail </th>
                  <td>{{$shippingAddress->user_email}}</td>
              </tr>
              <tr>
                  <th>Contact No. </th>
                  <td>{{$shippingAddress->user_phone_no}}</td>
              </tr>
               <tr>
                  <th>Address</th>
                  <td>{{$shippingAddress->user_city}}, {{$shippingAddress->user_state}}, {{$shippingAddress->user_pincode}}</td>
              </tr>
                </tbody>
              </table>
            </div>
          </div>
        </form>
      </div>
        <div class="col-xs-6">
            <!-- /.box-header -->
           <form> 
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"> Billing Details</h3>
            </div>
            
            
            
            <div class="box-body">
              <table id="" class="table table-bordered table-striped">
                
                <tbody>

      
             <tr>
                  <th> Name </th>
                  <td>{{@$billingAddress->name}}</td>
              </tr>

               <tr>
                  <th>Email </th>
                  <td>{{$billingAddress->email}}</td>
              </tr>
             
               <tr>
                  <th>Contact No. </th>
                  <td>{{$billingAddress->mobile}}</td>
              </tr>
               <tr>
                  <th>Address</th>
                  <td> {{ $billingAddress->address }} {{ $billingAddress->pincode }} , {{ @$mycity->name }} {{ @$mystate->name }}</td>
              </tr>
              </tbody>
              </table>
           
            </div>
         
          </div>
        </form>
      </div>
            <table id="my-datatable" class="table table-bordered table-striped" style="width: 100%">

                <thead>

                <tr>
                <th>Sl No</th>
                <th>Product</th>
                <th>Seller</th>
                <th>QTY</th>
                <th>MRP($)</th>
                <th>Discount(%)</th>
                <th>Net price($)</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
                </thead>

                <tbody>
            @if(count($order_details) > 0)
            @php
            $i= 0;
            @endphp
            @foreach($order_details as $row)
            @php
            $i++;
            $seller = \App\User::where('id',$row->product_seller_id)->get();
            @endphp
            <tr>
                <td>{{$i}}</td>
                <td><img src="{{config('app.url')}}/uploads/products/{{ $row->photos }}" height="50px" width="50px"><br><b>{{$row->name}}</b></td>
                <td>{{$seller[0]->name}}</td>
                <td>{{$row->cart_item_pro_qty}}</td>
                <td>{{$row->cart_item_price}}</td>
                <td>{{$row->cart_item_price_disc}}</td>
                <td>{{$row->cart_item_net_price}}</td>
                <td>{{$row->order_product_status}}</td>

                <td>
                <select class="form-control" onchange="change_sts(this.value,{{$row->order_id}},{{$row->order_details_id}})">
                  <option {{ $row->order_product_status == "Pending" ? 'selected' : '' }} value="Pending">Pending</option>
                  <option {{ $row->order_product_status == "Processing" ? 'selected' : '' }} value="Processing">Processing</option>
                  <option {{ $row->order_product_status == "On hold" ? 'selected' : '' }} value="On hold">On hold</option>
                  <option {{ $row->order_product_status == "Completed" ? 'selected' : '' }} value="Completed">Completed</option>
                  <option {{ $row->order_product_status == "Cancelled" ? 'selected' : '' }} value="Cancelled">Cancelled</option>
                  <option {{ $row->order_product_status == "Failed" ? 'selected' : '' }} value="Failed">Failed</option>
               </select>
               <span style="color:green;display:none;" id="statuschangemsg"><b>Please wait</b></span>
                </td>
            </tr>
            @endforeach
            @endif
          
        </tbody>
            <tfoot>
               <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><b>Sub-Total</b> : ${{$order->order_total_price - $order->shipping_charge}}</td>
                  <td></td>
                  <td></td>
                </tr>
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><b>Shipping Charge</b> : ${{$order->shipping_charge}}</td>
                  <td></td>
                  <td></td>
                </tr>
                 <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><b>Total</b> : ${{$order->order_total_price}} &nbsp;&nbsp;<a style="curser:pointer" href="{{$path}}" download><i class="fa fa-print" aria-hidden="true"></i></a></td>
                  <td></td>
                  <td></td>
                </tr>
            </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
@push('script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
function change_sts(val,order_id,order_details_id){
swal({
 title: "Are you sure?",
 text: "",
 icon: "warning",
 buttons: true,
 dangerMode: true,
})
.then((willDelete) => {
if (willDelete) {
  $("#statuschangemsg").css('display','block');
  Pace.track(function(){
                $.ajax({
                    url: "{{route('dashboard.orders.statusChange')}}",
                    method: "POST",
                    data: {'_token':'{{csrf_token()}}','val':val,'order_id':order_id,'order_details_id':order_details_id},
                    beforeSubmit:function(){
                            //form.find('button[type="submit"]').button('loading');
                            $("#statuschangemsg").css('display','block');
                        },
                    success: function(data){
                       location.reload();
                    }, error: function(errors){
                        showErrors(errors);
                    }
                });
            });      // submitting the form when user press yes
} else {
  }
});
}
</script>
@endpush


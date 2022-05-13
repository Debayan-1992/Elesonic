@extends('layouts.frontend.app')
@section('content')
@section('pageheader', 'Carts')
<div class="ptb bd_productlisting">
<div class="container">
<div class="heading text-center"><h2>Cart</h2></div>
     <span class="return_message"></span>
        <div class="row align-items-center justify-content-center">
            <div class="col-12">
             <div class="order-detail-content">
              <div class="heading"><h2>Your Cart  ({{ count($cartDetails)}} items)</h2></div>
               @if(count($cartDetails) > 0)
              <div class="col-md-12">
                <div class="table-responsive">
                <table class="table cart_summary table-striped table-bordered table-hover">
                  <thead class="cart-head">
                    <tr>
                      <th class="text-center">Image</th>
                       <th class="text-center">title</th>
                      <th class="text-center">MRP</th>
                      <th class="text-center">Discount (%)</th>
                      <th class="text-center">Net price</th>
                      <th>Quantity</th>
                      <th class="text-center">Subtotal </th>
                      <th class="action">Action</th>
                    </tr>
                  </thead>

                  <tbody id="show_table">
                    @php
                      $subTotal = 0;
                    @endphp
                        @foreach($cartDetails as $row)
                        @php
                        $subTotal = $subTotal + ($row->cart_item_qty * $row->cart_item_net_price);
                        @endphp
                
                    <tr>
                      <input type="hidden" id="max_odr_{{ $row->cart_item_id }}" value="{{ $row->quantity }}">
                      <input type="hidden" id="stock_qty_{{ $row->cart_item_id }}" value="{{ $row->quantity }}">
                      <td class="cart_product"><a href="{{route('product-details')}}/{{ $row->slug }}"><img src="{{config('app.url')}}/uploads/products/{{ $row->photos }}" height="80" width="80" alt="Product"></a></td>
                      <td class="cart_description"><p class="product-name"><a href="{{route('product-details')}}/{{ $row->slug }}">{{ $row->name }}</a></p>
                      </td>
                      <td class="price"><span>${{ $row->cart_item_price }}</span></td>
                      <td class="price"><span>{{ $row->cart_item_price_disc }}</span></td>
                      <td class="price"><span> ${{ $row->cart_item_net_price }} </span></td>
                      <td class="qty cart-qty" style="width: 130px;">
                        <div class="cart-plus-minus">
                          <div class="numbers-row d-flex flex-wrap justify-content-center align-items-center text-center">

                            <div class="col-3 p-0"><div style="cursor: pointer;" onclick="min_val('{{ $row->cart_item_id }}')" class="dec qtybutton"><i class="fa fa-minus">&nbsp;</i></div></div>

                            <div class="col-6 p-0"><input type="text" class="qty form-control text-center"   id="qty_{{ $row->cart_item_id }}" name="qty" title="Qty" value="{{ $row->cart_item_qty }}" disabled=""></div>

                            <div class="col-3 p-0"><div style="cursor: pointer;" onclick="max_val('{{ $row->cart_item_id }}')" class="inc qtybutton"><i class="fa fa-plus">&nbsp;</i></div></div>
                            
                            <div class="col-12 text-center"><a style="margin-top: 5px;color:blue;cursor: pointer;display: inline-block;" onclick="update_qty('{{ $row->cart_item_id }}','{{ $row->cart_id }}')">Update</a></div>

                            
                          </div>
                        </div>
                      </td>
                       
                      <td class="price"><span>${{ $row->cart_item_qty  * $row->cart_item_net_price}}</span></td>

                      <td class="action"><a href="javascript:void(0)" onclick="del_product('{{ $row->cart_id }}')"><i class="fa fa-trash"></i></a></td>
                    </tr>
                    @endforeach
                  </tbody>
                  <tfoot>

                    
                      <td colspan="2" style="border: none;"></td>
                      <td colspan="2" style="border-left: none;" class="text-right"><strong>Total</strong></td>
                      <td colspan="2" class="text-right"><strong>  ${{$subTotal}}</strong></td>
               
                    </tr>
                  </tfoot>
                </table>
                </div>
              </div>
              @else
              <div></div>
              @endif
              <div class="cart_navigation text-right mr-2">
                @if(count($cartDetails) > 0)
                <a class="checkout-btn" href="{{route('customer.confirm-order')}}"><i class="fa fa-check"></i> Proceed to checkout</a>
                @else
                <div class="no-item">
                  <img src="./public/assets/front/images/no-item.png" alt="#" class="img-fluid">
                  <h3><strong>Your Cart is Empty</strong></h3>
                </div>
               @endif

                 </div>
            </div>           
            </div>
        </div>
  

</div>
</div>

@endsection
<!-- medical-device -->
@push('script')
<script type="text/javascript">

function max_val(id){
var count=$("#qty_"+id).val();
var stock_qty= $("#stock_qty_"+id).val();
  count++;
  $('#qty_'+id).val(count);
  var max_odr_qty=$('#max_odr_'+id).val();
  if(count > max_odr_qty &&  max_odr_qty > 0){
     swal({
        title: 'Order Quantity Exceeded',
        timer: 2000,
        
        showCancelButton: false,
        showConfirmButton: false,
        type:'success'
        }).then(
        function () {},
        function (dismiss) {
          if (dismiss === 'timer') {
          }
        }
      )
    $('#qty_'+id).val(count-1);
}
else if(count > stock_qty)
{
     swal({
        title: 'Order Quantity Exceeded',
        timer: 2000,
        
        showCancelButton: false,
        showConfirmButton: false,
        type:'success'
        }).then(
        function () {},
        function (dismiss) {
          if (dismiss === 'timer') {
          }
        }
      )
    $('#qty_'+id).val(count-1);
}
else
{

}
}
function min_val(id){
var count=$("#qty_"+id).val();
if(count>1)
{
count--;
}
$('#qty_'+id).val(count);
}
function update_qty(pid,aid){
var qty=$('#qty_'+pid).val();
  swal({
 title: "Are you sure?",
 text: "",
 icon: "warning",
 buttons: true,
 dangerMode: true,
})
.then((willDelete) => {
if (willDelete) {
  $.ajax({
    type: "POST",
    dataType: 'Json',
    url: "{{ route('customer.update-product-cart') }}",
    data:{'_token':'{{csrf_token()}}','qty':qty,'pid':pid,'aid':aid},
    async: true,
    success: function (data) {
    if(data==1){
      location.reload();
      }else{
        location.reload();
      }              
    }
});       // submitting the form when user press yes
} else {
  }
});
    
}
function del_product(value)
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
     $.ajax({
     type: "POST",
     dataType: 'Json',
     url: "{{ route('customer.del-product-cart') }}",
    data:{'_token':'{{csrf_token()}}','aid':value},
     async: true,
    success: function (data) {
      if(data==0){
        window.location.href = "{{route('index')}}";
      }else{
        location.reload();
      }             
}
});        // submitting the form when user press yes
} else {
  }
});
}
</script>
@endpush
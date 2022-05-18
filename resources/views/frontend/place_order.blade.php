@extends('layouts.frontend.app')
@section('content')
@section('pageheader', 'Place Order')
<section class="ptb">
   <div class="order-detail-content">
    <div class="container">
       <div class="heading text-center"><h2>Place Order</h2></div>
    <div class="row align-items-center justify-content-center mb-4">
    @if(count($cartDetails) > 0)
              <div class="col-md-10 col-12">
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
                    </tr>
                  </thead>

                  <tbody id="show_table">
                      @foreach($cartDetails as $row)
                    <tr>
                      <input type="hidden" id="max_odr_{{ $row->cart_item_id }}" value="{{ $row->quantity }}">
                      <input type="hidden" id="stock_qty_{{ $row->cart_item_id }}" value="{{ $row->quantity }}">
                      <td class="cart_product"><a href="{{route('product-details')}}/{{ $row->slug }}"><img src="{{config('app.url')}}/uploads/products/{{ $row->photos }}" height="80" width="80" alt="Product"></a></td>
                      <td class="cart_description"><p class="product-name"><a href="{{route('product-details')}}/{{ $row->slug }}">{{ $row->name }}</a></p>
                      </td>
                      <td class="price"><span>${{ $row->cart_item_price }}</span></td>
                      <td class="price"><span>{{ $row->cart_item_price_disc }}</span></td>
                      <td class="price"><span> ${{ $row->cart_item_net_price }} </span></td>
                      <td class="price"><span> {{ $row->cart_item_qty }} </span></td>
                      <td class="price"><span>${{ $row->cart_item_qty  * $row->cart_item_net_price}}</span></td>

                    
                    </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="2" style="border: none;"></td>
                      <td colspan="2" style="border-left: none;" class="text-right"><strong>Shipping Charges</strong></td>
                      <td colspan="2" class="text-right"><strong>  ${{$shippingCharges}}</strong></td>
                      </tr>
                    <tr>
                      <td colspan="2" style="border: none;"></td>
                      <td colspan="2" style="border-left: none;" class="text-right"><strong>Total</strong></td>
                      <td colspan="2" class="text-right"><strong>  ${{$subTotal}}</strong></td>
               
                    </tr>
                  </tfoot>
                </table>
              </div>
              @endif
              @if(!empty($shippingAddress))
              <div class="col-12 text-center">
                <input type="radio" value="cod" name="payment_type" checked> Cash On Delivery
              </div>
              <div class="col-12 text-center">
                <input type="radio" value="online" name="payment_type" value="online"> Online
              </div>

              <!-- <div class="col-12 text-center">
                 <div id="paypal-button"></div>
              </div> -->

            <!--   <button id="afterpay-button">
              Afterpay it!
            </button> -->
            <div class="col-12 text-center mt-3">
                
                <a class="bd_btn btn-save" onclick="placeOrder()">Place Now</a>
                
             </div>
              <div class="col-12 text-center mt-3">
                
                 <a class="bd_btn btn-save" href="{{route('customer.confirm-order')}}">Back</a>
                 
              </div>
           @else
               <div class="col-12 text-center">
                Add shipping address
              </div>
            @endif
              
            </div>
            </div>
            </div>
</section>
@endsection
@push('script')
<script>
  function placeOrder(){
    var paymentType = $('input[name="payment_type"]:checked').val(); //cod or online
    //alert(paymentType);
    $.ajax({
			url: "{{ route('customer.order-now') }}",
            type: "get",
            datatype: "json",
			      data:{'_token':'{{csrf_token()}}','paymentType':paymentType},
            beforeSend: function (){
              $("#loadList").css('display','block');
          },
          success: function (data) {
            if(data==1){
            $("#loadList").css('display','none');
            window.location.href = "{{route('customer.my-order')}}";
            }
            
          }
      });
  }
</script>
@endpush

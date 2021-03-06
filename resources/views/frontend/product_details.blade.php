@extends('layouts.frontend.app')
@section('content')
@section('pageheader', $product->name)
	<div class="breadcrumb_block">
		<div class="container">	  
			<ul>

				<li><a href="{{config('app.url')}}" title="Home">Home</a></li>

				<li><i class="fa fa-angle-right"></i></li>

				<li><a href="{{route('product-list')}}/{{ $product->category->slug }}">{{ $product->category->name}}</a></li>

				<!-- <li><i class="fa fa-angle-right"></i></li> -->

				<!-- <li><span>X Ray Machine</span></li> -->

			</ul>
		</div>
	</div>
<!-- top-block -->



<!-- product-details -->
	<div class="product-details">
		<div class="container">
			<div class="row">

				<!-- item -->
				<div class="col-lg-5">
					<div class="item-lt">

					<div class="zoomerbd" >

						  <div class="large-5 column">
						    <div class="xzoom-container">

						      <img class="xzoom" id="xzoom-default" src="{{config('app.url')}}/uploads/products/{{ $product->photos }}" xoriginal="{{config('app.url')}}/uploads/products/{{ $product->photos }}" />

						      <div class="xzoom-thumbs">
						      	@if(!empty($relatedImage))	
									@foreach($relatedImage as $row)			          
										<a href="{{config('app.url')}}/uploads/products/{{ $row->image }}"><img class="xzoom-gallery" src="{{config('app.url')}}/uploads/products/{{ $row->image }}" ></a>
									@endforeach
						        @endif
						        <!-- <a href="{{asset('custom_resource/images/elesonic/img3.png')}}"><img class="xzoom-gallery" src="{{asset('custom_resource/images/elesonic/img3.png')}}" ></a>

						        <a href="{{asset('custom_resource/images/elesonic/img2.png')}}" title="big images">
						        	<img class="xzoom-gallery" src="{{asset('custom_resource/images/elesonic/img2.png')}}"  >
						        </a> -->

						      </div>
						    </div>        
						  </div>

						  <div class="large-7 column"></div>

						</div>

						<!---->
					</div>
				</div>
				<!-- item -->

				<!-- item -->
				<div class="col-lg-7">
					<div class="item-rt">
						<h3>{{ $product->name}}</h3>
						<h4>{{ $product->category->name}}</h4>

						<div class="price-block">
							<h5>$ {{ $product->unit_price}}</h5>
							<span>$ {{ $product->purchase_price}}</span>
						</div>
						@if($role_id != 6)
						<ul class="button-block">
							@if($product->quantity > 0)
							<li><a href="javascript:void(0)" id="addCartBtn" onclick="addCart('{{ $product->id }}','{{ $product->quantity }}')">ADD TO CART</a></li>
							 <li><a style="display: none" href="{{route('customer.carts')}}"  id="checkoutCartBtn">Go To Cart</a></li> 
							 <li><a href="javascript:void(0)" id="addCartBtn" onclick="buyNow('{{ $product->id }}','{{ $product->quantity }}')">BUY NOW</a></li>
							@else
							<li><a href="javascript:void(0)">Out Of Stock</a></li>    
							@endif
						</ul>
						@endif

						<h6>{{ $product->name}}</h6>
						{{  strip_tags($product->description)}}
						<!-- <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.
						<br><br>
						It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p> -->


					</div>
				</div>
				<!-- item -->

			</div>
		</div>
	</div>
<!-- product-details -->



<!-- popular-products -->
@if(count($relatedProducts)>0)
	<div class="related-products">
		<div class="container">

				<div class="title">
					<h3>Related Products</h3>
				</div>


				<!---->
				
				<div class="slider-bd">
					<div id="related-products" class="owl-carousel">

						<!-- item -->
						@if(!empty($relatedProducts))
						@foreach($relatedProducts as $row)
						<div class="item">
							<a href="{{route('product-details')}}/{{ $row->slug }}">
								<div class="img-figure">
									<img src="{{config('app.url')}}/uploads/products/{{ $row->photos }}">
								</div>

								<div class="text-block">
									<span>{{ $row->category->name}}</span> 
									<p>{{ $row->name}}</p>
									<h6>$ {{ $row->unit_price}}</h6>
								</div>

							</a>						
						</div>
						@endforeach
						@endif
						<!-- item -->
						
					</div>
				</div>
			<!---->
		</div>
	</div>
@endif
@endsection
@push('script')
<script>
	
	function addCart(Id,qty){
        var product_quantity = 1;
        if(qty == 0 || qty < 0){
            swal('Out of Stock');
			return false;
        }
        else{
            $.ajax({
			url: "{{ route('add-cart') }}",
            type: "post",
            datatype: "json",
			data:{'_token':'{{csrf_token()}}','Id':Id,'product_quantity':product_quantity},
            beforeSend: function (){
               
            },
            success: function (data) {
               $("#myCrtItem").text(data);
               //$("#againCart").attr('value', 'Add to Cart Again');
               swal("Item Successfully Added");
               $("#addCartBtn").hide();
               $("#checkoutCartBtn").show();
            }
          });
        }
    }
</script>
<script>
	
	function buyNow(Id,qty){
        var product_quantity = 1;
        if(qty == 0 || qty < 0){
            swal('Out of Stock');
			return false;
        }
        else{
            $.ajax({
			url: "{{ route('add-cart') }}",
            type: "post",
            datatype: "json",
			data:{'_token':'{{csrf_token()}}','Id':Id,'product_quantity':product_quantity},
            beforeSend: function (){
               
            },
            success: function (data) {
               $("#myCrtItem").text(data);
			   window.location.href = "{{route('customer.carts')}}";
            }
          });
        }
    }
</script>
@endpush
<!-- popular-products -->

















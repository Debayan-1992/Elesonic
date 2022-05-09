@extends('layouts.frontend.app')
@section('content')
@section('pageheader', 'Product-list')
	<div class="breadcrumb_block">
		<div class="container">	  
			<ul>

				<li>
					<a href="#" title="Home">Home</a>
				</li>

				<li>
					<i class="fa fa-angle-right"></i>
				</li>

				<li>
					<span id="getfilterCat">{{ $category->name }}</span>
				</li>

			</ul>
		</div>
	</div>
<!-- top-block -->



<!-- medical-device -->
	<div class="medical-device">
		<div class="container">
			<div class="row">

				<!-- item -->
					<div class="col-lg-3">
						<div class="lt-block">

							<!-- item -->
							<div class="item-block">
								<!-- //////////////// -->

									<div class="accordion" id="faq">

										<!-- item -->
										<div class="item">

										    <div class="card-header" id="faqhead1">
										        <a href="javascript:void(0)" class="btn btn-header-link" data-toggle="collapse" data-target="#faq1" aria-expanded="true" aria-controls="faq1">Category</a>
										    </div>

										    <div id="faq1" class="collapse show" aria-labelledby="faqhead1" data-parent="#faq">
										        <div class="card-body">

										        	<!---->
										            <div class="search-group">

														
														<input type="search" placeholder="Search..." class="form-control" />

														<button type="button" class="btn btn-primary">
															<i class="fa fa-search"></i>
														</button>
										            </div>
										            <!---->

										            <!-- check box block -->
										            <div class="checkbox-sec">
											            <ul>
															@foreach($parentCategory as $row)
											            	<li>
											            		<input type="radio" {{ ($row->id==$cat_id)? "checked" : "" }} name="catId" onclick="getFilterdata('{{ $row->name }}')" value="{{ $row->id }}">
											            		<p>{{ $row->name }} </p>
											            	</li>
															@endforeach

											            	

											            </ul>

											        </div>
										            <!-- check box block -->

										        </div>
										    </div>
										</div>
										<!-- item -->

										<!-- ///////////////////////////////////////// -->

										<!-- item -->
										<div class="item">

										    <div class="card-header" id="faqhead2">
										        <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq2"
										        aria-expanded="true" aria-controls="faq2">Price</a>
										    </div>


										     <div id="faq2" class="collapse " aria-labelledby="faqhead2" data-parent="#faq">
										        <div class="card-body">


										            <!-- check box block -->
										            <div class="price-block">
										            	<h5>Custom Price In USD</h5>
										            	<div class="form-bd">
										            		<form>
										            			<input type="number" id="min_price" placeholder="Minimum Price">
										            			<input type="number" id="max_price" placeholder="Maximum Price">
										            			<button type="button" onclick="getFilterdata()" class="btn btn-primary">Apply</button>
										            		</form>
										            	</div>
											        </div>
										            <!-- check box block -->

										        </div>
										    </div>
										</div>
										<!-- item -->



										<!-- item -->
										<div class="item">

										    <div class="card-header" id="faqhead3">
										        <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq3"
										        aria-expanded="true" aria-controls="faq2">Brand</a>
										    </div>


										     <div id="faq3" class="collapse " aria-labelledby="faqhead3" data-parent="#faq">
										        <div class="card-body">

										        	<!---->
										            <div class="search-group">

														
														<input type="search" placeholder="Search..." class="form-control" />

														<button type="button" class="btn btn-primary">
															<i class="fa fa-search"></i>
														</button>
										            </div>
										            <!---->

										            <!-- check box block -->
										            <div class="checkbox-sec">
											            <ul>
															@if(!empty($filterBrands))
																@foreach($filterBrands as $row)
																
																	<li>
																		<input onclick="getFilterdata('')" type="checkbox" value="{{ stristr($row,"-",true) }}" name="brandId">
																		<p>{{ stristr($row," ") }}</p>
																	</li>
																@endforeach
															@endif
											            	

											            </ul>

											        </div>
										            <!-- check box block -->

										        </div>
										    </div>
										</div>
										<!-- item -->
										

									</div>

								<!-- //////////////// -->
							</div>
							<!-- item -->

						</div>
					</div>
				<!-- item -->

				<!-- item -->
					<div class="col-lg-9">
						<div class="rt-block">
							<!-- item -->
							<div class="right-top-sec">
								<h3><span id="filterwiseCat"></span></h3>
								<div class="rt">
									<p>( Showing <span id="filterWiseCountPro">{{count($products)}}</span> out of {{count($productsCount)}} products )</p>
									<div class="select-price">
										<span>Sort By Price</span>
										<select id="sort_by_price" onchange="getFilterdata('')">
										    <option value=" ">Choose</option>
											<option value="h_t_l">Price High to Low</option>
											<option value="l_t_h">Price Low to High</option>
											
										</select>
									</div>
								</div>
							</div>
							<!-- item -->

							<!---->
							<div class="medical-product-block" id="getFilterData">
								<div class="row">

									<!-- item -->
									@if(!empty($products))
										@foreach($products as $row)
										<div class="col-lg-3 col-md-6 col-sm-12 comn-px0">
											<div class="item">
												<a href="{{route('product-details')}}/{{ $row->slug }}">
													<div class="img-figure">
													<img src="{{config('app.url')}}/uploads/products/{{ $row->photos }}">
													</div>

													<div class="text-block">
														<span>{{ $row->category->name }}</span>
														<p>{{ $row->name }}</p>
														<h6>${{ $row->unit_price }}</h6>
													</div>

												</a>						
											</div>
										</div>
									@endforeach
									@endif
									<!-- item -->
								</div>


								<!-- pasination -->
								<div class="medical-pasination">
									{{$products->links()}}
									  <!-- <ul class="pagination">
									    <li class="page-item active"><a href="#">1</a></li>
									    <li class="page-item"><a href="#">2</a></li>
									    <li class="page-item"><a href="#">3</a></li>
									    <li class="page-item"><a href="#">4</a></li>
									    <li class="page-item"><a href="#">5</a></li>
									    <li class="page-item"><a href="#"><i class="fa fa-angle-right"></i></a></li>
									  </ul> -->
								</div>
								<!-- pasination -->


							</div>
							<!---->


						</div>
					</div>
				<!-- item -->

			</div>
		</div>
	</div>
	@endsection
<!-- medical-device -->
@push('script')
<script>
function getFilterdata(catName){
	var catId = $('input[name="catId"]:checked').val();
	var brandId = [];
	var priceShort = $("#sort_by_price").val();
	var min_price = $("#min_price").val();
	var max_price = $("#max_price").val();
	
    $("input:checkbox[name=brandId]:checked").each(function(){
        brandId.push($(this).val());
    });
	$.ajax({
			url: "{{ route('get-filter-data') }}",
			method: "POST",
			data: {'_token':'{{csrf_token()}}','catId':catId, 'brandId':brandId,'priceShort':priceShort,'min_price':min_price,'max_price':max_price},
			beforeSend: function (){
              $("#loadList").css('display','block');
            },
			success: function(data){
				//$("#filterwiseCat").text(catName);
				//$("#getfilterCat").text(catName);
				
				$("#getFilterData").html(data);
				$("#filterWiseCountPro").text($("#filterCountProduct").val());
				$("#loadList").css('display','none');
			}, error: function(errors){
				showErrors(errors);
			}
		});
}
</script>
@endpush













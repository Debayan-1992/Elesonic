@extends('layouts.frontend.app')
@section('content')

<!-- banner-block -->
	<div class="banner-block">

		<div class="banner-bd">
			<div id="banner-slider" class="owl-carousel">

				<!-- item -->
				@if($banners)
					@foreach($banners as $row)
					<div class="item">
						
						<img src="{{config('app.url')}}/uploads/banners/{{ $row->image }}">
						{{-- <img src="{{asset('uploads/banners/'.$row->image) }}"> --}}
					</div>
					@endforeach
				@endif
				<!-- item -->

				<!-- item -->
				<!-- <div class="item">
					<img src="{{asset('custom_resource/images/banner1.jpg')}}">
				</div> -->
				<!-- item -->
			</div>
		</div>

		<!-- text-block -->
			<div class="text-block">
				<div class="container">				
					<h1>{{ $titles->banner_title_one }}<br> {{ $titles->banner_title_two }}</h1>
					{{ $titles->banner_description }}
				</div>
			</div>
		<!-- text-block -->
	</div>
<!-- banner-block end-->


<!-- departments-block start-->
@if(!empty($departments))
	<div class="departments-block">
		<div class="container">
			<div class="top-block">
				<h3>Your favorite departments</h3>
				<a href="{{route('departments')}}">VIEW ALL</a>
			</div>

			<div class="departments-bd">
				<div class="row">

					<!-- item -->
					@foreach($departments as $row)
						<div class="col-lg-2">
							<div class="item">
								<a href="#">
									<div class="figure">
									<img src="{{config('app.url')}}/uploads/departments/{{ $row->image }}">
									</div>
									<p>{{ $row->name }}</p>
								</a>
							</div>
						</div>
						@endforeach
					<!-- item -->
			
				</div>
			</div>

		</div>
	</div>
	@endif
<!-- departments-block end -->


<!-- popular-products -->
@if(!empty($popularproducts))
	<div class="popular-products">
		<div class="container">
			<h3>Popular Products</h3>

			<!---->
				<div class="slider-bd">
					<div id="products-slider" class="owl-carousel">
						@foreach($popularproducts as $row)
						<!-- item -->
						<div class="item">
							<a href="{{route('product-details')}}/{{ $row->slug }}">
								<div class="img-figure">
									<img src="{{config('app.url')}}/uploads/products/{{ $row->photos }}">
								</div>

								<p>{{ $row->name }}</p>

							</a>						
						</div>
						@endforeach
						<!-- item -->
						
					</div>
				</div>
			<!---->
		</div>
	</div>
	@endif
<!-- popular-products -->




<!-- popular-services-block start-->
@if(!empty($services))
	<div class="popular-services-block">
		<div class="container">
			<div class="top-block">
				<h3>Popular services</h3>
				<a href="{{route('services')}}">VIEW ALL</a>
			</div>

			<div class="departments-bd">
				<div class="row">

					<!-- item -->
					@foreach($services as $row)
						<div class="col-lg-2">
							<div class="item">
							<a href="#" data-toggle="modal" onclick="serviceBokkingModal('{{ $row->id }}','{{ $row->name }}')" >
									<div class="figure">
									<img src="{{config('app.url')}}/uploads/services/{{ $row->image }}">
									</div>
									<p>{{ $row->name }}</p>
								</a>
							</div>
						</div>
						@endforeach
					<!-- item -->
				</div>
			</div>

		</div>
	</div>
	@endif
<!-- departments-block end -->



<!-- popular-products -->
	<div class="elesonic-block">
		<div class="container">
			<h3>Best of ELESONIC</h3>

			<!---->
				<div class="slider-bd">
					<div id="elesonic-slider" class="owl-carousel">

						<!-- item -->
						<div class="item">
							<a href="#">
								<div class="img-figure">
									<img src="{{asset('custom_resource/images/elesonic/img1.png')}}">
								</div>

								<div class="text-block">
									<span>Radiology</span>
									<p>Medical Diagnostic X-ray equipments</p>
									<h6>$ 27,720</h6>
								</div>

							</a>						
						</div>
						<!-- item -->

						<!-- item -->
						<div class="item">
							<a href="#">
								<div class="img-figure">
									<img src="{{asset('custom_resource/images/elesonic/img2.png')}}">
								</div>

								<div class="text-block">
									<span>CT SEGMENT</span>
									<p>High quality & cost effective CT Scan</p>
									<h6>$ 27,720</h6>
								</div>

							</a>						
						</div>
						<!-- item -->

						<!-- item -->
						<div class="item">
							<a href="#">
								<div class="img-figure">
									<img src="{{asset('custom_resource/images/elesonic/img3.png')}}">
								</div>

								<div class="text-block">
									<span>MRI SEGMENT</span>
									<p>Medical Diagnostic X-ray equipments</p>
									<h6>$ 27,720</h6>
								</div>

							</a>						
						</div>
						<!-- item -->

						<!-- item -->
						<div class="item">
							<a href="#">
								<div class="img-figure">
									<img src="{{asset('custom_resource/images/elesonic/img1.png')}}">
								</div>

								<div class="text-block">
									<span>Radiology</span>
									<p>Medical Diagnostic X-ray equipments</p>
									<h6>$ 27,720</h6>
								</div>

							</a>						
						</div>
						<!-- item -->

						<!-- item -->
						<div class="item">
							<a href="#">
								<div class="img-figure">
									<img src="{{asset('custom_resource/images/elesonic/img2.png')}}">
								</div>

								<div class="text-block">
									<span>CT SEGMENT</span>
									<p>High quality & cost effective CT Scan</p>
									<h6>$ 27,720</h6>
								</div>

							</a>						
						</div>
						<!-- item -->

						<!-- item -->
						<div class="item">
							<a href="#">
								<div class="img-figure">
									<img src="{{asset('custom_resource/images/elesonic/img3.png')}}">
								</div>

								<div class="text-block">
									<span>MRI SEGMENT</span>
									<p>Medical Diagnostic X-ray equipments</p>
									<h6>$ 27,720</h6>
								</div>

							</a>						
						</div>
						<!-- item -->

						<!-- item -->
						<div class="item">
							<a href="#">
								<div class="img-figure">
									<img src="{{asset('custom_resource/images/elesonic/img1.png')}}">
								</div>

								<div class="text-block">
									<span>Radiology</span>
									<p>Medical Diagnostic X-ray equipments</p>
									<h6>$ 27,720</h6>
								</div>

							</a>						
						</div>
						<!-- item -->

						<!-- item -->
						<div class="item">
							<a href="#">
								<div class="img-figure">
									<img src="{{asset('custom_resource/images/elesonic/img2.png')}}">
								</div>

								<div class="text-block">
									<span>CT SEGMENT</span>
									<p>High quality & cost effective CT Scan</p>
									<h6>$ 27,720</h6>
								</div>

							</a>						
						</div>
						<!-- item -->

						<!-- item -->
						<div class="item">
							<a href="#">
								<div class="img-figure">
									<img src="{{asset('custom_resource/images/elesonic/img3.png')}}">
								</div>

								<div class="text-block">
									<span>MRI SEGMENT</span>
									<p>Medical Diagnostic X-ray equipments</p>
									<h6>$ 27,720</h6>
								</div>

							</a>						
						</div>
						<!-- item -->

						<!-- item -->
						<div class="item">
							<a href="#">
								<div class="img-figure">
									<img src="{{asset('custom_resource/images/elesonic/img1.png')}}">
								</div>

								<div class="text-block">
									<span>Radiology</span>
									<p>Medical Diagnostic X-ray equipments</p>
									<h6>$ 27,720</h6>
								</div>

							</a>						
						</div>
						<!-- item -->

						<!-- item -->
						<div class="item">
							<a href="#">
								<div class="img-figure">
									<img src="{{asset('custom_resource/images/elesonic/img2.png')}}">
								</div>

								<div class="text-block">
									<span>CT SEGMENT</span>
									<p>High quality & cost effective CT Scan</p>
									<h6>$ 27,720</h6>
								</div>

							</a>						
						</div>
						<!-- item -->

						<!-- item -->
						<div class="item">
							<a href="#">
								<div class="img-figure">
									<img src="{{asset('custom_resource/images/elesonic/img3.png')}}">
								</div>

								<div class="text-block">
									<span>MRI SEGMENT</span>
									<p>Medical Diagnostic X-ray equipments</p>
									<h6>$ 27,720</h6>
								</div>

							</a>						
						</div>
						<!-- item -->

						

						
					</div>
				</div>
			<!---->
		</div>
	</div>
<!-- popular-products -->



<!-- client-logo -->
	<div class="client-logo">
		<div class="container">

				<!---->
					<div class="slider-bd">
						<div id="logo-slider" class="owl-carousel">

							<!-- item -->
							<div class="item">
								<div class="img-figure">
									<img src="{{asset('custom_resource/images/logo/logo1.jpg')}}">
								</div>
							</div>
							<!-- item -->

							<!-- item -->
							<div class="item">
								<div class="img-figure">
									<img src="{{asset('custom_resource/images/logo/logo2.jpg')}}">
								</div>
							</div>
							<!-- item -->

							<!-- item -->
							<div class="item">
								<div class="img-figure">
									<img src="{{asset('custom_resource/images/logo/logo3.jpg')}}">
								</div>
							</div>
							<!-- item -->

							<!-- item -->
							<div class="item">
								<div class="img-figure">
									<img src="{{asset('custom_resource/images/logo/logo4.jpg')}}">
								</div>
							</div>
							<!-- item -->

							<!-- item -->
							<div class="item">
								<div class="img-figure">
									<img src="{{asset('custom_resource/images/logo/logo3.jpg')}}">
								</div>
							</div>
							<!-- item -->

							<!-- item -->
							<div class="item">
								<div class="img-figure">
									<img src="{{asset('custom_resource/images/logo/logo4.jpg')}}">
								</div>
							</div>
							<!-- item -->

							<!-- item -->
							<div class="item">
								<div class="img-figure">
									<img src="{{asset('custom_resource/images/logo/logo4.jpg')}}">
								</div>
							</div>
							<!-- item -->
							

						</div>
					</div>
				<!---->

			<!-- ///////////////// -->
		</div>
	</div>
<!-- client-logo -->




@endsection


<!-- js-link -->

















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
				<a href="#">VIEW ALL</a>
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
							<a href="#">
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
				<a href="#">VIEW ALL</a>
			</div>

			<div class="departments-bd">
				<div class="row">

					<!-- item -->
					@foreach($services as $row)
						<div class="col-lg-2">
							<div class="item">
								<a href="#">
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



<!-- footer-block start-->
	<div class="footer-block">
		<div class="container">

			<div class="footer-bd">
				<div class="row">

					
					<!-- item -->
						<div class="col-lg-3">
							<div class="item quick-links-block">
								<h4>CATEGORIES</h4>
								<ul>
									<li><a href="#">Consumables & Disposables</a></li>
									<li><a href="#">Lab Diagnostics & Instruments</a></li>
									<li><a href="#">Medical Device & Equipment</a></li>
									<li><a href="#">Dental</a></li>
									<li><a href="#">Medical Implants</a></li>
									<li><a href="#">Surgical Instruments</a></li>
									<li><a href="#">Hospital Establishment</a></li>
								</ul>
							</div>
						</div>
					<!-- item -->
					
					<!-- item -->
					<div class="col-lg-3">
						<div class="item quick-links-block">
							<h4>SERVICES</h4>
							<ul>
								<li><a href="#">Sell on Elesonic</a></li>
								<li><a href="#">Annual Maintenance Contract</a></li>
								<li><a href="#">Ultrasound Machine Service</a></li>
								<li><a href="#">Oxygen Concentrator Service</a></li>
								<li><a href="#">Write for Us</a></li>
							</ul>
						</div>
					</div>
					<!-- item -->

					<!-- item -->
						<div class="col-lg-2">
							<div class="item quick-links-block">
								<h4>ABOUT</h4>
								<ul>
									<li><a href="#">Our Story</a></li>
									<li><a href="#">Contact Us</a></li>
									<li><a href="#">News</a></li>
									<li><a href="#">Blogs</a></li>
								</ul>
							</div>
						</div>
					<!-- item -->

				

					<!-- item -->
					<div class="col-lg-4 ">
						<div class="item submit-mail">
							<h4>Subscribe for our newsletter</h4>


							<form class="formbd-sec">
								<input type="email" placeholder="Email address" name="">
								<div class="submit-btn">
									<input type="submit" name="">
								</div>
							</form>

							<div class="social-icon">
								<ul>
									<li><a href="#" target="_blank"><i class="fa fa-facebook"></i></a></li>
									<li><a href="#" target="_blank"><i class="fa fa-twitter"></i></a></li>
									<li><a href="#" target="_blank"><i class="fa fa-linkedin"></i></a></li>
									<li><a href="#" target="_blank"><i class="fa fa-instagram"></i></a></li>
									
								</ul>
							</div>

						</div>
					</div>
					<!-- item -->


				</div>
			</div>

			<!--copyright-->
			<div class="copyright-block">

				<!---->
				<div class="item">
					<div class="menu-sec">
						<ul>
							<li><a href="#">Terms of Use</a></li>
							<li><a href="#">Privacy Policy</a></li>
							<li><a href="#">FAQs</a></li>
							<li><a href="#">Return Policy</a></li>
							<li><a href="#">Disclaimer</a></li>
						</ul>
					</div>
				</div>
				<!---->


				<!---->
				<div class="item">

					<p>&copy; 2021 Elesonic | Website designed and developed by</p>
					<a href="https://www.ivaninfotech.com/" target="_blank">Ivan Infotech</a>
				</div>
				<!---->

				

			</div>
			<!-- copyright -->

		</div>
	</div>
<!-- footer-block  end -->

@endsection


<!-- js-link -->

















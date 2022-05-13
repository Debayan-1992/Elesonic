@extends('layouts.frontend.app')
@section('content')

<!-- inr-banner start -->

<div class="inr-banner">

	<div class="figure">
		<img src="{{asset('custom_resource/images/banner1.jpg')}}">
	</div>

	<div class="text-block">
		<div class="container">

			<h3>Contact Us</h3>
			<div class="breatcome_content">
				<ul>
					<li><a href="{{route('index')}}">Home</a> <i class="fa fa-angle-right"></i> 
					 <span>Contact Us</span></li>
				</ul>
			</div>

		</div>
	</div>
</div>


<!-- contact-block end -->

	<div class="contact-block">
		<div class="container">

			<div class="bd-block">

				<!-- item -->
				<div class="lt-block">
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
					<h4>Send Us a Message</h4>
					<div class="form-bd">
						<form action="{{route('contact_us')}}" method="post">
						@csrf
							<div class="item">
								<input type="text" placeholder="First Name" name="name">
							</div>

							<div class="item">
								<input type="email" placeholder="Email" name="email">
							</div>

							<div class="item">
								<input type="text" placeholder="Phone" name="mobile">
							</div>

							<div class="item">
								<textarea placeholder="Message" name="message"></textarea>
							</div>

							<div class="item submit-mail-sec">
								<input type="submit" name="">
							</div>

						</form>
					</div>
				</div>
				<!-- item -->

				<!-- item -->
				<div class="rt-block">

					<!-- item -->
						<div class="item">
							<div class="icon">
								<span>
									<i class="fa fa-phone" aria-hidden="true"></i>
								</span>
							</div>
							<div class="text-sec">
								<ul>

									<li>
										<p>{{$setting->site_number_office_name}}</p>
										<a href="#">{{$setting->site_number}}</a>
									</li>

									{{-- <li>
										<p>Kolkata Factory</p>
										<a href="#">+91-33-2470 0066</a>
									</li> --}}

								</ul>
							</div>
						</div>
					<!-- item -->

					<!-- item -->
						<div class="item">
							<div class="icon">
								<span>
									<i class="fa fa-envelope-o"></i>
								</span>
							</div>
							<div class="text-sec">
								<ul>

									<li>
										<a href="#">{{$setting->site_email}}</a>
									</li>

									{{-- <li>
										<a href="#">elesonic@rediffmail.com</a>
									</li>

									<li>
										<a href="#">info@elesonicnigeria.com</a>
									</li>

									<li>
										<a href="#">elesonicnigeria@gmail.com</a>
									</li>

									<li>
										<a href="#">elesonic.uganda@gmail.com</a>
									</li> --}}

								</ul>
							</div>
						</div>
					<!-- item -->

					<!-- item -->
						<div class="item">
							<div class="icon">
								<span>
									<i class="fa fa-globe"></i>
								</span>
							</div>
							<div class="text-sec">
								<ul>

									<li>
										<a href="{{$setting->site_link}}">{{$setting->site_link}}</a>
									</li>

									{{-- <li>
										<a href="#">www.elesonicgroup.co.in</a>
									</li>

									<li>
										<a href="#">www.elesonic.ca</a>
									</li>

									<li>
										<a href="#">www.elesonicnigeria.com</a>
									</li>

									<li>
										<a href="#">www.elesonicuganda.com</a>
									</li>

									<li>
										<a href="#"></a>
									</li> --}}
									
								</ul>
							</div>
						</div>
					<!-- item -->
				</div>
				<!-- item -->

			</div>


			<!-- location-block -->
			<div class="location-block">
				<div class="row">

					<!-- item -->
					<div class="col-lg-4">
						<div class="item-block">
							<span><i class="fa fa-map-marker"></i></span>
							{!!$setting->address1!!}
						</div>
					</div>
					<!-- item -->

					<!-- item -->
					<div class="col-lg-4">
						<div class="item-block">
							<span><i class="fa fa-map-marker"></i></span>
							{!!$setting->address2!!}
						</div>
					</div>
					<!-- item -->

					<!-- item -->
					<div class="col-lg-4">
						<div class="item-block">
							<span><i class="fa fa-map-marker"></i></span>
							{!!$setting->address3!!}
						</div>
					</div>
					<!-- item -->

				</div>
			</div>
			<!-- location-block -->

			
		</div>
	</div>

<!-- contact-block -->


<!-- map-block -->

	<div class="map-block">
		{!!$setting->map_embed_link!!}
	</div>

<!-- map-block -->




<!--  -->

<div class="location-block" style="display: none;">
	<!-- item -->
		<div class="item">
			<div class="icon">
				<span>
					<i class="fa fa-map-marker"></i>

				</span>
			</div>
			<div class="text-sec">
				<h6>Elesonic India National Sales Office</h6>
				<p>Fortuna Building, Unit No. 506, Pimple Saudagar, Wakad, Pune, Maharashtra</p>
			</div>
		</div>
	<!-- item -->

	<!-- item -->
		<div class="item">
			<div class="icon">
				<span>
					<i class="fa fa-map-marker"></i>
				</span>
			</div>
			<div class="text-sec">
				<h6>Elesonic Medical Systems Canada Inc.</h6>
				<p>2000 McGill College Avenue, 6th Floor, Montreal, Quebec,H3A 3H3, Canada</p>
			</div>
		</div>
	<!-- item -->

	<!-- item -->
		<div class="item">
			<div class="icon">
				<span>
					<i class="fa fa-map-marker"></i>
				</span>
			</div>
			<div class="text-sec">
				<h6>Elesonic Medical Systems Nigeria Ltd.</h6>
				<p>Lagos, Nigeria,Nigeria – Local Contact : Harshit Vasavada</p>
			</div>
		</div>
	<!-- item -->
</div>

<!-- location-block -->


@endsection
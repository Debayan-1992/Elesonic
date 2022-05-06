<!DOCTYPE html>
<html>

<head>
	
	<title>@yield('pageheader', 'Elesonic') | {{config('app.name', 'Laravel')}} - {{config('app.title', 'Laravel E-Commerce')}}</title>
	  <meta charset="utf-8">
	  <meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="shortcut icon" href="{{asset('custom_resource/images/favicon.ico')}}" type="image/x-icon">
	<link rel="icon" href="{{asset('custom_resource/images/favicon.ico')}}" type="image/x-icon">

	<!-- font -->
	<link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&display=swap" rel="stylesheet">

	<!-- font-family: 'Abril Fatface', cursive; -->

	<!-- font -->
<!-- font-awesome -->
	<link rel="stylesheet" href="{{asset('custom_resource/css/font-awesome.min.css')}}">
	<!-- font-awesome -->

	 <link rel="stylesheet" type="text/css" href="{{asset('custom_resource/css/bootstrap.min.css')}}">


	<!-- owl-slider -->
	<link rel="stylesheet" type="text/css" href="{{asset('custom_resource/css/owl.carousel.min.css')}}">
	<!-- owl-slider -->
    <!-- zoomer -->
		<link rel='stylesheet' href="{{asset('custom_resource/css/zoomer/xzoom.css')}}">
	<!-- zoomer -->
	

	<link rel="stylesheet" type="text/css" href="{{asset('custom_resource/css/style.css')}}">
	
	@yield('header')
	@stack('header')
	 <!-- countru-code -->

</head>
<body>
 <div id="loadList" style="text-align: center; display: none;position: fixed;top: 0;left: 0;width: 100%;height: 100%;z-index: 999;background: rgba(0, 0, 0, 0.8);" class="loader">
  <div style="width: 100%;height: 100%;display: flex;flex-wrap: nowrap;justify-content: center;align-items: center;">
    <div class="col-12">
      <img src="https://img.icons8.com/material-outlined/96/ffffff/spinner--v4.png"><br>

    <p style="color:  #fff;">Please wait... </p> 
    </div>
    
  </div>

</div>
<!-- header start -->

	<header class="header-block">
		<nav class="navbar navbar-expand-lg ">
			<div class="container-fluid">

				<div class="logo-block">
				  <a class="navbar-brand" href="{{route('index')}}"><img src="{{asset('custom_resource/images/logo.png')}}"></a>
				</div>

				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
				</button>


				<div class="collapse navbar-collapse navbar-manu-block" id="navbarSupportedContent">
					<ul class="navbar-nav">

					  <li class="nav-item department dropdown">
					    <a class="nav-link" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					      Department
					    </a>
					    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
					      <a class="dropdown-item" href="#">Action</a>
					      <a class="dropdown-item" href="#">Another action</a>
					    </div>
					  </li>

					  <li class="nav-item services dropdown">
					    <a class="nav-link " href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					      Services
					    </a>
					    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
					      <a class="dropdown-item" href="#">Action</a>
					      <a class="dropdown-item" href="#">Another action</a>
					    </div>
					  </li>
					</ul>
				</div>

				<!---->
					<div class="right-block">

						<div class="search-block">
					  	<form>
					      <input class="form-control" type="search" placeholder="Search" aria-label="Search">
					    </form>
						</div>

						<div class="icon-block">
							<ul>
								<li class="sell-product"><a href="#">Sell on Elesonic</a></li>
								@if(Auth::check())
									<li class="user-icon"><a href="{{route('lgt')}}"><img src="{{asset('custom_resource/images')}}"></a></li>
								@else
									<li class="user-icon"><a href="{{route('login')}}"><img src="{{asset('custom_resource/images/user-icon.png')}}"></a></li>
								@endif
								<li class="cagrt-icon" ><a href="#"><img src="{{asset('custom_resource/images/cart-icon.png')}}"></a></li>
							</ul>
						</div>
					</div>
				<!---->

			</div>
		</nav>
	</header>

<!-- header end -->

@yield('content')

<!-- footer-block start-->
<div class="footer-block">
	<div class="container">

		<div class="footer-bd">
			<div class="row">

			@php
				$categories = App\Model\Category::where('parent_id', 0)->where('status','A')
				->get();
			@endphp
				<!-- item -->
					<div class="col-lg-3">
						<div class="item quick-links-block">
							<h4>CATEGORIES</h4>
							<ul>
								@foreach($categories as $row)
								<li><a href="{{route('product-list')}}/{{ $row->slug }}/{{base64_encode($row->id)}}">{{ $row->name }}</a></li>
								@endforeach
								<!-- <li><a href="#">Lab Diagnostics & Instruments</a></li>
								<li><a href="#">Medical Device & Equipment</a></li>
								<li><a href="#">Dental</a></li>
								<li><a href="#">Medical Implants</a></li>
								<li><a href="#">Surgical Instruments</a></li>
								<li><a href="#">Hospital Establishment</a></li> -->
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
								<li><a href="{{route('contact_us')}}">Contact Us</a></li>
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

<!-- js-link -->

<script type="text/javascript" src="{{asset('custom_resource/js/jquery-3.6.0.min.js')}}"></script>
<script type="text/javascript" src="{{asset('custom_resource/js/bootstrap.min.js')}}"></script>
<!-- owl-js -->
<script type="text/javascript" src="{{asset('custom_resource/js/owl.carousel.min.js')}}"></script>
<!-- owl-js -->
<!-- zoomer -->

<script  type="text/javascript" src="{{asset('custom_resource/js/zoomer/script.js')}}"></script>
<script type="text/javascript" src="{{asset('custom_resource/js/zoomer/xzoom.min.js')}}"></script>
<!-- zoomer -->

<script type="text/javascript" src="{{asset('custom_resource/js/custome.js')}}"></script>

@yield('script')
@stack('script')

</body>
</html>















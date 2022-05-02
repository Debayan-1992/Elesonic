<!DOCTYPE html>
<html>

<head>
	<title>Elesonic Canada</title>
	  <meta charset="utf-8">
	  <meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="shortcut icon" href="{{asset('custom_resource/images/favicon.ico')}}" type="image/x-icon">
	<link rel="icon" href="{{asset('custom_resource/images/favicon.ico')}}" type="image/x-icon">

	<!-- font -->
	<link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&display=swap" rel="stylesheet">

	<!-- font-family: 'Abril Fatface', cursive; -->

	<!-- font -->


	 <link rel="stylesheet" type="text/css" href="{{asset('custom_resource/css/bootstrap.min.css')}}">


	<!-- owl-slider -->
	<link rel="stylesheet" type="text/css" href="{{asset('custom_resource/css/owl.carousel.min.css')}}">
	<!-- owl-slider -->

	<!-- font-awesome -->
	<link rel="stylesheet" href="{{asset('custom_resource/css/font-awesome.min.css')}}">
	<!-- font-awesome -->

	<link rel="stylesheet" type="text/css" href="{{asset('custom_resource/css/style.css')}}">

@yield('header')
	 <!-- countru-code -->

</head>
<body>

<!-- header start -->

	<header class="header-block">
		<nav class="navbar navbar-expand-lg ">
			<div class="container-fluid">

				<div class="logo-block">
				  <a class="navbar-brand" href="#"><img src="{{asset('custom_resource/images/logo.png')}}"></a>
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
								<li class="user-icon"><a href="{{route('welcome')}}"><img src="{{asset('custom_resource/images/user-icon.png')}}"></a></li>
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



<!-- js-link -->

<script type="text/javascript" src="{{asset('custom_resource/js/jquery-3.6.0.min.js')}}"></script>
<script type="text/javascript" src="{{asset('custom_resource/js/bootstrap.min.js')}}"></script>

<!-- owl-js -->
<script src='{{asset('custom_resource/js/owl.carousel.min.js')}}'></script>
<!-- owl-js -->

<script type="text/javascript" src="{{asset('custom_resource/js/custome.js')}}"></script>

@yield('script')


</body>
</html>















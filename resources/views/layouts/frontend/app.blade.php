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

	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

	<link rel="stylesheet" type="text/css" href="{{asset('custom_resource/css/ul-css.css')}}">

	<link rel="stylesheet" href="{{config('app.url')}}/inhouse/bower_components/select2/dist/css/select2.min.css">
	
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
					    <a class="nav-link" href="{{route('departments')}}">
					      Department
					    </a>
					    <!-- <div class="dropdown-menu" aria-labelledby="navbarDropdown">
					      <a class="dropdown-item" href="#">Action</a>
					      <a class="dropdown-item" href="#">Another action</a>
					    </div> -->
					  </li>

					  <li class="nav-item services dropdown">
					    <a class="nav-link " href="{{route('services')}}" >
					      Services
					    </a>
					    <!-- <div class="dropdown-menu" aria-labelledby="navbarDropdown">
					      <a class="dropdown-item" href="#">Action</a>
					      <a class="dropdown-item" href="#">Another action</a>
					    </div> -->
					  </li>
					</ul>
				</div>

				<!---->
					<div class="right-block">

						<div class="search-block" id="pro_search">
					  	<form action="{{route('search-product')}}" method="get">
					      <input class="form-control" type="search" onkeypress="get_prod('search_name')"  id="search_name" name="search" placeholder="Search Products....." placeholder="Search" aria-label="Search">
					    </form>
						</div>
						
						@php 
							if(Auth::check() == false){
								$user_id= "";
								
							}else{
								$user_id= auth()->user()->id;
							}
							if($user_id == ""){
								$cart_sess_id= \Session::get('cart_session_id');
								if($cart_sess_id != ""){
									$mycartsItem=  App\Model\Cart_item::where('cart_session_id',$cart_sess_id)->get();
									$totalQty = 0;
									foreach($mycartsItem as $row){
										$totalQty = $totalQty+$row->cart_item_qty;
									}
								}else{
									$totalQty = 0;
								}
							}else{
								$mycartsItem=  App\Model\Cart_item::where('user_id',$user_id)->get();
								$totalQty1 = 0;
								foreach($mycartsItem as $row){
									$totalQty1 = $totalQty1+$row->cart_item_qty;
								}
							}
						@endphp
						<div class="icon-block">
							<ul>
								<li class="sell-product"><a href="#">Sell on Elesonic</a></li>
								@if(Auth::check())
									<li class="user-icon"><a href="{{route('customer.customer_dashboard')}}"><i class="fa fa-user" aria-hidden="true"></i></a></li>
								@else
									<li class="user-icon"><a href="{{route('login')}}"><img src="{{asset('custom_resource/images/user-icon.png')}}"></a></li>
								@endif
								@if($user_id != "")
								<li class="cagrt-icon" ><a href="{{route('customer.carts')}}"><img src="{{asset('custom_resource/images/cart-icon.png')}}"><span id="myCrtItem">{{$totalQty1}}</span></a></li>
								@else
									<li class="cagrt-icon"><a href="{{route('login')}}"><img src="{{asset('custom_resource/images/cart-icon.png')}}"><span id="myCrtItem">{{$totalQty}}</span></a></li>
								@endif
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
				$services = App\Model\Service::where('status','A')
                ->get();
				$cms = App\Model\CmsContent::where('page_name','!=',' ')
                ->get();

				$city = App\Model\City::all();
			@endphp
				<!-- item -->
					<div class="col-lg-3">
						<div class="item quick-links-block">
							<h4>CATEGORIES</h4>
							<ul>
								@foreach($categories as $row)
								<li><a href="{{route('product-list')}}/{{ $row->slug }}">{{ $row->name }}</a></li>
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
						@foreach($services as $row)
							<li><a style="cursor:pointer" onclick="serviceBokkingModal('{{ $row->id }}','{{ $row->name }}')" >{{ $row->name }}</a></li>
						@endforeach
							<!-- <li><a href="#">Annual Maintenance Contract</a></li>
							<li><a href="#">Ultrasound Machine Service</a></li>
							<li><a href="#">Oxygen Concentrator Service</a></li> -->
							
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
								<li><a href="#">Write for Us</a></li>
							</ul>
						</div>
					</div>
				<!-- item -->

			

				<!-- item -->
				<div class="col-lg-4 ">
					<div class="item submit-mail">
						<h4>Subscribe for our newsletter</h4>
						@if(session()->has('subsmessage'))
							<div class="alert alert-success">
								{{ session()->get('subsmessage') }}
							</div>
						@endif

						<form  action="{{route('subscribeEmail')}}" class="formbd-sec"  method="post">
							@csrf
							<input type="email" required="" placeholder="Email address" id="subscribermail" name="subscribermail">
							<div class="submit-btn">
								<input type="submit">
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
						@foreach($cms as $row)
						<li><a href="{{route('content-details')}}/{{ $row->slug }}">{{ $row->page_name }}</a></li>
						@endforeach
						<li><a href="{{route('faq')}}">FAQs</a></li>
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
<div class="modal fade booknow-popup " id="staticBackdrop" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>

      </div>

      <div class="popup-body">

        <div class="header">
        	<h3>Book <span id="serviceName"></span></h3>
        	<p>Please enter your details and we will reach out to you as<br> soon as we can.</p>
        </div>

        <div class="form-bd">
			
        	<form id="serviceBook" action="{{route('servicebook')}}" method="post">
				@csrf
				<input type="hidden" value="" name="serviceId" id="serviceId">
        		<!-- item -->
	        		<!-- <div class="item">
	        			<label>Service category*</label>
	        			<select>
	        				<option>Oxygen Concentrator</option>
	        				<option>Text 1</option>
	        				<option>Text 2</option>
	        				<option>Text 3</option>
	        				<option>Text 4</option>
	        			</select>
	        		</div> -->
        		<!-- item -->

        		<!-- item -->
	        		<div class="item">
	        			<label>Your Name*</label>
	        			<input type="text" placeholder="Enter your name" id="name" name="name">
	        		</div>
        		<!-- item -->

				<!-- item -->
					<div class="item">
						<label>Mobile Number*</label>
						<input type="text" onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxlength="10" minlength="10" name="mobile" id="mobile">
					</div>
				<!-- item -->

        		<!-- item -->
	        		<div class="item">
	        			<label>Email*</label>
	        			<input type="email" id="email" name="email">
	        		</div>
        		<!-- item -->


				<!-- item -->
				<div class="item">
				<label>City</label>
				<select name="city" id="city" class="form-control select2">
					<option value="">Select City</option>
					@foreach ($city as $item)
						<option value="{{$item->name}}">{{$item->name}}</option>
					@endforeach
				</select>
				</div>
        		<!-- item -->


				<!-- item -->
					<div class="item">
						<label>Enter additional information</label>
						<textarea name="information"></textarea>
					</div>
				<!-- item -->

				<!-- item -->
					<div class="item">
						<input type="button" onclick="bookService()" value="Request Quote">
					</div>
				<!-- item -->




        	</form>
        </div>

      </div>
      
    </div>
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

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script src="{{config('app.url')}}/inhouse/bower_components/select2/dist/js/select2.full.min.js"></script>
<script src="//unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
	function subscribe(){
		var subscribermail = $("#subscribermail").val();
		if(subscribermail == ""){
			swal('Email required.');
			return false;
		}else if(!IsEmail(subscribermail)){
			swal('Invalid email.');
			return false;
		}else{
			$("#subscribermailform").submit();
		}
	}
	function IsEmail(email) {
        var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if(!regex.test(email)) {
           return false;
        }else{
           return true;
        }
      }
	$(document).ready(function() {
     $('#city').select2();
	 $('#state').select2();
    });
	function serviceBokkingModal(id,name){
		
		$("#serviceName").text(name);
		$("#serviceId").val(id);
		$("#staticBackdrop").modal('show');
	}
	function bookService(){
		if($("#name").val() == ""){
			swal('Name required.');
			return false;
		}else if($("#mobile").val() == ""){
			swal('Phone required.');
			return false;
		}else if($("#email").val() == ""){
			swal('Email required.');
			return false;
		}else if(!IsEmail($("#email").val())){
			swal('Invalid email.');
			return false;
		}else if($("#city").val() == ""){
			swal('City required.');
			return false;
		}else{
			$("#serviceBook").submit();
			$('#serviceBook')[0].reset();
			$("#staticBackdrop").modal('hide');
		}
		
	}
</script>

<script type="text/javascript">
function get_prod(text_id){
	var textLength = $("#"+text_id).val().length;
	if(textLength > 1){
	var cat_id = 0;
	$( "#"+text_id ).autocomplete({
			source: function(request, response) {
			$.ajax({
			url: "{{ route('get-search-data') }}",
			data:{'_token':'{{csrf_token()}}','val':$("#"+text_id).val(),'cat_id':cat_id},
			dataType: "json",
			type: "POST",
				success: function(data){
					
				var arr_data=data.pro;
				$("#pro_search").val(arr_data);
				response(data);
				}
				});
			},
		});
	}
}
</script>  

@yield('script')
@stack('script')

</body>
</html>















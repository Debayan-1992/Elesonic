@extends('layouts.frontend.app')
@section('content')
@section('pageheader', 'Services')
<link rel="stylesheet" href="{{config('app.url')}}/inhouse/bower_components/select2/dist/css/select2.min.css">
	<div class="innr-banner">

		<div class="figure">
			<img src="{{asset('custom_resource/images/innr-banner1.jpg')}}">
		</div>

		<div class="text-sec">
			<div class="container">
				<h3>Instrument Service Repair</h3>
				<p>Fast, on-demand repair options</p>
			</div>
		</div>

	</div>

<!-- innr-banner -->

<!-- product-service -->

	<div class="product-service">
		<div class="container">
		@if(session()->has('message'))
			<div class="alert alert-success">
				{{ session()->get('message') }}
			</div>
		@endif
			<h3>Services</h3>
			<div class="bd">
				<div class="row">

					<!-- item -->
					@if(!empty($services))
						@foreach($services as $row)
							<div class="col-lg-4 col-md-6 col-sm-12">
								<div class="item">

									<div class="text">
										<h5>{{$row->name}}</h5>
										<a href="#" data-toggle="modal" onclick="serviceBokkingModal('{{ $row->id }}','{{ $row->name }}')" >Book Now</a>
									</div>

									<div class="figure">
										<img src="{{config('app.url')}}/uploads/services/{{ $row->image }}">
									</div>

								</div>
							</div>
						@endforeach
					@endif
				</div>
			</div>
		</div>
	</div>
	<!-- Modal -->

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
					@foreach ($cities as $item)
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
@endsection
<!-- medical-device -->
@push('script')
<script src="{{config('app.url')}}/inhouse/bower_components/select2/dist/js/select2.full.min.js"></script>
<script src="//unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
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
@endpush
<!-- product-service -->






<!-- popup-block -->




















@extends('layouts.frontend.app')
@section('content')
@section('pageheader', 'Services')

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
@endsection
<!-- medical-device -->
@push('script')

@endpush
<!-- product-service -->






<!-- popup-block -->




















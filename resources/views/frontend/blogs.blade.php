@extends('layouts.frontend.app')
@section('content')
@section('pageheader', 'Blogs')

	<div class="innr-banner">

		<div class="figure">
			<img src="{{asset('custom_resource/images/innr-banner1.jpg')}}">
		</div>

		<div class="text-sec">
			<div class="container">
				
			</div>
		</div>

	</div>

<!-- innr-banner -->

<!-- product-service -->

	<div class="product-service">
		<div class="container">
			<h3>Blogs</h3>
			<div class="bd">
				<div class="row">

					<!-- item -->
					@if(!empty($blogs))
						@foreach($blogs as $row)
							<div class="col-lg-4 col-md-6 col-sm-12">
								<div class="item">

									<div class="text">
										<h5>{{$row->title}}</h5>
									</div>
									<div class="figure">
										<img src="{{config('app.url')}}/uploads/blogs/{{ $row->image }}">
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




















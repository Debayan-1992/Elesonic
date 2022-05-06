<div class="row">
<input type="hidden" value="{{ count($products) }}" id="filterCountProduct">
	<!-- item -->
	@if(!empty($products))
		@foreach($products as $row)
		<div class="col-lg-3 col-md-6 col-sm-12 comn-px0">
			<div class="item">
				<a href="{{route('product-details')}}/{{ $row->slug }}/{{base64_encode($row->id)}}">
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

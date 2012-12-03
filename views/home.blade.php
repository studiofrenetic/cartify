@layout('cartify::template')

@section('content')
	@foreach ( $products as $product )
	<div class="row">
		<div class="span12 well">
			<form class="form-horizontal" method="post" action="{{ URL::to('cartify/cart/add') }}">
				<span class="span3 thumbnail"><img src="{{ URL::to_asset('img/products/' . $product['image']) }}" /></span>
				<span class="span7">
					<input type="hidden" name="item_id" id="item_id" value="{{ $product['id']}}" />
					<div class="page-header">
						<h4>{{ $product['name']}}</h4>
					</div>
					@if ( isset( $product['options'] ) )
						<div class="control-group">
						@foreach ( $product['options'] as $option => $options )
							<label class="control-label" for="options_{{ $option }}">{{ $option }}</label>
							<div class="controls">{{ Form::select('options[' . $option . ']', $options, null, array('id' => 'options_' . $option)) }}</div>
						@endforeach
						</div>
					@endif
					<div class="control-group">
						<label class="control-label" for="qty">Qty.</label>
						<div class="controls"><input type="text" class="span1" name="qty" id="qty" value="1" /></div>
					</div>
					<div class="control-group">
						<label class="control-label">Price</label>
						<div class="controls">{{ $product['price'] }}</div>
					</div>
					<div class="control-group">
						<div class="controls">
							<input class="btn btn-inverse" type="submit" value="Add to Cart" />
							<a href="{{ URL::to('cartify/wishlist/add/' . $product['id']) }}" class="btn btn-warning" />Add to Wishlist</a>
						</div>
					</div>
				</span>
			</form>
		</div>
	</div>
	@endforeach
@endsection

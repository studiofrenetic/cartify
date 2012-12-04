@layout('cartify::template')

@section('content')
<h3>Products</h3>
@foreach ($products as $product)
<div class="row">
	<div class="span12 well">
		<form class="form-horizontal" method="post" action="">
			<span class="span3 thumbnail"><img src="{{ URL::to_asset('bundles/cartify/img/products/' . $product['image']) }}" /></span>
			<span class="span7">
				<input type="hidden" name="item_id" id="item_id" value="{{ $product['id']}}" />
				<div class="page-header">
					<h4>{{ $product['name']}}</h4>
				</div>
				@if (isset($product['options']))
					<div class="control-group">
					@foreach ($product['options'] as $option => $options)
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
					<div class="controls">{{ format_number($product['price']) }}</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<button type="submit" name="action" value="add_to_cart" class="btn btn-inverse">Add to Cart</button>
						<button type="submit" name="action" value="add_to_wishlist" class="btn btn-warning">Add to Wishlist</button>
					</div>
				</div>
			</span>
		</form>
	</div>
</div>
@endforeach
@endsection

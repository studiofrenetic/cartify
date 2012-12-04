@layout('cartify::template')

@section('content')
					<form class="form-horizontal" method="post" action="">
	<h3>Wishlist Contents</h3>
	<table class="table table-hover table-striped table-bordered">
		<thead>
			<tr>
				<th width="6%"></th>
				<th width="60%">Name</th>
				<th width="12%">Price</th>
			</tr>
		</thead>
		<tbody>
			@forelse (Cartify::wishlist()->contents() as $item)
			<tr>
				<td xrowspan="2">
					<span class="span1 thumbnail"><img src="{{ URL::to_asset('img/products/' . $item['image']) }}" /></span>
				</td>
				<td>
					{{ $item['name'] }}

					<span class="pull-right">
						<a href="{{ URL::to('cartify/cart/add/' . $item['id']) }}" class="btn btn-mini btn-info" rel="tooltip" title="Add to the Shopping Cart"><i class="icon icon-white icon-shopping-cart"></i></a>
						<a href="{{ URL::to('cartify/wishlist/remove/' . $item['rowid']) }}" rel="tooltip" title="Remove the product." class="btn btn-mini btn-danger"><i class="icon icon-white icon-remove"></i></a>
					</span>
				</td>
				<td xrowspan="2">{{ format_number($item['price']) }}</td>
			</tr>
			<?php
			/*
			<tr>
				<td>
					<!-- Get the product options, you should get both cart contents and product related options on your controller -->
					<?php $product_options = Cartify\Models\Products::get_options($item['id']); ?>

					<!-- Get this item options -->
					<?php $item_options = Cartify::wishlist()->item_options($item['rowid']); ?>

					<div class="control-group">
						<label class="control-label" for="options_qty">Qty</label>
						<div class="controls">
							<input type="text" name="qty" value="1" />
						</div>

						<!-- Loop through the product options -->
						@foreach ($product_options as $option => $values)
							<label class="control-label" for="options_{{ $option }}">{{ $option }}</label>
							<div class="controls">
								{{ Form::select('options[' . $option . ']', $product_options[ $option ], array_get($item_options, $option), array('id' => 'options_' . $option))}}
							</div>
						@endforeach
					</div>
				</td>
			</tr>
			*/?>
			@empty
			<tr>
				<td colspan="3">Your wishlist is empty.</td>
			</tr>
			@endforelse
		</tbody>
	</table>

	@if (Cartify::wishlist()->total())
	<button type="submit" id="empty" name="empty" value="1" class="btn btn-warning">Empty your Wishlist</button>
	@endif
</form>
@endsection

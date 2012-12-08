@layout('cartify::template')

@section('content')
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
			<!-- Get the product options, you should get product related options on your controller ! -->
			<?php $product_options = Cartify\Models\Products::get_options($item['id']); ?>

			<tr>
				<td>
					<span class="span1 thumbnail"><img src="{{ URL::to_asset('bundles/cartify/img/products/' . $item['image']) }}" /></span>
				</td>
				<td>
					<strong>{{ $item['name'] }}</strong>

					<span class="pull-right">
						<a href="{{ URL::to('cartify/wishlist/add_to_cart/' . $item['rowid']) }}" xdata-target="#AddToCartModel" data-rowid="{{ $item['rowid'] }}" data-toggle="modal" class="btn btn-mini btn-info add_to_cart" rel="tooltip" title="Add to the Shopping Cart"><i class="icon icon-white icon-shopping-cart"></i></a>
						<a href="{{ URL::to('cartify/wishlist/remove/' . $item['rowid']) }}" rel="tooltip" title="Remove the product." class="btn btn-mini btn-danger"><i class="icon icon-white icon-remove"></i></a>
					</span>

					<!-- Check if this cart item has options. -->
					@if (Cartify::wishlist()->has_options($item['rowid']))
					<small>
						<ul class="unstyled">
						@foreach ($item['options'] as $option_name => $option_value)
							<li>- <small>{{ $option_name }}: {{ array_get($product_options, $option_name . '.' . $option_value) }}</small></li>
						@endforeach
						</ul>
					</small>
					@endif
				</td>
				<td>{{ format_number($item['price']) }}</td>
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
							<input type="text" name="items[{{ $item['rowid'] }}][qty]" value="1" />
						</div>

						<!-- Loop through the product options -->
						@foreach ($product_options as $option => $values)
							<label class="control-label" for="options_{{ $option }}">{{ $option }}</label>
							<div class="controls">
								{{ Form::select('items[' . $item['rowid'] . '][options][' . $option . ']', $product_options[ $option ], array_get($item_options, $option), array('id' => 'options_' . $option))}}
							</div>
						@endforeach
					</div>
				</td>
			</tr>
*/
?>
			@empty
			<tr>
				<td colspan="3">Your wishlist is empty.</td>
			</tr>
			@endforelse
		</tbody>
	</table>

	@if (Cartify::wishlist()->total())
	<form method="post" action="{{ URL::to('cartify/wishlist') }}" class="form-horizontal">
		<button type="submit" id="empty" name="empty" value="1" class="btn btn-warning">Empty your Wishlist</button>
	</form>
	@endif






<div id="AddToCartModel" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form class="modal-form" id="modal-form" action="/tagging" data-remote="true" method="post">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">×</a>
            <h3>Add item to the Cart</h3>
        </div>
        <div class="modal-body">
            <input name="something" value="Some value" />
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal">Cancel</a>
            <input type="submit" value="Save" class="btn btn-primary" />
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
//
//
$('.add_to_cart').click(function(){
	// Get the item rowid.
	//
	var rowid = $(this).data('rowid');

	// Make the request.
	//
	/*
	$.ajax({
		//
		//
		type : 'POST',
		url  : '',
		data : 'rowid=' + rowid,

		//
		//
		success : function(data)
		{
			//
			//
			#

			// Show the modal window.
			//
			$('#AddToCartModel').show();
		}
	})
	*/

	$('#AddToCartModel').show();
});
</script>
@endsection

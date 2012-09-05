@layout('lara-cart::template')

@section('content')
<form method="post" action="">
	<h3>Cart Contents</h3>
	<table class="table table-hover table-striped table-bordered">
		<thead>
			<tr>
				<th width="15%"></th>
				<th width="40%">Name</th>
				<th width="8%">Qty.</th>
				<th width="12%">Price</th>
				<th width="12%">Sub-Total</th>
			</tr>
		</thead>
		<tbody>
			@forelse ( Cart::contents() as $item )
			<tr>
				<td>
					<span class="span2 thumbnail"><img src="{{ URL::to_asset('img/products/' . $item['image']) }}" /></span>
				</td>
				<td>
					{{ $item['name'] }}
					<span class="pull-right">
						<a href="{{ URL::to('cart/remove/' . $item['rowid']) }}" rel="tooltip" title="Remove the product" class="btn btn-mini btn-danger"><i class="icon icon-white icon-remove"></i></a>
					</span>
					@if ( Cart::has_options($item['rowid']) )
						<p>
						@foreach ( Cart::product_options($item['rowid']) as $option_name => $option_value)
						<strong>{{ $option_name }}:</strong> {{ $option_value }}<br />
						@endforeach
						</p>
					@endif
				</td>
				<td><input type="text" class="span1" value="{{ $item['qty'] }}" name="items[{{ $item['rowid'] }}]" /></td>
				<td>{{ Cart::format_number( $item['price'] ) }}</td>
				<td>{{ Cart::format_number( $item['subtotal'] ) }}</td>
			</tr>
			@empty
			<tr>
				<td colspan="6">The cart is empty.</td>
			</tr>
			@endforelse
		</tbody>
	</table>

	@if ( Cart::total() )
	<table class="table table-bordered">
		<tbody>
			<tr>
				<td width="63%" colspan="3"></td>
				<td width="12%">Items</td>
				<td width="12%">{{ Cart::total_items() }}</td>
			</tr>
			<tr>
				<td width="63%" colspan="3"></td>
				<td width="12%">Total</td>
				<td width="12%">{{ Cart::format_number( Cart::total() ) }}</td>
			</tr>
		</tbody>
	</table>

	<button type="submit" id="update" name="update" value="1" class="btn btn-success">Update</button>
	<button type="submit" id="empty" name="empty" value="1" class="btn btn-warning">Empty the Cart</button>
	@endif
</form>
@endsection
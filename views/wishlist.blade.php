@layout('cartify::template')

@section('content')
<form method="post" action="">
	<h3>Wishlist Contents</h3>
	<table class="table table-hover table-striped table-bordered">
		<thead>
			<tr>
				<th width="15%"></th>
				<th width="60%">Name</th>
				<th width="12%">Price</th>
			</tr>
		</thead>
		<tbody>
			@forelse ( Cartify::wishlist()->contents() as $item )
			<tr>
				<td>
					<span class="span2 thumbnail"><img src="{{ URL::to_asset('img/products/' . $item['image']) }}" /></span>
				</td>
				<td>
					{{ $item['name'] }}
					<span class="pull-right">
						<a href="{{ URL::to('cartify/wishlist/remove/' . $item['rowid']) }}" rel="tooltip" title="Remove the product" class="btn btn-mini btn-danger"><i class="icon icon-white icon-remove"></i></a>
					</span>
				</td>
				<td>{{ Cartify::wishlist()->format_number( $item['price'] ) }}</td>
			</tr>
			@empty
			<tr>
				<td colspan="3">Your wishlist is empty.</td>
			</tr>
			@endforelse
		</tbody>
	</table>

	@if ( Cartify::wishlist()->total() )
	<button type="submit" id="empty" name="empty" value="1" class="btn btn-warning">Empty your Wishlist</button>
	@endif
</form>
@endsection

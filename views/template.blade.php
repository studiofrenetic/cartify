<!DOCTYPE html>
<html>
	<head>
    	<title>Lara Cart</title>

    	<link href="{{ URL::to_asset('css/bootstrap.css') }}" rel="stylesheet">
    	<style type="text/css">body { padding-top: 60px; }</style>
  	</head>
  	<body>
    	<div class="navbar navbar-inverse navbar-fixed-top">
      		<div class="navbar-inner">
        		<div class="container">
		          	<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
			            <span class="icon-bar"></span>
			            <span class="icon-bar"></span>
			            <span class="icon-bar"></span>
		          	</a>
          			<a class="brand" href="{{ URL::to('cart') }}">Lara Cart</a>
		          	<div class="nav-collapse collapse">
			            <ul class="nav">
			              	<li{{ ( URI::segment(2) == '' ? ' class="active"': '' ) }}><a href="{{ URL::to('cart') }}">Home</a></li>
			              	<li{{ ( URI::segment(2) == 'products' ? ' class="active"': '' ) }}><a href="{{ URL::to('cart/products') }}">Products</a></li>
	            		</ul>
	          		</div>
	        	</div>
	      	</div>
	    </div>

    	<div class="container">
	    	@if ( $success = Session::get('success') )
			<div class="alert alert-success">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<strong>Success!</strong> {{ $success }}
			</div>
			@endif
	    	@if ( $error = Session::get('error') )
			<div class="alert alert-error">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<strong>Error!</strong> {{ $error }}
			</div>
			@endif
	    	@if ( $warning = Session::get('warning') )
			<div class="alert alert-warning">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<strong>Warning!</strong> {{ $warning }}
			</div>
			@endif

    		@yield('content')
		</div>
    	<script src="http://code.jquery.com/jquery-latest.js"></script>
    	<script src="{{ URL::to_asset('js/bootstrap.js') }}"></script>
  	</body>
</html>
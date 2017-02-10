<html lang="en">
@include('layouts.header')


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>

<head>

	<title>Import -CSV</title>
	 

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" >

</head>

<body>



		<div class="container-fluid">

			<div class="navbar-header">
               
	<!--{  Auth::user()->email}
	{  Auth::id()}-->
				<a class="navbar-brand" href="#">Import CSV</a>
                @if(Session::has('msg'))
                {{ Session::get('msg')}}
                @endif
			</div>

		</div>

	

	<div class="container">

		<!--<a href="{{ URL::to('downloadExcel/xls') }}"><button class="btn btn-success">Download Excel xls</button></a>

		<a href="{{ URL::to('downloadExcel/xlsx') }}"><button class="btn btn-success">Download Excel xlsx</button></a>

		<a href="{{ URL::to('downloadExcel/csv') }}"><button class="btn btn-success">Download CSV</button></a>-->

		<form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px;" class="form-horizontal" action="{{url('/')}}/importExcel" method="post" enctype="multipart/form-data">

			<input type="file" name="import_file" id="import_file" />

			<input type="hidden" name="_token" value="{{csrf_token()}}">

			<input type="submit" id="import" value="Import" class="btn btn-primary"></input>
		</form>

	</div>

</body>







</html>
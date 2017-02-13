<!DOCTYPE html>
@include('layouts.header')
<html>
<head>
	<title>All Domain</title>
</head>
<style>
table, th, td {
    border: 1px solid black;
}
</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">

<link rel="stylesheet" type="text/css" href="{{url('/')}}/resources/assets/css/bootstrap.css">

    
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" >

	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  	<link rel="stylesheet" href="/resources/demos/style.css">
  	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
  	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

  	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="{{url('/')}}/resources/assets/css/bootstrap.css">
	
	<script type="text/javascript" src="{{url('/')}}/resources/assets/js/jquery-1.12.0.js"></script>
	<script type="text/javascript" src="{{url('/')}}/resources/assets/js/jquery.dataTables.js"></script>
      <script type="text/javascript" src="{{url('/')}}/theme/js/bootstrap.js"></script>
		
		
<body>

	<div class="col-md-10" style="padding-left:85px"> 
		
	

	<p>All domain names for email address <b>{{$email}}</b></p>

	<u>Total count : {{count($alldomain->get())}}</u>

	<div>
		<table class="table table-hover table-bordered domainDAta">
		
			<tr>
				<tr>
					<th>SL no</th>
				    <th>Domain Name</th>
				    <th>Registrant Name</th>
				    <th>Registrant Email</th>
				    <th>Registrant Phone</th>
				    <th>Date</th>
				    <th>Registrant Company</th>
			  	</tr>

			  	<?php $x = 1; ?>

		  		@foreach($alldomain->get() as $domain)

		  		<tr>
		  			<td>{{$x++}}</td>
			  		<td>
						{{$domain->domain_name}}
					</td>
					<td>
						
						{{$domain->leads->registrant_name}}
					</td>
					<td>
						
						{{$domain->leads->registrant_email}}
					</td>
					<td>
						
						{{$domain->leads->registrant_phone}}
					</td>
					<td>
						
						{{$domain->domains_info->domains_create_date}}
						
					</td>
					<td>

						@if($domain->leads->registrant_company == null)
							<img src="{{url('/')}}/public/images/userimg.png" style="width:30px; height:30px">
						@else
							{{$domain->leads->registrant_company}}
						@endif

					</td>
				</tr>

				@endforeach
				  		
			  	
			</tr>
		</table>
	</div>

	</div>
</body>

<script type="text/javascript" src="{{url('/')}}/resources/assets/js/jquery-1.12.0.js"></script>
<script type="text/javascript" src="{{url('/')}}/resources/assets/js/bootstrap.js"></script>
</html>
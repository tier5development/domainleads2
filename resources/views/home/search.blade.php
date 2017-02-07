<html lang="en">
@include('layouts.header')


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>

<head>

	<title>Import -CSV</title>
	 

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" >

	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  	<link rel="stylesheet" href="/resources/demos/style.css">
  	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

  	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="{{url('/')}}/resources/assets/css/bootstrap.css">
	
	<script type="text/javascript" src="{{url('/')}}/resources/assets/js/jquery-1.12.0.js"></script>
	<script type="text/javascript" src="{{url('/')}}/resources/assets/js/jquery.dataTables.js"></script>
        

</head>

<body>
	


		<div class="container-fluid">

			<div class="navbar-header">
                @if(Session::has('msg'))
                {{ Session::get('msg')}}
                @endif
			</div>

			<!-- form elements -->
			<div class="">

			<form method="POST" action="{{url('/')}}/postSearch">
				<table class="table table-hover table-bordered domainDAta">
					
						<tr>
							<td>
								<label>Domain Name : </label>
								<input type="text" name="domain_name" id="domain_name">
							</td>
							<td>
								<label>Registrant Country : </label>
								<input type="text" name="registrant_country" id="registrant_country">
							</td>
							<td>
								<label>Registrant State : </label>
								<input type="text" name="registrant_state" id="registrant_state">
							</td>
							<td>
								<label>Domains Create Date</label>
								<input type="date" name="registered_date" id="registered_date">
							</td>
						</tr>
						<tr>
							<td class="col-md-8">
								
								<label>.com</label>
								<input type="checkbox" name=".com" value=".com">
								<label>.net</label>
								<input type="checkbox" name=".net" value=".net">
								<label>.org</label>
								<input type="checkbox" name=".org" value=".org">
								<label>.io</label>
								<input type="checkbox" name=".io" value=".io">
								
							</td>

							<td class="col-md-4">
								<label>cell number</label>
								<input type="checkbox" name="cell_number" value="cell number">
								<label>landline number</label>
								<input type="checkbox" name="landline_number" value="landline number">
							</td>
						</tr>

					
				</table>
				<input type="hidden" name="_token" value="{{csrf_token()}}">
			<input class="btn btn-info pull-right" type="submit" name="Submit" value="Submit">

			</form>
				
			</div>


			<br><br>
 	 		
			<div>
			<span class="pull-left">Total Records : {{count($record)}}</span>
				<table class="table table-hover table-bordered domainDAta">
					<tr>
						<th>Check box</th>
						<th>Domain Name</th>
						<th>Registrant Name</th>
						<th>Registrant Email</th>
						<th>Registrant Phone</th>
						<th>Registered Date</th>
						<th>Registrant Company</th>
					</tr>

					@foreach($record as $key=>$each)
					<tr>
						<th><input type="checkbox" id="ch_{{$key}}" name="ch_{{$key}}"></th>
						<th>

							{{$each->domain_name}}
							<br>
							<small>Unlocked Num : {{$each->unlocked_num}}</small>
							<br>
							<small>Total Domains: {{$arr[$each->unique_hash]}} </small>
						</th>
						<th>{{$each->leads->registrant_name}}</th>
						<th>{{$each->leads->registrant_email}}</th>
						<th>{{$each->leads->registrant_phone}}</th>
						<th>{{$each->created_at}}</th>
						<th>{{$each->leads->registrant_company}}</th>
					</tr>
					@endforeach
				</table>
				
			</div>
			
			 



		</div>
</body>
	<script>
	  	$( function() {
	    	$( "#registered_date" ).datepicker();
	  	});
  	</script>

	<script type="text/javascript">
	$(document).ready(function(){
		
		$(window).on('hashchange',function(){
			page = window.location.hash.replace('#','');
			getProducts(page);
		});

	});
	</script>
</html>
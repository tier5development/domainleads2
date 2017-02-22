<html lang="en">
@include('layouts.header')
<head>
<style>
table, th, td {
    border: 1px solid black;
}
</style>
	<title>domainleads | My Links</title>

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

	<body>
		<section>
		
			<div class="col-md-10" style="padding-left:85px">

				<h4>
				<small><u>MY  LEADS</u></small> 

				<small class="pull-right">TOTAL : {{sizeof($myleads->get())}}</small>
				</h4>
				
		    	@if(isset($myleads) && $myleads != null)         
				
				<table class="table table-hover table-bordered domainDAta">


					<tr>
						<th>Domain Name</th>
						<th>Registrant name</th>
						<th>Registrant email</th>
						<th>Registrant phone</th>
						<th>Domains Create Date</th>
					</tr>

					@foreach($myleads->get() as $key=>$each)

					<tr>
						<td>
							{{ $each->each_domain->first()->domain_name }}

							<small> Unlocked Num : <span id="unlocked_num_{{$key}}">{{$each->unlocked_num}}</span></small>
							<br>
							<small > Total Domain Count : <a href="{{url('/')}}/lead/{{encrypt($each->registrant_email)}}">{{$leadArr[$each->registrant_email]}}</a></small>

				        </td>

				        <td>
				        	{{ $each->registrant_fname}} {{$each->registrant_lname}}
				        </td>
				        
				        <td>
					        {{ $each->registrant_email}}
				        </td>
			        
			        	<td>
					        {{ $each->registrant_phone}}				        
				        </td>

				        <td>
				        	{{$each->each_domain->first()->domains_info->first()->domains_create_date}}
				        </td>
					</tr>

					@endforeach

				</table>
				
				@endif
			</div>
		</section>
	</body>
</head>
</html>
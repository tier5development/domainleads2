<!DOCTYPE html>
@include('layouts.header')
<html>
<head>
	<title>All Domain</title>



<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">


    
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" >

	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  	
  	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
  	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

  	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	
	
	
	
    <script type="text/javascript" src="{{url('/')}}/theme/js/bootstrap.js"></script>
    <link rel="stylesheet" href="{{url('/')}}/public/css/style.css">
</head>

		
		
<body>

	<div class="col-md-10" style="padding-left:85px"> 
		
	

	<p>All domain names for email address <b>{{$email}}</b></p>

	<u>Total count : {{count($alldomain->get())}}</u>

	<div style="display: none" id="spinner" class="spinner"></div>
	
    <h1 style="display: none" id="processing">Please Wait while we create your Wordpress setup.. (Don't abort or refresh this page if this operation takes a bit of time)</h1>    
    


	<div id="content_div">
		<table class="table table-hover table-bordered domainDAta">
		
			<tr>
				<tr>
					<th>SL no</th>
				    <th>Domain Name</th>
				    {{-- <th>Wordpress Site Status</th> --}}
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
            <br>

            @if(isset($domain->domains_feedback))
            <small><i style="color: brown">{{str_limit($domain->domains_feedback->first()->curl_errors->err_reason,30)}}</i></small>
            <a href="javascript:void(0)" onclick="show_info('{{$domain->domain_name}}','{{$domain->domains_feedback->first()->curl_errors->err_reason}}')">more</a>
            @endif

					</td>


					{{-- <td>


						@if(isset($domain->wordpress_env))
							
							Wordpress Environment Set
							
						@else
							<button class="btn btn-primary" id="createWordpressEnvID_{{$domain->id}}" onclick="createWordpressEnv('{{$domain->domain_name}}','{{$domain->id}}','{{$email}}')" >Create website</button>
						@endif
						

					</td> --}}

					<td>
						
						{{$domain->leads->registrant_fname}} {{$domain->leads->registrant_lname}}
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
							<img src="{{url('/')}}/images/userimg.png" style="width:30px; height:30px">
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

<!-- <script type="text/javascript" src="{{url('/')}}/resources/assets/js/jquery-1.12.0.js"></script>
<script type="text/javascript" src="{{url('/')}}/resources/assets/js/bootstrap.js"></script> -->

<script type="text/javascript">

	$(document).ready(function(){
		//$('#spinner').hide();
		//$('#processing').hide();
	});

  function show_info($d_name,$err_reason)
  {
    alert($err_reason);
  }

	function createWordpressEnv(domain_name,id,registrant_email){		
           var _token='{{csrf_token()}}';
           var user_id = '{{\Auth::user()->id}}';

          

           $.ajax({
               type:'POST',
               url:'{{url('/')}}/createWordpressForDomain',
               beforeSend: function()
				{

            		$('#content_div').addClass('hiddenDiv');
					$('#spinner').show();
					$('#processing').show();

					$('#createWordpressEnvID_'+domain_name).html('<span align="center"><img src="theme/images/loading.gif">checking...</span>');
				},
               data:'domain_name='+domain_name+'&_token='+_token+'&registrant_email='+registrant_email+'&user_id='+user_id,
               success:function(response)
               {
               		if(response.error == 'null')
               		{
               			$('#spinner').hide();
	      				$('#content_div').removeClass('hiddenDiv');
	      				$('#processing').hide();
						$('#createWordpressEnvID_'+id).text('Environment Created');
	                  	$('#createWordpressEnvID_'+id).prop("disabled",true);
               		}
               		else
               			alert(response.error);
               },

            });

	    }
</script>
</html>
<!DOCTYPE html>
@include('layouts.header')
<html>
<head>
	<title>All Domain</title>



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
<style>
table, th, td {
    border: 1px solid black;
}

.table>tbody>tr>td.clickable{
	text-align: center;
    vertical-align: middle;
    transition: 0.5s;
    cursor: pointer;
    text-transform: uppercase;
    font-size: 12px;
}	

.table>tbody>tr>td.clickable:hover{
	background: #000;
    color: #fff;
    font-size: 14px;
    
}

.table>tbody>tr>td.clicked{
	background: #ccc;
    color: #fff;
    font-size: 14px;
}


.spinner {
  width: 60px;
  height: 60px;
  margin: 60px;
  animation: rotate 1.4s infinite ease-in-out, background 1.4s infinite ease-in-out alternate;
}

@keyframes rotate {
  0% {
    transform: perspective(120px) rotateX(0deg) rotateY(0deg);
  }
  50% {
    transform: perspective(120px) rotateX(-180deg) rotateY(0deg);
  }
  100% {
    transform: perspective(120px) rotateX(-180deg) rotateY(-180deg);
  }
}
@keyframes background {
  0% {
  background-color: #27ae60;
  }
  50% {
    background-color: #9b59b6;
  }
  100% {
    background-color: #c0392b;
  }
}

.hiddenDiv {
	display: none;
}


html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }




</style>
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
				    <th>Wordpress Site Status</th>
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


						@if(isset($domain->wordpress_env))
							
							Wordpress Environment Set
							
						@else
							<button class="btn btn-primary" id="createWordpressEnvID_{{$domain->id}}" onclick="createWordpressEnv('{{$domain->domain_name}}','{{$domain->id}}','{{$email}}')" >Create website</button>
						@endif
						

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

<script type="text/javascript">

	$(document).ready(function(){
		//$('#spinner').hide();
		//$('#processing').hide();
	});

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
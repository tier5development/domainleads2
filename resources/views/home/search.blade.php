<html lang="en">
@include('layouts.header')

<style type="text/css">

.dropdown {
  position: absolute;
  top:50%;
  transform: translateY(-50%);
}

.dropdown dd,
.dropdown dt {
  margin: 0px;
  padding: 0px;
}

.dropdown ul {
  margin: -1px 0 0 0;
}

.dropdown dd {
  position: relative;
}

.dropdown a,
.dropdown a:visited {
  color: #fff;
  text-decoration: none;
  outline: none;
  font-size: 12px;
}

.dropdown dt a {
  background-color: #4F6877;
  display: block;
  padding: 8px 20px 5px 10px;
  min-height: 25px;
  line-height: 24px;
  overflow: hidden;
  border: 0;
  width: 272px;
}

.dropdown dt a span,
.multiSel span {
  cursor: pointer;
  display: inline-block;
  padding: 0 3px 2px 0;
}

.dropdown dd ul {
  background-color: #4F6877;
  border: 0;
  color: #fff;
  display: none;
  left: 0px;
  padding: 2px 15px 2px 5px;
  position: absolute;
  top: 2px;
  width: 280px;
  list-style: none;
  height: 100px;
  overflow: auto;
}

.dropdown span.value {
  display: none;
}

.dropdown dd ul li a {
  padding: 5px;
  display: block;
}

.dropdown dd ul li a:hover {
  background-color: #fff;
}



</style>


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
								<input type="date" name="domains_create_date" id="registered_date">
							</td>
						</tr>
						<tr>
							<td class="col-md-8">
								
								<dl class="dropdown"> 
  
			    <dt>
			    <a href="#">
			      <span class="hida">Select</span>    
			      <p class="multiSel"></p>  
			    </a>
			    </dt>
			  
			    <dd>
			        <div class="mutliSelect">
			            <ul>
			                <li>
			                    <input type="checkbox" value="Apple" />com</li>
			                <li>
			                    <input type="checkbox" value="Blackberry" />io</li>
			                <li>
			                    <input type="checkbox" value="HTC" />net</li>
			                <li>
			                    <input type="checkbox" value="Sony Ericson" />org</li>
			                <li>
			                    <input type="checkbox" value="Motorola" />gov</li>
			                <li>
			                    <input type="checkbox" value="Nokia" />edu</li>
			                <li>
			                    <input type="checkbox" value="Nokia" />in</li>

			            </ul>
			        </div>
			    </dd>
			  <button>Filter</button>
			</dl>
								
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
	    	$( "#registered_date" ).datepicker({ dateFormat: 'yy-mm-dd' }).val();
	  	});
  	</script>



	<script type="text/javascript">




		var options = [];

		$( '.dropdown-menu a' ).on( 'click', function( event ) {

			alert(1);
		   var $target = $( event.currentTarget ),
		       val = $target.attr( 'data-value' ),
		       $inp = $target.find( 'input' ),
		       idx;

		   if ( ( idx = options.indexOf( val ) ) > -1 ) {
		      options.splice( idx, 1 );
		      setTimeout( function() { $inp.prop( 'checked', false ) }, 0);
		   } else {
		      options.push( val );
		      setTimeout( function() { $inp.prop( 'checked', true ) }, 0);
		   }

		   $( event.target ).blur();
		      
		   console.log( options );
		   return false;
		});



	$(document).ready(function(){

		$(window).on('hashchange',function(){
			page = window.location.hash.replace('#','');
			getProducts(page);
		});

	});
	</script>

	<script type="text/javascript">

		$(".dropdown dt a").on('click', function() {
		  $(".dropdown dd ul").slideToggle('fast');
		});

		$(".dropdown dd ul li a").on('click', function() {
		  $(".dropdown dd ul").hide();
		});

		function getSelectedValue(id) {
		  return $("#" + id).find("dt a span.value").html();
		}

		$(document).bind('click', function(e) {
		  var $clicked = $(e.target);
		  if (!$clicked.parents().hasClass("dropdown")) $(".dropdown dd ul").hide();
		});

		$('.mutliSelect input[type="checkbox"]').on('click', function(){

		  var title = $(this).closest('.mutliSelect').find('input[type="checkbox"]').val(),
		    title = $(this).val() + ",";

		  if ($(this).is(':checked')) 
		  {
		    var html = '<span title="' + title + '">' + title + '</span>';
		    $('.multiSel').append(html);
		    $(".hida").hide();
		  } 
		  else 
		  {
		    $('span[title="' + title + '"]').remove();
		    var ret = $(".hida");
		    $('.dropdown dt a').append(ret);

		  }

		});
	</script>
</html>
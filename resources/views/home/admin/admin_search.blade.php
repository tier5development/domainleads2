<html lang="en">
@include('layouts.header')
<head>

	<title>Search</title>
	 

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

<style type="text/css">
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
  /*background-color: #4F6877;*/
  display: block;
  padding: 8px 20px 5px 10px;
  min-height: 25px;
  /*line-height: 24px;
  overflow: hidden;
  border: 0;
  width: 272px;*/
  border: 1px solid #ccc;
    height: 40px;
    border-radius: 5px;
    width: 100%;
    color: #666 !important;
    font-size: 14px !important;
}

.dropdown dt a span,
.multiSel span {
  cursor: pointer;
  display: inline-block;
  padding: 0 3px 2px 0;
}

.dropdown dd ul {
  background-color: #eee;
  border: 1px solid #ccc;
  color: #666;
  display: none;
  left: 0px;
  padding: 2px 15px 2px 5px;
  position: absolute;
  top: 2px;
  width: 100%;
  list-style: none;
  height: 160px;
  overflow: auto;
  font-size: 14px !important;
}

.dropdown dd ul li{
	padding: 10px;
	font-size: 16px;
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

.dropdown dd ul li input{
	margin-right: 5px;
    vertical-align: middle;
}

form{
	border: 1px solid #ccc;
    border-radius: 5px;
    padding: 20px;
}


</style>
        

</head>

<body>
	


		<div class="container">

			<div class="navbar-header">
                @if(Session::has('msg'))
                {{ Session::get('msg')}}
                @endif
			</div>

			<!-- form elements -->


			<div>
				<a href="{{url('/')}}/myLeads/{{encrypt(\Auth::user()->id)}}">My Leads</a>
			</div>


			<div class="col-md-12">

				<form method="POST" action="{{Route('search')}}" class="col-md-6">
						<div class="form-group">
							<label>Domain Name : </label>
							<input type="text" value="{{ old('domain_name') }}" name="domain_name" id="domain_name" class="form-control">
						</div>
						<div class="form-group">
							<label>Registrant Country : </label>
							<input type="text" value="{{old('registrant_country')}}" name="registrant_country" id="registrant_country" class="form-control">
						</div>
						<div class="form-group">
							<label>Registrant State : </label>
							<input type="text" value="{{old('registrant_state')}}" name="registrant_state" id="registrant_state" class="form-control">
						</div>
						<div class="form-group">
							<label>Domains Create Date</label>
							<input type="date" value="{{old('domains_create_date')}}" name="domains_create_date" id="registered_date" class="form-control">
						</div>
						<div class="from-group">
							<label>Select Domains Extensions</label>
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
							                    <input type="checkbox" name="domain_ext[0]" value="com" />com</li>
							                <li>
							                    <input type="checkbox" name="domain_ext[1]" value="io" />io</li>
							                <li>
							                    <input type="checkbox" name="domain_ext[2]" value="net" />net</li>
							                <li>
							                    <input type="checkbox" name="domain_ext[3]" value="org" />org</li>
							                <li>
							                    <input type="checkbox" name="domain_ext[4]" value="gov" />gov</li>
							                <li>
							                    <input type="checkbox" name="domain_ext[5]" value="edu" />edu</li>
							                <li>
							                    <input type="checkbox" name="domain_ext[6]" value="in" />in</li>
							            </ul>
							        </div>
							    </dd>
							  <!-- <button>Filter</button> -->
							</dl>
						</div>
						<div class="form-group">
							<label>cell number</label>
							<input type="checkbox" name="cell_number" value="cell number">
						
							<label>landline number</label>
							<input type="checkbox" name="landline_number" value="landline number">

							
								<label>Data Per-Page</label>
								<select id="pagination" name="pagination">
									<option value="20">20</option>
									<option value="20">50</option>
									<option value="20">100</option>
									<option value="20">500</option>
									<option value="20">1000</option>
								</select>
							

						</div>

						<div>
							<label>Sort filter</label>
							<select id="sort" name="sort">
								<option value="unlocked_asnd">unlocked_asnd</option>
								<option value="unlocked_dcnd">unlocked_dcnd</option>
								<option value="domain_count_asnd">domain_count_asnd</option>
								<option value="domain_count_dcnd">domain_count_dcnd</option>
							</select>
						</div>

						



						
				
				<input type="hidden" name="_token" value="{{csrf_token()}}">
				<input class="btn btn-info pull-right" type="submit" name="Submit" value="Submit">

			</form>
				
			</div>


			<br><br>


			
 	 		
			<div>
			@if($record !== null)
			<span class="pull-left"> Total Leads   : {{ $record->total()}}</span>
			
			<span class="pull-right"> Total Domains : {{$totalDomains}}</span>

				<table class="table table-hover table-bordered domainDAta">
					<tr>
						<th>Check box</th>
						<th>Domain Name</th>
						<th>Registrant Name</th>
						<th>Registrant Email</th>
						<th>Registrant Phone</th>
						<th>Domains Create Date</th>
						<th>Registrant Company</th>
					</tr>

					
					@foreach($record as $key=>$each)
					<tr>

						<th>
							
								
							
							 <button class="btn btn-primary" id="chkDomainForWebsiteID_{{$key}}" onclick="chkDomainForWebsite('{{$each->each_domain->first()->domain_name}}','{{$key}}')">Check website</button>
							
						</th>
						<th>
							
								<small id="domain_name_{{$key}}"><b>{{$each->each_domain->first()->domain_name}}</b></small>
							
							<br>
							<small> Unlocked Num : <span id="unlocked_num_{{$key}}">{{$each->unlocked_num}}</span></small>
							<br>
							<small > Total Domains : <a href="{{url('/')}}/lead/{{encrypt($each->registrant_email)}}">{{$each->domains_count}}</a></small> 
							<!-- leadArr[$each->registrant_email] -->
						</th>
						<th>
							
								<small id="registrant_name_{{$key}}">{{$each->registrant_name}}</small>
							

						</th>
						<th>
							
								<small id="registrant_email_{{$key}}">{{$each->registrant_email}}</small>
							
						</th>
						<th>
							
								<small id="registrant_phone_{{$key}}">{{$each->registrant_phone}}</small>

								@if(isset($each->valid_phone)) 

									@if($each->valid_phone->number_type == "Cell Number")
									<img id="phone_{{$key}}" style="width:20px; height:40px" src="{{url('/')}}/images/phone.png">

								

									@elseif($each->valid_phone->number_type == "Landline")
									<img id="phone_{{$key}}" style="width:30px; height:40px" src="{{url('/')}}/images/landline.png">

									@endif

								@endif
							
						</th>
						<th>
							
								<small id="domains_create_date_{{$key}}">{{$each->first()->each_domain->first()->domains_info->domains_create_date}}</small>
							
						</th>
						<th>
							
								<small id="registrant_company_{{$key}}">{{$each->registrant_company}}</small>
							
						</th>
					</tr>
					@endforeach
					
				</table>
			@endif
			</div>
			@if($record)
			 {{$record->appends(\Request::except('page'))->links()}}
			 @endif


		</div>
		  <button style="display: none;" class="" id="popupid_for_domainexists" data-toggle="modal" data-target="#myModal_for_reg">popup</button>
			<div class="modal fade" id="myModal_for_reg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
			aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						×</button>
						<h4 class="modal-title" id="myLargeModalLabel">
						</h4>
						
						</div>

					</div>
				</div>
			</div>

		
</body>
	<script>
		
	  	$(function(){
	    	$( "#registered_date" ).datepicker({ dateFormat: 'yy-mm-dd' }).val();
	  	});
  	</script>



	<script type="text/javascript">




		var options = [];

		$('.dropdown-menu a' ).on( 'click', function( event ) {

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

		   event.preventDefault();
		});



	$(document).ready(function(){

		$(window).on('hashchange',function(){
			page = window.location.hash.replace('#','');
			getProducts(page);
		});

	});
	</script>

	<script type="text/javascript">
	

	    function chkDomainForWebsite(domain_name,key){
           var _token='{{csrf_token()}}';
           $.ajax({
               type:'POST',
               url:'chkWebsiteForDomain',
               beforeSend: function()
					{
						$('#chkDomainForWebsiteID_'+key).html('<span align="center"><img src="theme/images/loading.gif">checking...</span>');
					},
               data:'domain_name='+domain_name+'&_token='+_token,
               success:function(data){
               	  $("#myLargeModalLabel").text(data);
                  $("#popupid_for_domainexists").trigger('click');
                  $('#chkDomainForWebsiteID_'+key).html('Check website');
                 
               }
            });

	    }

		$(".dropdown dt a").on('click', function(e) {
		  $(".dropdown dd ul").slideToggle('fast');
		  e.preventDefault();
		});

		$(".dropdown dd ul li a").on('click', function(e) {
		  $(".dropdown dd ul").hide();
		  e.preventDefault();
		});

		function getSelectedValue(id) {
		  return $("#" + id).find("dt a span.value").html();
		}

		$(document).bind('click', function(e) {
		  var $clicked = $(e.target);
		  if (!$clicked.parents().hasClass("dropdown")) $(".dropdown dd ul").hide();
		});

		$('.mutliSelect input[type="checkbox"]').on('click', function(){

		  var title = $(this).closest('.mutliSelect').find('input[type="checkbox"]').val();
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
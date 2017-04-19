<html lang="en">
@include('layouts.header')
<head>


	<title>Search</title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" >

	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
  	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

  	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
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
			
                <?php if(Session::has('emailID_list'))
                {
                   $emailID_list=Session::get('emailID_list');
                   
                }else {
                 $emailID_list=array();

                }
                
               ?>
                 
			</div>

			<!-- form elements -->

		    
			<div>
				<a href="{{url('/')}}/myLeads/{{encrypt(\Auth::user()->id)}}">My Leads</a>
			</div>

			<div>
				<i class="fa fa-mobile" aria-hidden="true"></i>
			</div>


			<div class="col-md-12">

				<form method="POST" action="{{Route('search')}}" class="col-md-6">
						<div class="form-group">
							<label>Domain Name : </label>
							<input type="text" value="{{ Input::get('domain_name') }}" name="domain_name" id="domain_name" class="form-control">
						</div>
						<div class="form-group">
							<label>Registrant Country : </label>
							<input type="text" value="{{ Input::get('registrant_country') }}" name="registrant_country" id="registrant_country" class="form-control">
						</div>
						<div class="form-group">
							<label>Registrant State : </label>
							<input type="text" value="{{ Input::get('registrant_state') }}" name="registrant_state" id="registrant_state" class="form-control">
						</div>
						<div class="form-group">
							<label>Domains Create Date</label>
							<div class="row">
							<div class="col-sm-6">

							<div class="row">

							<div class="col-sm-6">	
							<input style="width: 150px" type="date" value="{{ Input::get('domains_create_date') }}" name="domains_create_date" id="registered_date" class="form-control" placeholder="From Date">
							</div>
							<div class="col-sm-6">
							<input style="width: 150px" type="date" value="{{ Input::get('domains_create_date2') }}" name="domains_create_date2" id="registered_date2" class="form-control" placeholder="To Date">
							</div>

							</div>

							</div>
							</div>

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
							<input type="checkbox" name="cell_number" value="cell number" @if(Input::get('cell_number')) checked @endif>
						
							<label>landline number</label>
							<input type="checkbox" name="landline_number" value="landline number" @if(Input::get('landline_number')) checked @endif>

							
								<label>Data Per-Page</label>
								<select id="pagination" name="pagination">
									<option value="10" @if(Input::get('pagination')=='10') selected @endif>10</option>
									<option value="20" @if(Input::get('pagination')=='20') selected @endif>20</option>
									<option value="50" @if(Input::get('pagination')=='50') selected @endif>50</option>
									<option value="100" @if(Input::get('pagination')=='100') selected @endif>100</option>
									<option value="200" @if(Input::get('pagination')=='200') selected @endif>200</option>
									<option value="500" @if(Input::get('pagination')=='500') selected @endif>500</option>
								</select>
						</div>
						<div class="row">
						<div class="col-sm-6">
							<label>Sort filter</label>
							<select id="sort" name="sort">
								<option value="unlocked_asnd" @if(Input::get('sort')=='unlocked_asnd') selected @endif>unlocked_asnd</option>
								<option value="unlocked_dcnd" @if(Input::get('sort')=='unlocked_dcnd') selected @endif>unlocked_dcnd</option>
								<option value="domain_count_asnd" @if(Input::get('sort')=='domain_count_asnd') selected @endif>domain_count_asnd</option>
								<option value="domain_count_dcnd" @if(Input::get('sort')=='domain_count_dcnd') selected @endif>domain_count_dcnd</option>
							</select>
						</div>
					<div class="col-sm-6">
				<input type="hidden" name="_token" value="{{csrf_token()}}">
				<input class="btn btn-info pull-right" type="submit" name="Submit" value="Submit">
				<div class="clearfix"></div>
				</div>
				</div>

			</form>
				
			</div>


			<br><br>
		
			<div>
			@if($record !== null)

			<span class="pull-left"> Total Leads   : {{$totalLeads}}</span>
			
			<span class="pull-right"> Total Domains : {{$totalDomains}}</span>

			<form class="col-md-12" style="margin-left: 10px;" action="{{ URL::to('downloadExcel') }}" class="form-horizontal" method="get" enctype="multipart/form-data">

		         <input type="hidden" name="domains_for_export" id="domains_for_export_id" value="">
		         <input type="hidden" id="leads_for_export" name="leads_for_export" value="">
		         <input type="hidden" name="domains_for_export_allChecked" id="domains_for_export_id_allChecked" value="0">
				 <button class="btn btn-primary" id="exportID">Export</button>
			</form>

				<table class="table table-hover table-bordered domainDAta">
					<tr>
						<th>Check box</th>
						<th>Create Website</th>
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
							@if(isset($users_array[$each['registrant_email']]))
								<input type="checkbox" id="ch_{{$key}}" onclick="unlock('{{$each['registrant_email']}}' , '{{$key}}')" name="ch_{{$key}}" checked="true" disabled="true">
								<input type="hidden" id="leads_id_{{$key}}"  class="leads_id" value="{{$each['id']}}">
							@else
								<input type="checkbox" id="ch_{{$key}}" onclick="unlock('{{$each['registrant_email']}}' , '{{$key}}')" name="ch_{{$key}}">	
								<input type="hidden" id="leads_id_{{$key}}"  class="leads_id" value="">
							@endif
						</th>

						<th>
						{{$each['id']}}
                            @if(isset($chkWebsite_array[$each['registrant_email']]))
								<button class="btn btn-primary" id="chkDomainForWebsiteID_{{$key}}" onclick="chkDomainForWebsite('{{$domain_list[$each['registrant_email']]['domain_name']}}','{{$key}}','{{$each['registrant_email']}}')" disabled="true">Created website</button>
							@else
								<button class="btn btn-primary" id="chkDomainForWebsiteID_{{$key}}" onclick="chkDomainForWebsite('{{$domain_list[$each['registrant_email']]['domain_name']}}','{{$key}}','{{$each['registrant_email']}}')" >Create website</button>
							@endif
							
							@if(isset($users_array[$each['registrant_email']]))
								<input type="checkbox" name="downloadcsv" value="1" class="eachrow_download" id="eachrow_download_{{$key}}" emailID="{{$each['registrant_email']}}"  @if(in_array($each['registrant_email'], $emailID_list)) {{'checked'}}  @endif >
							@else
								<small id="showCSV_{{$key}}" style="display: none"><input type="checkbox" name="downloadcsv" value="1" class="eachrow_download" id="eachrow_download_{{$key}}" emailID="{{$each['registrant_email']}}" <?php if(in_array($each['registrant_email'], $emailID_list)){ echo "checked";} ?>>
								</small>
								<small id="hideCSV_{{$key}}">***</small>
							@endif
						</th>

						<th>
							@if(isset($users_array[$each['registrant_email']]))
								<small><b  id="domain_name_{{$key}}">{{$domain_list[$each['registrant_email']]['domain_name']}}</b></small>
							@else
								<small id="domain_name_{{$key}}">***</small>
							@endif
							<br>
							<small> Unlocked Num : <span id="unlocked_num_{{$key}}">{{$each['unlocked_num']}}</span></small>
							<br>
							<small > Total Domains : <a href="{{url('/')}}/lead/{{encrypt($each['registrant_email'])}}">{{$each['domains_count']}}</a></small> 
							<!-- leadArr[$each->registrant_email] -->
						</th>

						<th>
							@if(isset($users_array[$each['registrant_email']]))
								<small id="registrant_name_{{$key}}">{{$each['registrant_name']}}</small>
							@else
								<small id="registrant_name_{{$key}}">***</small>
							@endif

						</th>

						<th>
							@if(isset($users_array[$each['registrant_email']]))
								<small id="registrant_email_{{$key}}">{{$each['registrant_email']}}</small>
							@else
								<small id="registrant_email_{{$key}}">***</small>
							@endif
						</th>

						<th>
							@if(isset($users_array[$each['registrant_email']]))	
								<small id="registrant_phone_{{$key}}">{{$each['registrant_phone']}}</small>

								@if(isset($each->valid_phone)) 

									@if($domain_list[$each['registrant_email']]['number_type'] == "Cell Number")
									<img id="phone_{{$key}}" style="width:20px; height:40px" src="{{url('/')}}/images/phone.png">

								

									@elseif($domain_list[$each['registrant_email']]['number_type'] == "Landline")
									<img id="phone_{{$key}}" style="width:30px; height:40px" src="{{url('/')}}/images/landline.png">

									@endif

								@endif

							@else
								<small id="registrant_phone_{{$key}}">***</small>
								@if(isset($each->valid_phone)) 

									@if($domain_list[$each['registrant_email']]['number_type'] == "Cell Number")
									<img  id="phone_{{$key}}" style="width:20px; height:40px; display:none" src="{{url('/')}}/images/phone.png">
									
									@elseif($domain_list[$each['registrant_email']]['number_type'] == "Landline")
									<img id="phone_{{$key}}" style="width:30px; height:40px; display:none" src="{{url('/')}}/images/landline.png">

									@endif
								@endif
							@endif
							<br>
						</th>

						<th>
							@if(isset($users_array[$each['registrant_email']]))
								<small id="domains_create_date_{{$key}}">{{$domain_list[$each['registrant_email']]['domains_create_date']}}</small>
							@else
								<small id="domains_create_date_{{$key}}">***</small>
							@endif
						</th>

						<th>
							@if(isset($users_array[$each['registrant_email']]))
								<small id="registrant_company_{{$key}}">{{$each['registrant_company']}}</small>
							@else
								<small id="registrant_company_{{$key}}">***</small>
							@endif
						</th>
					</tr>
					@endforeach
				</table>
			@endif
			</div>

			


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

	var leads_for_export = ''; // stores id
	$(function(){
		$('.leads_id').each(function(i,j){
			if(leads_for_export == '')
				leads_for_export += $(this).val();
			else
				leads_for_export += ","+$(this).val();
		});

		console.log(leads_for_export);
	});
   	
    var _token='{{csrf_token()}}';
    $('.eachrow_download').click(function(event){
   
	  // $("#domains_for_export_id_allChecked").val(0);
	  // $(".downloadcsv_all").prop( "checked", false);
	   var id=$(this).attr('id');
	   var emailID=$(this).attr('emailID');
		    if($("#"+id).is(':checked')) {
		    var  isChecked=1;
		    } else {
		    var  isChecked=0;
		     
		    }
	        
	        
		     
		     $.ajax({
               type:'POST',
               url:'storechkboxvariable',
               beforeSend: function()
					{
						//$('#chkDomainForWebsiteID_'+key).html('<span align="center"><img src="theme/images/loading.gif">checking...</span>');
					},
               data:'isChecked='+isChecked+'&_token='+_token+'&emailID='+emailID,
               success:function(response){
               	 
                 
               }
            });
   
	});

	function unlock(reg_em , key)
	{
		var id = '{{\Auth::user()->id}}';
		$.ajax({
			type : 'POST',
			url  : '/unlockleed',
			data : {_token:'{{csrf_token()}}',registrant_email:reg_em ,user_id:id},
			success :function(response)
			{
				console.log(response);
				$('#domain_name_'+key).text(response.domain_name);
				$('#registrant_email_'+key).text(response.registrant_email);
				$('#registrant_name_'+key).text(response.registrant_name);
				$('#registrant_phone_'+key).text(response.registrant_phone);
				$('#registrant_company_'+key).text(response.registrant_company);
				$('#domains_create_date_'+key).text(response.domains_create_date);
				$('#leads_id_'+key).val(response.id);
				if(leads_for_export == '')
					leads_for_export += response.id;
				else
					leads_for_export += ","+response.id;
				$('#phone_'+key).show();
				$('#ch_'+key).prop('checked'	, true);
				$('#ch_'+key).prop('disabled'	, true);
				$('#unlocked_num_'+key).text(response.unlocked_num);
				$('#showCSV_'+key).show();
				$('#hideCSV_'+key).hide();

				console.log(leads_for_export);
			}
		});
	}

  	$(function(){
    	$( "#registered_date" ).datepicker({ dateFormat: 'yy-mm-dd' }).val();
    	$( "#registered_date2" ).datepicker({ dateFormat: 'yy-mm-dd' }).val();
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
	    $('.downloadcsv_all').click(function(event){
   
	        $("#domains_for_export_id").val('');
	        
		    if($(this).is(':checked')) {
		      $(".eachrow_download").prop( "checked", true);
		      $("#domains_for_export_id_allChecked").val(1);
		    } else {
		       $(".eachrow_download").prop( "checked", false);
		       $("#domains_for_export_id_allChecked").val(0);
		    }
             $.ajax({
	               type:'POST',
	               url:'removeChkedEmailfromSession',
	               beforeSend: function()
						{
							//$('#chkDomainForWebsiteID_'+key).html('<span align="center"><img src="theme/images/loading.gif">checking...</span>');
						},
	               data:'_token='+_token,
	               success:function(response){
	               	 
	                 
	               }
                }); 
	   
        });

	    function chkDomainForWebsite(domain_name,key,registrant_email){
           var _token='{{csrf_token()}}';
           var user_id = '{{\Auth::user()->id}}';
           $.ajax({
               type:'POST',
               url:'chkWebsiteForDomain',
               beforeSend: function()
					{
						$('#chkDomainForWebsiteID_'+key).html('<span align="center"><img src="theme/images/loading.gif">checking...</span>');
					},
               data:'domain_name='+domain_name+'&_token='+_token+'&registrant_email='+registrant_email+'&user_id='+user_id,
               success:function(response){
               	  $("#myLargeModalLabel").text(response.message);
                  $("#popupid_for_domainexists").trigger('click');
                  $('#chkDomainForWebsiteID_'+key).html('Created website');
                   $('#chkDomainForWebsiteID_'+key).prop("disabled",true);
                 
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
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
				<!-- <a href="{{url('/')}}/myLeads/{{encrypt(\Auth::user()->id)}}">My Leads</a> -->
			</div>

			<div>
				<i class="fa fa-mobile" aria-hidden="true"></i>
			</div>


			@include('layouts.search')


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

			<div class="table-container">
				@include('home.search.searchTable', ['record' => $record, 'page' => $page, 'meta_id' => $meta_id, 'totalLeads' => $totalLeads, 'totalDomains' => $totalDomains, 
				'totalPage' => $totalPage, 'domain_list' => $domain_list, 'query_time' => $query_time, 'users_array' => $users_array]);
			</div>
				
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
	<script type="text/javascript">

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
			var chk = document.getElementById('ch_'+key);
			if(chk.checked == false) {
					return;
			}
			$.ajax({
					type : 'POST',
					url  : '/unlockleed',
					data : {_token:'{{csrf_token()}}',registrant_email:reg_em ,user_id:id},
					success :function(response)
					{
							if(response.status) {
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
									$('#ch_'+key).prop('checked'    , true);
									$('#ch_'+key).prop('disabled'   , true);
									$('#unlocked_num_'+key).text(response.unlocked_num);
									$('#showCSV_'+key).show();
									$('#hideCSV_'+key).hide();

									console.log(leads_for_export);
							} else {
									alert(response.message);
									//$(this).prop('checked', false);
							}
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
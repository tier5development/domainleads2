<html lang="en">
   @include('layouts.header')
   <head>
      <title>Search2</title>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" >
      <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
      <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
      <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
      <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
      <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
      <script type="text/javascript" src="{{url('/')}}/theme/js/bootstrap.js"></script>
      <link type="text/css" rel="stylesheet" href="{{url('/')}}/public/css/simplePagination.css"/>
      <script type="text/javascript" src="{{url('/')}}/public/js/jquery.simplePagination.js"></script>
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
         .search_form{
         border: 1px solid #ccc;
         border-radius: 5px;
         padding: 20px;
         }
         .overlay{background: rgba(0,0,0,0.7); width: 100%; height: 100%; position: fixed; top: 0;
         z-index: 1111;
         }
         .loader-main{width: 100px; height: 100px; position: absolute; margin-left: -50px; margin-top: -50px; top: 50%; left: 50%;}
         .loader-main img{max-width: 100%;}
      </style>
   </head>
   <body>
      <div id="ajax-loader" style="display: none;">
         <div class="overlay">
            <div class="loader-main">
               <img src="{{url('/')}}/images/loader.gif">
            </div>
         </div>
      </div>
      <div class="container">
         <div class="navbar-header">
            <?php if(Session::has('emailID_list')){
               $emailID_list=Session::get('emailID_list');
               
               }else {
                $emailID_list=array();
               
               }
               
                 $domainExtarray=Input::all();
                 if (array_key_exists("domain_ext",$domainExtarray)){
                 	 if (!array_key_exists("0",$domainExtarray['domain_ext'])){
                          $domainExtarray['domain_ext'][0]='';	
                 	 }	
                 	 if (!array_key_exists("1",$domainExtarray['domain_ext'])){
                          $domainExtarray['domain_ext'][1]='';	
                 	 }
                 	 if (!array_key_exists("2",$domainExtarray['domain_ext'])){
                          $domainExtarray['domain_ext'][2]='';	
                 	 }
                 	 if (!array_key_exists("3",$domainExtarray['domain_ext'])){
                          $domainExtarray['domain_ext'][3]='';	
                 	 }
                 	 if (!array_key_exists("4",$domainExtarray['domain_ext'])){
                          $domainExtarray['domain_ext'][4]='';	
                 	 }
                 	 if (!array_key_exists("5",$domainExtarray['domain_ext'])){
                          $domainExtarray['domain_ext'][5]='';	
                 	 }
                 	 if (!array_key_exists("6",$domainExtarray['domain_ext'])){
                          $domainExtarray['domain_ext'][6]='';	
                 	 }
                    
                 }
                 else
                 {
                   $domainExtarray['domain_ext'][0]='';	
                   $domainExtarray['domain_ext'][1]='';	
                   $domainExtarray['domain_ext'][2]='';	
                   $domainExtarray['domain_ext'][3]='';	
                   $domainExtarray['domain_ext'][4]='';	
                   $domainExtarray['domain_ext'][5]='';	
                   $domainExtarray['domain_ext'][6]='';	
                 }
                 
               
               ?>
         </div>
         <div>
            <a href="{{url('/')}}/myLeads/{{encrypt(\Auth::user()->id)}}">My Leads</a>
         </div>

         @include('layouts.search')

         <br><br>
         <div>

            @if($record !== null)
            <hr>
            <br><br>
            <label> </label>
            <form id="csv_leads_form" method="POST" action="/download_csv_single_page">
               <input type="hidden" name="_token" value="{{csrf_token()}}">
               <label>Total Leads :: </label>
               <span>{{$totalLeads}}</span>
               <br>
               <label>Total Domains ::</label>
               <span>{{$totalDomains}}</span>
               <br>
               <label>Search Time ::</label>
               <span id="search_time">  {{number_format($query_time,4)}} seconds</span>
               <br>

               <!-- <div id="ajax-loader" style="display: none; padding-left: 450px;">
                  <img src="{{url('/')}}/images/ajax-loader.gif">
                  </div> -->
               <div class="table-responsive">


               <table id="table" class="table table-hover table-bordered domainDAta">
                  <input id="exportLeads" type="submit" name="exportLeads" value="Export">
                  <input type="submit" class="pull-right" name="exportAllLeads" value="Export All Leads">
                  <input type="hidden" name="meta_id" value="{{$meta_id}}">
                  <input type="hidden" name="totalLeads" value="{{$totalLeads}}">
                  <input type="hidden" name="domainname" value="{{ Input::get('domain_name') }}">
                  <input type="hidden" name="domainext"  value="{{ null!==Input::get('domain_ext') 
                  ? implode(',',Input::get('domain_ext'))
                  : '' }}">
                  <input type="hidden" name="createdate1" value="{{ Input::get('domains_create_date') }}">
                  <input type="hidden" name="createdate2" value="{{ Input::get('domains_create_date2') }}">
                  <input type="hidden" name="cell" value="{{Input::get('cell_number')}}" >
                  <input type="hidden" name="landline" value="{{Input::get('landline_number')}}">
                  <!-- <button><id="exportLeads" class="btn btn-primary">Export</button>
                     <button id="exportAllLeads" class="btn btn-info pull-right">Export All</button> -->
                  <br><br><br>
                  <tr>
                     <th style="width: 40px"><input type="checkbox"  value="1" class="downloadcsv_all" id=""> Select Leads</th>
                     <th>Domain Name</th>
                     <th>Zip Code</th>
                     <th>Registrant Name</th>
                     <th>Registrant Email</th>
                     <th>Registrant Phone</th>
                     <th>Domains Create Date</th>
                     <th>Registrant Company</th>
                  </tr>

                  @foreach($record as $key=>$each)

                  <tr id="row_{{$key}}">
                     <td>
                        <small>
                        <input id="reg_email_{{$key}}" class="eachrow_download single_row" type="checkbox" name="csv_leads[]" value="{{$each['registrant_email']}}">
                        </small>
                     </td>
                     
                     <td>
                        <small><b class="reg_email" id="domain_name_{{$key}}">{{$each['domain_name']}}</b></small>
                        <br>
                        <small> Unlocked Num : <span class="unlocked_num" id="unlocked_num_{{$key}}">{{$each['unlocked_num']}}</span></small>
                        <br>
                        <small > Total Domains : <a class="domains_count" id="domains_count_{{$key}}" href="{{url('/')}}/lead/{{$each['email_link']}}">{{$each['domains_count']}}</a></small>
                        <!-- leadArr[$each->registrant_email] -->
                     </td>
                     <td>
                       <small id="registrant_zip_{{$key}}">{{$each['registrant_zip']}}</small>
                     </td>
                     <td>
                        <small class="reg_name" id="registrant_name_{{$key}}">{{$each['registrant_name']}}</small>	
                     </td>
                     <td>
                        <small class="reg_email" id="registrant_email_{{$key}}">{{$each['registrant_email']}} </small>
                     </td>
                     <td>
                        <?php
                          //$phone = explode('.',$each['registrant_phone']);
                          //$phone = isset($phone[1]) ? $phone[1] : $phone[0];
                         ?> 

                        <small class="reg_phone" id="registrant_phone_{{$key}}">{{$each['registrant_phone']}}</small>
                        @if(isset($each['number_type'])) 
                        @if($each['number_type']== "Cell Number")
                        <img class="mobile" id="phone_{{$key}}" style="width:20px; height:40px" src="{{url('/')}}/images/phone.png">
                        @elseif($each['number_type']=="Landline")
                        <img class="landline" id="phone_{{$key}}" style="width:30px; height:40px" src="{{url('/')}}/images/landline.png">
                        @endif
                        @endif
                     </td>
                     <td>
                        <small class="create_date" id="domains_create_date_{{$key}}">{{$each['domains_create_date']}}</small>
                     </td>
                     <td>
                        <small class="reg_company" id="registrant_company_{{$key}}">{{$each['registrant_company']}}</small>
                     </td>
                  </tr>
                  @endforeach
               </table>
               </div>
               <!-- <div id="paginate">
                  </div> -->
            </form>
            <div class="pg_" id="pages">
               <button class="pg_btn" value="prev" id="pg_prev">Previous</button>
               <?php $i=$page-1; ?>
               @while(++$i <= $totalPage)
               @if($i<10)
               <button class="pg_btn @if($i==1) btn-info @endif" id="pg_{{$i}}" value="{{$i}}">{{$i}}</button>
               @else
               <button class="pg_btn" id="pg_{{$i}}" value="{{$i}}" style="display:none;">{{$i}}</button>
               @endif
               @endwhile
               <button class="pg_btn" value="next" id="pg_next">Next</button>
            </div>
            <br><br><br>
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
      <div id="per_page">
      </div>
   </body>
   <script>
      var thisPage     = parseInt("{{$page}}");
      var totalPage    = parseInt("{{$totalPage}}");
      var URL          = "{{url('/')}}";
      var left_most    = 1;
      var per_page     = parseInt($('#pagination').val());
      var right_most   = Math.ceil(parseInt("{{$totalLeads}}")/per_page);
      var meta_id      = parseInt("{{$meta_id}}");
      var display_limit= 5;
      
      //alert(right_most);
      	
      
      function adjust()
      {
      	if(thisPage == left_most)
      	{
      		$('#pg_prev').hide();
      	}
      	else if(thisPage == right_most)
      	{
      		$('#pg_next').hide();
      	}
      	else
      	{
      		$('#pg_next').show();
      		$('#pg_prev').show();
      	}
      	if(totalPage < 2*display_limit)
      	{
      		$('#pg_next').hide();
      		$('#pg_prev').hide();
      	}
      }
      
      //page_form
      function load_new_page(page)
      {
      	if(isNaN(page)) return false;
      	$('#table').hide();
      	$('#ajax-loader').show();
      	var reg_date = $('#registered_date').val();
      	var reg_date2 = $('#registered_date2').val();
      	var domain_name = $('#domain_name').val();
      	var domain_ext = $('#domain_ext').val(); 
      	var num_type = $('#number_type').val();
      	var total_domains = "{{$totalDomains}}";
      	var total_leads = "{{$totalLeads}}";
      	var lead_list  = $(this).find('.leads_list_cls').val();
      	$('#pg_'+thisPage).removeClass('btn-info');
      	//var page = $(this).val();
      
      	$.ajax({
      		url  : URL+'/ajax_search_paginated',
      		type : 'post',
      		dataType: 'json',
      		data : {_token : "{{csrf_token()}}" , 
      				meta_id             : meta_id,
      				thisPage            : parseInt(page),
      				pagination          : per_page,
      				totalPage           : totalPage,
      				domain_ext          : domain_ext,
      				domain_name         : domain_name,
      				domains_create_date : reg_date,
      				domains_create_date2: reg_date2
      			},
      			success:function(response)
      			{
      				console.log(response);
      				$('.single_row').each(function(i,j){
      					$('#reg_email'+i).val('');
      					$('#domain_name_'+i).text('');
      					$('#unlocked_num_'+i).text('');
      					$('#domains_count_'+i).text('');
      					$('#domains_count_'+i).attr('href',null);
      					$('#domains_create_date_'+i).text('');
      					$('#registrant_phone_'+i).text('');
      					$('#registrant_name_'+i).text('');
      					$('#registrant_company_'+i).text('');
      					$('#registrant_country_'+i).text('');
      					$('#registrant_email_'+i).text('');
      					$('#phone_'+i).css('src','');
      					$('#row_'+i).hide();
      				});
      				thisPage = parseInt(page);
      				adjust();
      				$('#pg_'+thisPage).addClass('btn-info');
      				//setup_pages();
      				pages();
      				$('#search_time').text(response.time);
      				for(i=0 ; i<response.data.length ; i++)
      				{
      					//alert(i);
      					$('#row_'+i).show();
      					$('#reg_email_'+i).val(response.data[i]['registrant_email']);
      					$('#domain_name_'+i).text(response.data[i]['domain_name']);
      					$('#unlocked_num_'+i).text(response.data[i]['unlocked_num']);
      					$('#domains_count_'+i).text(response.data[i]['domains_count']);
      					$('#domains_count_'+i).attr('href',URL+'/lead/'+response.data[i]['email_link']);
      					$('#registrant_name_'+i).text(response.data[i]['registrant_name']);
      					$('#registrant_email_'+i).text(response.data[i]['registrant_email']);
      					$('#domains_create_date_'+i).text(response.data[i]['domains_create_date']);
      					$('#registrant_company_'+i).text(response.data[i]['registrant_company']);
      					$('#registrant_country_'+i).val(response.data[i]['registrant_company']);
      					$('#registrant_email_'+i).val(response.data[i]['registrant_email']);
      					$('#registrant_phone_'+i).text(response.data[i]['registrant_phone'])
      
      					if(response.data[i]['number_type'] == 'Landline')
      						$('#phone_'+i).css('src',URL+'/images/landline.png');
      					else if(response.data[i]['number_type'] == 'Cell Number')
      						$('#phone_'+i).css('src',URL+'/images/phone.png');
      					else
      						$('#phone_'+i).css('src',null);
      				}
      				$('#table').show();
      				$('#ajax-loader').hide();
      			}
            
      		});
      }
      
      $('.pg_btn').click(function(e){
      	e.preventDefault();
      	load_new_page(parseInt($(this).val()));
      });
      
      $('#pg_next').click(function(e){
      	e.preventDefault();
      	load_new_page(parseInt(thisPage)+1);
      	adjust();
      });
      
      $('#pg_prev').click(function(e){
      	e.preventDefault();
      	load_new_page(parseInt(thisPage)-1);
      	adjust();
      });
      
      
      $(function(){
      	pages();
      	setup_pages();
      });
      
      $('#next').click(function(e){
      	thisPage += 5;
      	setup_pages();
      });
      $('#previous').click(function(e){
      	thisPage -= 5;
      	setup_pages();
      });
      
      function pages()
      {
      	//console.log('in pages');
      	//console.log(thisPage);
      	low  = parseInt(thisPage)-display_limit;
      	high = parseInt(thisPage)+display_limit;
      	//console.log(low,high);
      	if(low < 0)
      	{
      		high = high - low;
      		low  = low  - low; 
      	}
      	//console.log(low,high);
      	if(high > totalPage)
      	{
      		high = high - (high - totalPage);
      		low  = low - (high - totalPage);
      	} 
      	//console.log(low,high);
      	$('.pg_btn').each(function(i,j){
      
      		if(i>= low && i<=high)
      		{
      			$('#pg_'+i).show();
      			//console.log(i,'--show');
      		}
      		else
      		{
      			$('#pg_'+i).hide();
      			//console.log(i,'--hide');
      		}
      	});
      	adjust();
      }
      
      function setup_pages()
      {
      	$('#page_forms').hide();
      	var pages     = [];
      	var limit = 9;
      	l  = parseInt(thisPage) -5;
      	h  = parseInt(thisPage) +5;
      	l_most = 0;
      	r_most = 
      	
      	$('.page_form').each(function(i,j)
      	{
      		if(thisPage == 1)
      		{
      			if(i<10)
      				$(this).show();
      
      			else
      				$(this).hide();
      		}
      		else
      		{
      			if(i>=l && i<=h)
      			{
      				$(this).show();
      				console.log('++show-- ',i,thisPage,l,h);
      			}
      			else
      			{
      				$(this).hide();
      				console.log('++hide-- ',i,thisPage);
      			}
      		}
      	});
      	if(thisPage <= left_most+limit)
      	{
      		$('#previous').hide();
      	}	
      	else if(thisPage >= right_most-limit)
      	{
      		$('#next').hide();
      	}
      	else
      	{
      		$('#previous').show();
      		$('#next').show();
      	}
      	$('#page_forms').show();
      }
      
       	$(function(){
       		$( "#registered_date" ).datepicker({ dateFormat: 'yy-mm-dd' }).val();
         	$( "#registered_date2" ).datepicker({ dateFormat: 'yy-mm-dd' }).val();
       	});
       	$("#refine_searchID").click(function(){
      
      	    $("#postSearchDataForm").submit();
      
         });
       	
   </script>
   <script type="text/javascript">
      var options = [];
      
      $('.dropdown-menu a' ).on( 'click', function( event ) {
      
      	//alert(1);
         var $target = $( event.currentTarget ),
             val = $target.attr( 'data-value' ),
             $inp = $target.find( 'input' ),
             idx;
      
         if (( idx = options.indexOf(val)) > -1) {
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
      	    });
      
      
          
          $('#exportLeads').click(function(e){
          	
          	var csv_flag = 0;
          	$('.eachrow_download').each(function(e2){
      
          		if(!$(this).is(':checked'))
          		{
          			csv_flag = 1;
          		}
          	});
      
          	if(csv_flag == 1)
          	{
          		return true;
          	}
          	else
          	{
          		alert('please select some leads');
          	}
          });
      
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
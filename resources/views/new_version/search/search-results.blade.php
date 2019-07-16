<!DOCTYPE html>
<html lang="en">

{{-- Include common user panel head used for all dashboard components 
    * Input title (Optional)
    * Brings in css and js files
    --}}
@include('new_version.section.user_panel_head', ['title' => 'Domainleads | Search Results'])

<body>

    {{-- Loader icon in the platform --}}
    @include('new_version.shared.loader')

    <div class="container">
        <div class="rightPanTgl">   
            <span></span>
            <span></span>
            <span></span>
        </div>
        {{-- Include common user panel header used for all dashboard components 
            * Input user object (compulsary)
            --}}
        @include('new_version.section.user_panel_header', ['user' => $user])
        
        <section class="mainBody">
            
            <div class="leftPanel leadUnlock">
             
                {{-- Include advanced sdearch options for more filtering on current result set
                    * Input --}}
                @include('new_version.shared.advanced-search-box')

                <div class="dataTableArea">
                    <div class="dataTableHeader">
                        <div class="unlockInfo">
                            <strong>{{isset($totalLeads) ? $totalLeads : 0}}</strong> leads found against your search!
                        </div>
                        <div class="dataTableHeaderRight">
                            <button class="refineSearch">
                                <div class="icon"><img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/Icon_refine_search.png" alt=""></div>
                                <p>Refine your search</p>
                            </button>
                            <div class="pageViewControl">
                            <label for="">SHOW : </label>
                                <div class="selectBox">
                                    <select data-pagination='1' id="slect-pagination-box" class="selectpage">
                                        <option {{$pagination == 10 ? 'selected' : ''}} value="10">10 per page</option>
                                        <option {{$pagination == 20 ? 'selected' : ''}} value="20">20 per page</option>
                                        <option {{$pagination == 50 ? 'selected' : ''}} value="50">50 per page</option>
                                        <option {{$pagination == 100 ? 'selected' : ''}} value="100">100 per page</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($user->user_type > config('settings.PLAN.L1'))
                    <div class="dataTableHeader">
                        <form method="POST" action="{{route('download_csv_single_page')}}" id="downloadDataForm">
                            {{csrf_field()}}
                            {{-- {{dd(Input::get('domain_ext'))}} --}}
                            <input type="hidden" name="currentPage" id="currentPage" value="{{$page}}">
                            <input  type="hidden" name="totalPagination" id="totalPagination" value="{{Input::get('pagination')}}">
                            <input type="hidden" name="mode" value="{{Input::get('mode')}}">  
                            <input type="hidden" name="domain_name" value="{{Input::get('domain_name')}}">
                            <input type="hidden" name="registrant_country" value="{{Request::get('registrant_country')}}">
                            <input type="hidden" name="registrant_state" value="{{Request::get('registrant_country')}}">
                            <input type="hidden" name="registrant_zip" value="{{Request::get('registrant_zip')}}">
                            <input type="hidden" name="domains_create_date" value="{{ Input::get('domains_create_date') }}">
                            <input type="hidden" name="domains_create_date2" value="{{ Input::get('domains_create_date2') }}">
                            <input type="hidden" name="domains_expired_date" value="{{Request::get('domains_expired_date')}}">
                            <input type="hidden" name="domains_expired_date2" value="{{Request::get('domains_expired_date2')}}">
                            
                            <input style="float: left" class="orangeBtn" id="exportLeads" type="submit" name="exportLeads" value="Export" onclick="downloadAllCsvFn(0, event)">
                            <input style="float: right" class="orangeBtn" type="submit" name="exportAllLeads" value="Export All Leads" onclick="downloadAllCsvFn(1, event)">
                            <input type="hidden" name="meta_id" value="{{$meta_id}}">
                            <input type="hidden" name="totalLeads" value="{{$totalLeads}}">
                            
                            <input type="hidden" name="domain_ext"  value="{{ Input::get('domain_ext') }}">
                            
                            <input type="hidden" name="cell" value="{{Input::get('cell_number')}}">
                            <input type="hidden" name="landline" value="{{Input::get('landline_number')}}">
                            {{-- <button type="submit" class="orangeBtn pull-left">Export</button>
                            <button type="submit" class="orangeBtn pull-right">Export All</button> --}}
                        </form>
                    </div>
                    @endif
                    
                    {{-- Include common search results table : this is reused in multiple places across different user profiles and admin --}}        
                    @include('new_version.shared.search-results-table', [
                        'record'        =>  isset($record) ? $record : [],
                        'users_array'   =>  isset($users_array) ? $users_array : [],
                        'restricted'    =>  isset($restricted) ? $restricted : false,
                        'user'          =>  $user
                    ])
                </div>

                <div class="pagination-parent">
                    <div class="pagination-cl">
                        @if(isset($totalPage) && $totalPage > 0)
                        <div class="pg_" id="pages">
                            <button class="pg_btn" value="prev" id="pg_prev"><<</button>
                            <?php $i=$page-1; ?>
                            @while(++$i <= $totalPage)
                                @if($i<=6)
                                    <button class="pg_btn @if($i==1) btn-info @endif" id="pg_{{$i}}" value="{{$i}}">{{$i}}</button>
                                @else
                                    <button class="pg_btn" id="pg_{{$i}}" value="{{$i}}" style="display:none;">{{$i}}</button>
                                @endif
                            @endwhile
                                <button class="pg_btn" value="next" id="pg_next">>></button>
                        </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Include common dashboard right panel --}}
            @include('new_version.shared.right-panel')
            
            {{-- Include common footer --}}
            @include('new_version.shared.dashboard-footer', ['class' => 'footer mobileOnly'])
            
        </section>

        @include('new_version.shared.dashboard-footer', ['class' => 'footer'])
    </div>

    

    {{-- Include common sticky note --}}
    @include('new_version.shared.sticky-note')

    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/custom2.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/common.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/right-panel.js"></script>
    <script>

        var req_pagination = "{{isset($pagination) ? $pagination : 10}}";
        
        var submitFormCustom = function() {
            $('#loader-icon').show();
            $('#postAdvancedSearchDataForm').submit();
        }
        var bodyScroll;

        var downloadAllCsvFn = function(all, e) {
            // Make an ajax call to generate the csv file
            e.preventDefault()
            $.ajax({
                url : "{{config('settings.DL-API')}}/api/csv-download",
                type: "post",
                data: $("#downloadDataForm").serialize()+"&all="+all,
                beforeSend : function() {
                    $('#loader-icon').show();
                }, success : function(data) {
                    console.log("data : ", data)
                    window.location.href = data.path;
                }, error: function(er) {
                    console.log("er : ", er)
                }, complete : function() {
                    $('#loader-icon').hide();
                }
            })
        }

        $(document).ready(function(){
            
            $(".rightPanTgl").click(function(){    
                if($(this).hasClass("open")){
                    $(this).removeClass("open");
                    $(".rightPanel").removeClass("open");
                    $(".mainBody").scrollTop(bodyScroll);
                    $(".leftPanel").css("opacity","1");
                } else {
                    bodyScroll = $(".mainBody").scrollTop();
                    $(".mainBody").scrollTop(0);
                    $(this).addClass("open");
                    $(".rightPanel").addClass("open");
                    $(".leftPanel").css("opacity","0.2");
                }
            });

            $(".refineSearch").click(function(){
                $(".filterPopup").fadeIn();
            });
            $(".closeFilterPopup").click(function(){
                $(".filterPopup").fadeOut();
            });

            $('#slect-pagination-box').change(function(e) {
                console.log(e);
                alert($(this).val());
            });
        });

        $('.selectpage').each(function(){

            console.log('each executed', req_pagination);
            var thisInstance = $(this);

            var thisVal = $(this), numberOfOptions = $(this).children('option').length;

            thisVal.addClass('select-hidden'); 
            thisVal.wrap('<div class="select"></div>');
            thisVal.after('<div class="select-styled"></div>');

            var styledSelect = thisVal.next('div.select-styled');
            // styledSelect.text(thisVal.children('option').eq(0).text());
            styledSelect.text(thisVal.children('option:selected').text());

            var list = $('<ul />', {
                'class': 'select-options'
            }).insertAfter(styledSelect);

            for (var i = 0; i < numberOfOptions; i++) {
                $('<li />', {
                    text: thisVal.children('option').eq(i).text(),
                    rel: thisVal.children('option').eq(i).val()
                }).appendTo(list);
            }

            var listItems = list.children('li');
            styledSelect.click(function(e) {
                e.stopPropagation();
                $('div.select-styled.active').not(this).each(function(){
                    $(this).removeClass('active').next('ul.select-options').fadeOut(200);
                });
                $(this).toggleClass('active').next('ul.select-options').fadeToggle(200);
            });

            listItems.click(function(e) {
                e.stopPropagation();
                console.log('clicked');
                styledSelect.text($(this).text()).removeClass('active');
                thisVal.val($(this).attr('rel'));
                list.fadeOut(200);

                if(thisInstance.data('pagination') !== undefined) {
                    req_pagination = thisVal.val();
                    console.log('pagination', thisInstance.data('pagination'), thisVal.val());
                    $('#pagination').val(thisVal.val());
                }

                // Used for advanced-search-box
                if(thisInstance.data('stopsubmit') === undefined) {
                    console.log('not pagination', thisInstance.data('stopsubmit'));
                    submitFormCustom(); 
                }
                // console.log('afsdckhjtaykj kjfy', thisInstance.data('stopsubmit'));
                // submitFormCustom();
            });

            $(document).click(function() {
                styledSelect.removeClass('active');
                list.fadeOut(200);
            });
        });

        @if(count($record) > 0)
        var thisPage     = parseInt("{{$page}}");
        var totalPage    = parseInt("{{$totalPage}}");
        var URL          = "{{url('/')}}";
        var left_most    = 1;
        var per_page     = parseInt($('#pagination').val()); 
        var right_most   = Math.ceil(parseInt("{{$totalLeads}}")/per_page);
        var meta_id      = parseInt("{{$meta_id}}");
        var display_limit= 2;

        $(function() {
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

        function adjust()
        {
            if(thisPage == left_most) {
                $('#pg_prev').hide();
            }
            else if(thisPage == right_most) {
                $('#pg_next').hide();
            }
            else {
                $('#pg_next').show();
                $('#pg_prev').show();
            }
            
            if(totalPage < 2*display_limit)
            {
                $('#pg_next').hide();
                $('#pg_prev').hide();
            }
        }

        function pages()
        {
            low  = parseInt(thisPage)-display_limit;
            high = parseInt(thisPage)+display_limit;   
            if(low < 0) {
                high = high - low;
                low  = low  - low;
            }
            
            if(high > totalPage) {
                high = high - (high - totalPage);
                low  = low - (high - totalPage);
            }
            
            $('.pg_btn').each(function(i,j){
                if(i>= low && i<=high) $('#pg_'+i).show();
                else $('#pg_'+i).hide();
            });
            adjust();
        }

        function setup_pages()
        {
            $('#page_forms').hide();
            var pages     = [];
            var limit     = 6;
            l  = parseInt(thisPage) -5;
            h  = parseInt(thisPage) +5;
            l_most = 0;
            r_most =

            $('.page_form').each(function(i,j) {
                if(thisPage == 1) {
                    if(i<10) $(this).show();
                    else $(this).hide();
                } else {
                    if(i>=l && i<=h) {
                        $(this).show();
                        console.log('++show-- ',i,thisPage,l,h);
                    } else {
                        $(this).hide();
                        console.log('++hide-- ',i,thisPage);
                    }
                }
            });
            
            if(thisPage <= left_most+limit) {
                $('#previous').hide();
            } else if(thisPage >= right_most-limit) {
                $('#next').hide();
            } else {
                $('#previous').show();
                $('#next').show();
            }
            $('#page_forms').show();
        }
   	
        function load_new_page(page) {
            console.log(page);
            if(isNaN(page)) return false;
            // $('#table').hide();
            // $('#ajax-loader').show();
            var reg_date        = $('#registered_date').val();
            var reg_date2       = $('#registered_date2').val();
            var expiry_date     = $('#domains_expired_date').val();
            var expiry_date2    = $('#domains_expired_date2').val();
            var mode            = $('#mode').val();
            var domain_name     = $('#domain_name').val();
            var domain_ext      = $('#domain_ext').val();
            var num_type        = $('#number_type').val();
            var total_domains   = "{{$totalDomains}}";
            var total_leads     = "{{$totalLeads}}";
            var lead_list       = $(this).find('.leads_list_cls').val();
            $('#pg_'+thisPage).removeClass('btn-info');
            //var page = $(this).val();

            $.ajax({
                url  : "{{route('ajax_search_paginated_subadmin')}}",
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
                    domains_create_date2: reg_date2,
                    mode : mode,
                    domains_expiry_date : expiry_date,
                    domains_expiry_date2 : expiry_date2,
                }, beforeSend: function() {
                    $('#loader-icon').show();
                }, success:function(response) {
                    console.log(response);
                    if(response.status == true) {
                        $('#search-result-container').empty().append(response.view);
                        // $('.table-container').empty();
                        // $('.table-container').append(response.view);
                        thisPage = parseInt(page);
                        $('#currentPage').val(thisPage);
                        adjust();
                        $('#pg_'+thisPage).addClass('btn-info');
                        pages();
                    } else {
                        thisPage = parseInt(page);
                        adjust();
                        $('#pg_'+thisPage).addClass('btn-info');
                        pages();
                    }
                    // $('#table').show();
                    // $('#ajax-loader').hide();
                }, error : function(er, status) {
                    console.log('err : ', er);
                    // if(er.status == 401) {
                        window.location.replace("{{route('loginPage')}}");
                    // }
                }, complete: function() {
                    $('#loader-icon').hide();
                }
            });
        }

        function clickLink(t, key) {
            var restrict = $('#domain_name_'+key).data('restrict');
            if(restrict == 1) {
                alert('You dont have access over this data.');
            } else {
                window.open($(t).data('ref'));
            }
        }

        function unlock(reg_em , key, ph, ph_type)
        {
            var id = '{{$user->id}}';
            var domain_name = $('#domain_name_'+key).data('domainname');
            $.ajax({
                type : 'POST',
                url  : "{{route('unlockleed')}}",
                data : { _token:'{{csrf_token()}}',
                    registrant_email:reg_em ,
                    user_id:id, 
                    domain_name: domain_name,
                    key: key,
                    ph: ph,
                    ph_type : ph_type
                }, beforeSend: function() {
                    // Show loader
                    $('#loader-icon').show();
                }, success :function(response) {
                    $('#loader-icon').hide();
                    if(response.status) {
                        $('#tr_'+key).removeClass('locked').empty().append(response.view);
                        r = response.usageMatrix;
                        if(r !== null && r !== undefined) {
                            canvasObj.setCanvas();
                            canvasObj.setCurve(r.leadsUnlocked, r.limit);
                            canvasObj.drawProgressBar(10);
                            $('#currentUnlockedCount').text(r.leadsUnlocked);
                            $('#perDayLimitCount').text(r.limit);
                            $('#tillDateCount').text(r.allLeadsUnlocked);
                        }
                    } else {
                        alert(response.message);
                    }
                }, error : function(er) {
                    $('#loader-icon').hide();
                    if(er.status == 401) {
                        window.location.replace("{{route('loginPage')}}");
                    }
                }
            });
        }
        @endif
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">

{{-- Include common user panel head used for all dashboard components 
    * Input title (Optional)
    * Brings in css and js files
    --}}
@include('new_version.section.user_panel_head', ['title' => 'Domainleads | Unlocked Leads'])
<link rel="stylesheet" href="{{config('settings.APPLICATION-DOMAIN')}}/public/css/new_design/laravel-pagination.css">
<body>


    @php
        $country_codes = country_codes();
    @endphp

    {{-- Loader icon in the platform --}}
    @include('new_version.shared.loader')

    <div class="container">

        {{-- Include common user panel header used for all dashboard components 
            * Input user object (compulsary)
            --}}
        @include('new_version.section.user_panel_header', ['user' => $user])
        
        <section class="mainBody">
            <div class="leftPanel leadUnlock">

                <div class="dataTableArea">
                    <div class="dataTableHeader">
                        <div class="unlockInfo">
                            <strong>{{isset($domains) ? $domains->total() : 0}}</strong> result{{$domains->total() > 1 ? 's':''}} found against your search!
                        </div>

                        <div class="dataTableHeaderRight">
                            <form method="POST" action = "{{route('downloadUnlockedLeads')}}" id="downloadCsvForm">
                                <button type="submit" class="greenBtn">Download CSV</button>
                                <input type="hidden" name = "date" value="{{Request::has('date') ? Request::get('date') : null}}">
                                {{csrf_field()}}
                            </form>
                            <form id="unlockedLeadsForm" class="" action="{{route('myUnlockedLeadsPost')}}" method="POST">
                                <div class="dateArea">
                                    <div class="date">
                                        <input id="filterDate" class="dateHiddenField" type="hidden" name = "date" value="{{Request::has('date') ? Request::get('date') : null}}" style="display:none;">
                                        <input type="text" class="month" placeholder="mm">
                                        <input type="text" class="day" placeholder="dd">
                                        <input type="text" class="year" placeholder="yyyy">
                                    </div>
                                </div>
                                <input type="hidden" name = "perpage" id="perpageVal" value="{{Request::has('perpage') ? Request::get('perpage') : null}}">
                                <button type="submit" class="orangeBtn">Filter</button>
                                
                                {{csrf_field()}}
                            </form>
                            
                            
                            <div class="pageViewControl">
                                <label for="">SHOW:</label>
                                <div class="selectBox">
                                    <select class="selectpage">
                                        <option {{Request::has('perpage') && Request::get('perpage') == 10 ? 'selected' : ''}} value="10">10 per page</option>
                                        <option {{Request::has('perpage') && Request::get('perpage') == 20 ? 'selected' : ''}} value="20">20 per page</option>
                                        <option {{Request::has('perpage') && Request::get('perpage') == 50 ? 'selected' : ''}} value="50">50 per page</option>
                                        <option {{Request::has('perpage') && Request::get('perpage') == 100 ? 'selected' : ''}} value="100">100 per page</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- All leads unlocked goes here --}}
                    <div class="datatable" id="search-result-container">
                        <a class="refresh-anchor" href="{{route('myUnlockedLeads')}}">Refresh </a>
                        
                        <table cellpadding="0" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>DOMAIN<br>NAME</th>
                                    <th>REGISTRANT<br>NAME, EMAIL</th>
                                    <th>REGISTRANT<br>PHONE</th>
                                    <th>CREATED<br>DATE<br><small>(M-D-Y)</small></th>
                                    <th>EXPIRY<br>DATE<br><small>(M-D-Y)</small></th>
                                    <th>REGISTRANT<br>COMPANY</th>
                                    <th>DATE OF UNLOCK<br><small>(M-D-Y)</small></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($domains as $key => $each)
                                    <tr>
                                        <td>
                                            <p data-restrict="1" id="domain_name_{{$key}}"> {{$each->domain_name}}</p>
                                            <div class="leadStatus">
                                                <p class="locked">
                                                    <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_lock_opened.png" alt="">
                                                    <span>{!!$each->lead ? $each->lead->unlocked_num : '<i><small>unavailable</small></i>'!!}</span>
                                                </p>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="name wordBreak">
                                                {{$each->registrant_fname}}&nbsp;{{$each->registrant_lname}}
                                            </p>
                                            <p class="email wordBreak">
                                                {{$each->registrant_email}}
                                            </p>
                                            <p class="country">
                                                <!-- TODO -->
                                                @php
                                                    $country_abr = isset($country_codes[ucwords(strtolower($each->registrant_country))]) 
                                                        ? strtoupper($country_codes[ucwords(strtolower($each->registrant_country))]) : null;
                                                @endphp
                                                
                                                <img 
                                                    @if(strlen($country_abr) > 0)
                                                        src="{{config('settings.APPLICATION-DOMAIN')}}/public/svg/{{$country_abr}}.svg" 
                                                    @endif
                                                alt="" style="width: 18px; height: 18px;">
                                                <span>{!! $each->lead ? $each->lead->registrant_country : '<i><small>unavailable</small></i>'!!}</span>
                                            </p>
                                        </td>
                                        <td>
                                            
                                            @if($each->lead)
                                                @php 
                                                    $phone = $each->lead ? $each->lead->registrant_phone : '<i><small>unavailable</small></i>'; 
                                                    $phoneType = $each->lead ? ($each->lead->valid_phone ? $each->lead->valid_phone->number_type : null) : '<i><small>unavailable</small></i>';
                                                @endphp
                                                <p class="phone">
                                                    @if(strtolower(trim($phoneType)) == 'cell number')
                                                        <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_mobile.png" alt="">
                                                    @elseif(strtolower(trim($phoneType)) == 'landline')
                                                        <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_land_phone.png" alt="">
                                                    @endif
                                                    <span>{{ str_replace('.', '-', $phone) }}</span>
                                                </p>
                                            @else

                                            @endif
                                        </td>
                                        <td>
                                            <p><span>{{DateTime::createFromFormat('Y-m-d', $each->domains_create_date)->format('m-d-Y')}}</span></p>
                                        </td>
                                        <td>
                                            <p><span>{{DateTime::createFromFormat('Y-m-d', $each->expiry_date)->format('m-d-Y')}}</span></p>
                                        </td>
                                        <td>
                                            <p><span class="wordBreak">{{$each->registrant_company}}</span></p>
                                        </td>
                                        <td>
                                            <p><span>{{$each->created_at->format('m-d-Y')}}</span></p>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($domains->count() > 0)
                        <div class="paginate">
                            {{$domains->appends(['date' => Request::has('date') ? Request::get('date') : null,
                            'perpage' => Request::has('perpage') ? Request::get('perpage') : 20])->links()}}
                        </div>
                    @endif
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
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/right-panel.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/common.js"></script>
    <script>


        var preDate = "{{Request::has('date') ? Request::get('date') : ''}}";

        

        var req_pagination = "{{Request::has('pagination') ? Request::get('pagination') : 10}}";
        
        // var submitFormCustom = function() {
        //     $('#loader-icon').show();
        //     $('#unlockedLeadsForm').submit();
        // }

        var submitFormCustom = function() {
            $('#loader-icon').show();
            $('#unlockedLeadsForm').submit();
        }
        
        $(document).ready(function() {
            

            (function() {
                console.log('predate', preDate);
                var dateSelect = preDate.split("-");
                // console.log(dateSelect);
                $('.dateHiddenField').nextAll(".year").val(dateSelect[0]);
                $('.dateHiddenField').nextAll(".month").val(dateSelect[1]);
                $('.dateHiddenField').nextAll(".day").val(dateSelect[2]);
            })();

            $( "#filterDate").datepicker({
                dateFormat: "yy-mm-dd",
                showOn: "button",
                buttonImage: "/public/images/icon_calendar.png",
                buttonImageOnly: true,
            });

            $(".dateHiddenField").change(function(){
                var dateSelect = $(this).val().split("-");
                $(this).nextAll(".year").val(dateSelect[0]);
                $(this).nextAll(".month").val(dateSelect[1]);
                $(this).nextAll(".day").val(dateSelect[2]);
            });

            $(".refineSearch").click(function(){
                $(".filterPopup").fadeIn();
            });

            $(".closeFilterPopup").click(function(){
                $(".filterPopup").fadeOut();
            });

            // setTimeout(() => {
            //     $('#loader-icon').hide();
            // }, 300);

            $('#slect-pagination-box').change(function(e) {
                alert($(this).val());
            });
        });

        $('.selectpage').each(function(){
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
                req_pagination = thisVal.val();
                // $('#pagination').val(thisVal.val());

                $('.selectBox select option[value="' + thisVal.val() + '"]').html();
                $('#perpageVal').val(thisVal.val());
                submitFormCustom();
            });

            $(document).click(function() {
                styledSelect.removeClass('active');
                list.fadeOut(200);
            });
        });

        
    </script>
</body>
</html>
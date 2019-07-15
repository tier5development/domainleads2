
<div class="searchDomainForm">
    <form method="POST" action="{{Route('search')}}" class="col-md-6 search_form" id="postSearchDataForm">
        <div class="formRow">
            {{-- <div class="rowHeading">I am looking for...</div> --}}
            <div class="formRowInner">
                <div class="radioCol">
                    <div class="radio">
                        <input type="radio" checked name="mode" value="newly_registered" {{Input::get('mode') == 'newly_registered' ? 'checked' : '' }}>
                        <p><span></span></p>
                    </div>
                    <label for="">Newly registered domains</label>
                </div>
                <div class="radioCol">
                    <div class="radio">
                        <input type="radio" name="mode" value="getting_expired" {{Input::get('mode') == 'getting_expired' ? 'checked' : ''}}>
                        <p><span></span></p>
                    </div>
                    <label for="">To be expired domains</label>
                </div>
            </div>
        </div>
        <div class="formRow">
            <div id="created_date_div">
                <div class="rowHeading">Registered date range</div>
                <div class="formRowInner dateRange">
                    <div class="dateArea">
                        <div class="date">
                            {{-- <input type="text" 
                            id="datepicker" 
                            class="dateHidden"> --}}
    
                            <input type="text" 
                                value="{{Request::get('domains_create_date') != null ? date('Y-m-d',strtotime(Request::get('domains_create_date'))) : '' }}" 
                                name="domains_create_date" 
                                id="registered_date" 
                                class="dateHidden"
                                placeholder="Start Date"
                                style="disply: none;">
    
                            <input type="text" id="registered-date1-m" value="" class="month" placeholder="mm" readonly>
                            <input type="text" id="registered-date1-d" value="" class="day" placeholder="dd" readonly>
                            <input type="text" id="registered-date1-y" value="" class="year" placeholder="yyyy" readonly>
                        </div>
                    </div>
                    <div class="dateArea endDate">
                        <div class="date">
                            {{-- <input type="text" id="datepicker2" class="dateHidden"> --}}
                            
                            <input type="text" 
                                value="{{Request::get('domains_create_date2') != null ? date('Y-m-d',strtotime(Request::get('domains_create_date2'))) : ''}}" 
                                name="domains_create_date2" 
                                id="registered_date2" 
                                class="dateHidden" 
                                placeholder="End Date"
                                style="display: none">
    
                            <input type="text" id="registered-date2-m" value="" class="month" placeholder="mm" readonly>
                            <input type="text" id="registered-date2-d" value="" class="day" placeholder="dd" readonly>
                            <input type="text" id="registered-date2-y" value="" class="year" placeholder="yyyy" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div id="expired_date_div" style="display: none">
                <div class="rowHeading">Expired date range</div>
                <div class="formRowInner dateRange">
                    <div class="dateArea">
                        <div class="date ">
                            {{-- <input type="text" 
                            id="datepicker" 
                            class="dateHidden"> --}}
    
                            <input type="text" 
                                value="{{Request::get('domains_expired_date') != null ? date('Y-m-d',strtotime(Request::get('domains_expired_date'))) : '' }}" 
                                name="domains_expired_date" 
                                id="expired_date" 
                                class="dateHidden"
                                placeholder="Start Date"
                                style="disply: none;">
                            
                            <input type="text" id="expired-date1-m" class="month" placeholder="mm" readonly>
                            <input type="text" id="expired-date1-d" class="day" placeholder="dd" readonly>
                            <input type="text" id="expired-date1-y" class="year" placeholder="yyyy" readonly>
                        </div>
                    </div>
                    <div class="dateArea endDate">
                        <div class="date">
                            {{-- <input type="text" id="datepicker2" class="dateHidden"> --}}
                            
                            <input type="text" 
                                value="{{Request::get('domains_expired_date2') != null ? date('Y-m-d',strtotime(Request::get('domains_expired_date2'))) : ''}}" 
                                name="domains_expired_date2" 
                                id="expired_date2" 
                                class="dateHidden" 
                                placeholder="End Date"
                                style="display: none">
    
                            <input type="text" id="expired-date2-m" class="month" placeholder="mm" readonly>
                            <input type="text" id="expired-date2-d" class="day" placeholder="dd" readonly>
                            <input type="text" id="expired-date2-y" class="year" placeholder="yyyy" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="formRow">
            <div class="rowHeading">Domain name</div>
            <div class="formRowInner domainName">
                {{-- <input type="text" placeholder="ex: money"> --}}
                <input type="text" 
                    placeholder="ex: money" 
                    value="{{ Request::get('domain_name') }}" 
                    name="domain_name" 
                    id="domain_name">

                <input type="hidden" name="domain_ext" id="domain_ext">
                <div class="selectBox">
                    <select type="checkbox" class="selectOption">
                        @foreach ($allExtensions as $item)
                            <option value="{{$item}}">{{$item}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="formRow address">
            <div class="formCol">
                <label for="">Registered Country</label>
                <input type="text" 
                    value   =   "{{ Request::get('registrant_country') }}" 
                    name    =   "registrant_country" 
                    id      =   "registrant_country"
                    placeholder = "ex:United States">
                {{-- <input type="text"> --}}
            </div>
            <div class="formCol">
                <label for="">State</label>
                {{-- <input type="text"> --}}
                <input 
                    type="text" 
                    value="{{ Request::get('registrant_state') }}" 
                    name="registrant_state" 
                    id="registrant_state"
                    placeholder = "ex:California">
            </div>
            <div class="formCol">
                <label for="">ZIP Code</label>
                {{-- <input type="text"> --}}
                <input 
                    type="text" 
                    name="registrant_zip" 
                    value="{{Request::get('registrant_zip')}}"
                    placeholder = "ex:54321">
            </div>
        </div>
        <div class="formRow submit">
            <button type="submit" type="button" id="searchDomains" class="orangeBtn">Search Domains</button>
        </div>

        {{-- Hidden fields --}}
        <input type="hidden" name="gt_ls_domaincount_no" value="0">
        <input type="hidden" name="domaincount_no">
        <input type="hidden" name="gt_ls_leadsunlocked_no" value="0">
        <input type="hidden" name="leadsunlocked_no">
        <input type="hidden" name="sort" value="unlocked_acnd">
        <input type="hidden" name="pagination" value="10">
    </form>
</div>

<script type="text/javascript">

    // var elm = document.getElementById('textarea');

    var PATH = "{{config('settings.APPLICATION-DOMAIN')}}/public/";
    
    var tdlExtensions = {};
    
    var pushExtension = function(ext) {
        // console.log(typeof tdlExtensions.ext, tdlExtensions.ext === undefined, tdlExtensions.ext === null, tdlExtensions.ext === undefined || tdlExtensions.ext === null);
        if(tdlExtensions.ext === undefined || tdlExtensions.ext === null) {
            tdlExtensions[ext] = 1;
        }
    }
    
    var popExtension = function(ext) {
        if(typeof tdlExtensions.ext !== undefined && typeof tdlExtensions.ext !== null) {
            delete tdlExtensions[ext];
        }
    }

    $(document).on('keyup', '#registered-date1-m, #registered-date1-d, #registered-date2-m, #registered-date2-d, #expired-date1-m, #expired-date1-d, #expired-date2-m, #expired-date2-d', function() {
        this.value = this.value.slice(-2).translateToDate();
    });
    
    $(document).ready(function(){

        $('#postSearchDataForm input[type=radio]').on('change', function() {
            var mode = $(this).val();
            if(mode == 'newly_registered') {
                $('#created_date_div').show();
                $('#expired_date_div').hide();
                $('#domains_expired_date').val('');
                $('#domains_expired_date2').val('');
            } else if(mode == 'getting_expired') {
                $('#expired_date_div').show();
                $('#created_date_div').hide();
                $('#registered_date').val('');
                $('#registered_date2').val('');
            }
        });
    
        $('.selectOption').each(function(){
            var thisVar = $(this), numberOfOptions = $(this).children('option').length;
            thisVar.addClass('select-hidden'); 
            thisVar.wrap('<div class="select"></div>');
            thisVar.after('<div class="select-styled"><div class="tagContainer"><div class="tagArea"><div class="tagAreaInner"></div></div></div><div class="tglBtn"></div></div>');
            var styledSelect = thisVar.next('div.select-styled');
            var list = $('<ul />', {
                'class': 'select-options'
            }).insertAfter(styledSelect);
            for (var i = 0; i < numberOfOptions; i++) {
                $('<li />', {
                    text: thisVar.children('option').eq(i).text(),
                    rel: thisVar.children('option').eq(i).val()
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
                $('div.select-styled .tagArea .tagAreaInner').append("<p><span class='tagTxt'>" + $(this).text() + "</span><span class='cl'>x</span></p>");
                $(this).hide();
                thisVar.val($(this).attr('rel'));
                pushExtension($(this).text());
                //console.log(thisVar.val());
                var tagAreaWidth = 0;
                $(".tagAreaInner p").each(function(){
                    tagAreaWidth += $(this).outerWidth()+3;
                });
                $(".tagAreaInner").css("width", tagAreaWidth + "px");
                $(".select-styled p .cl").click(function(e){
                    e.stopPropagation();
                    var a = $(this).prev(".tagTxt").text();
                    $(this).parent("p").remove();
                    popExtension(a);
                    $('ul.select-options li').each(function(){
                        if($(this).text() == a){
                            $(this).show();
                        }
                    });
                });
            });
            $(document).click(function(e) {
                styledSelect.removeClass('active');
                list.fadeOut(200);
            });
            var sc = 0;
            $('body').on('mousewheel', function(e) {
                if($(e.target).closest(".select-styled").hasClass("select-styled")){
                    return false;
                }
            });
            $('.tagAreaInner').on('mousewheel', function(event) {
                optionScrollWidth = $(".tagAreaInner").width() - $('.tagArea').width();
                if(event.deltaY == -1) {
                    if(sc > optionScrollWidth) {
                    sc = optionScrollWidth;
                }
                    sc += 10;
                } 
                else if(event.deltaY == 1) {
                    if(sc < 0) {
                    sc = 0;
                }
                    sc -= 10;
                }
                $(".tagArea").scrollLeft(sc);        
            });
        });
    
        $('#searchDomains').click(function(e) {
            e.preventDefault();
            $('#loader-icon').show();
            var tldOptionsStr = '';
            Object.keys(tdlExtensions).map(function(key, index) {
                if(tldOptionsStr != '') {
                    tldOptionsStr += ','+key;
                } else {
                    tldOptionsStr += key;
                }
            });
            $('#domain_ext').val(tldOptionsStr);

            // Call the ajax function to cache search results in mysql
            $.ajax({
                url  : "{{config('settings.DL-API')}}/api/search_api"+"?"+$('#postSearchDataForm').serialize(),
                type : "get",
                beforeSend : function() {
                    console.log(" going to send form ");
                },
                success : function(data) {
                    console.log("data return : ", data)
                }, error : function(er) {
                    console.log("err return : ", er)
                }, complete : function() {
                    console.log(" completed ... ")
                    // $('#loader-icon').hide();
                    $('#postSearchDataForm').submit();
                }
            });
        });

        $(window).bind("pageshow", function(event) {
            $("#loader-icon").hide();
        });
    
        var cross = $(".selectBox .select-styled p span");
        $(".cl").click(function(event){
            event.stopPropagation();
            // console.log(event);
            event.stopPropagation();
            $(this).closest("p").css("background","#000");
        });
    
        $("#registered_date, #registered_date2, #expired_date, #expired_date2").datepicker({
            dateFormat: "yy-mm-dd",
            showOn: "button",
            buttonImage: "/public/images/icon_calendar.png",
            buttonImageOnly: true,
        });
    
        $(".dateHidden").change(function(){
            var dateSelect = $(this).val().split("-");
            $(this).nextAll(".year").val(dateSelect[0]);
            $(this).nextAll(".month").val(dateSelect[1]);
            $(this).nextAll(".day").val(dateSelect[2]);
        });

        $(".date").click(function(){
            $(this).find(".ui-datepicker-trigger").trigger('click');
        });
    });
</script>
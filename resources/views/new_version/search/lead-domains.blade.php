<!DOCTYPE html>
<html lang="en">

{{-- Include common user panel head used for all dashboard components 
    * Input title (Optional)
    * Brings in css and js files
    --}}
@include('new_version.section.user_panel_head', ['title' => 'Domainleads | Search Results'])
<link rel="stylesheet" href="{{config('settings.APPLICATION-DOMAIN')}}/public/css/new_design/laravel-pagination.css">
<body>

    {{-- Loader icon in the platform --}}
    @include('new_version.shared.loader')`

    <div class="container">

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
                            <strong>{{isset($alldomain) ? $alldomain->total() : 0}}</strong> domains found against your search!
                        </div>
                        <div class="dataTableHeaderRight">
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
                    
                    {{-- Include common search results table : this is reused in multiple places across different user profiles and admin --}}        
                    @include('new_version.shared.lead-domains-table', [
                        'alldomain' =>  isset($alldomain) ? $alldomain : [],
                        'email'     =>  isset($email) ? $email : '',
                        'user'      =>  $user,
                        'users_array'   => $users_array,
                        'restricted'    => $restricted
                    ])
                </div>

                @if($alldomain->count() > 0)
                    <div class="paginate">
                        {{$alldomain->appends(['pagination' => Request::has('pagination') ? Request::get('pagination') : 10])->links()}}
                    </div>
                @endif
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

        var req_pagination = "{{Request::has('pagination') ? Request::get('pagination') : 10}}";
        
        // var submitFormCustom = function() {
        //     $('#loader-icon').show();
        //     $('#postAdvancedSearchDataForm').submit();
        // }

        function getUrlBase()
        {
            var sPageURL = window.location.href;
            var sURLVariables = sPageURL.split('?');
            console.log(sURLVariables);
            return sURLVariables[0];
            // for (var i = 0; i < sURLVariables.length; i++) 
            // {
            //     var sParameterName = sURLVariables[i].split('=');
            //     if (sParameterName[0] == sParam) 
            //     {
            //         return sParameterName[1];
            //     }
            // }
            // return "";
        }

        var repaginate = function (p) {
            var urlBase = getUrlBase();
            urlBase += '?'+'page=1&pagination='+p;
            console.log(urlBase);
            window.location.replace(urlBase);
        }
        
        $(document).ready(function(){

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
                    $('#pagination').val(thisVal.val());
                }

                // Used for advanced-search-box
                if(thisInstance.data('stopsubmit') === undefined) {
                    // console.log('urlparam : ', urlParam, 'full : ', urlFull);
                    console.log(req_pagination);
                    repaginate(req_pagination);
                }
                // console.log('afsdckhjtaykj kjfy', thisInstance.data('stopsubmit'));
                // submitFormCustom();
            });

            $(document).click(function() {
                styledSelect.removeClass('active');
                list.fadeOut(200);
            });
        });

        function unlockFromLeads(reg_em , key) {
            var id = '{{$user->id}}';
            var domain_name = $('#domain_name_'+key).data('domainname');
            $.ajax({
                type : 'POST',
                url  : "{{route('unlockFromLeads')}}",
                data : { _token:'{{csrf_token()}}',
                    registrant_email:reg_em ,
                    user_id:id, 
                    domain_name: domain_name,
                    key: key
                }, beforeSend: function() {
                    // Show loader
                    $('#loader-icon').show();
                }, success :function(response) {
                    $('#loader-icon').hide();
                    // console.log(response);
                    if(response.status) {
                        $('#tr_'+key).empty().append(response.view);
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
                    // console.error(er);
                    $('#loader-icon').hide();
                    if(er.status == 401) {
                        window.location.replace("{{route('loginPage')}}");
                    }
                }
            });
        }
    </script>
</body>
</html>
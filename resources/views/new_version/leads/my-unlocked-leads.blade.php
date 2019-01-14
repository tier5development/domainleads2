<!DOCTYPE html>
<html lang="en">

{{-- Include common user panel head used for all dashboard components 
    * Input title (Optional)
    * Brings in css and js files
    --}}
@include('section.user_panel_head', ['title' => 'Domainleads | Unlocked Leads'])

<body>

    {{-- Loader icon in the platform --}}
    @include('new_version.shared.loader')

    <div class="container">

        {{-- Include common user panel header used for all dashboard components 
            * Input user object (compulsary)
            --}}
        @include('section.user_panel_header', ['user' => $user])
        
        <section class="mainBody">
            <div class="leftPanel leadUnlock">

                <div class="dataTableArea">
                    <div class="dataTableHeader">
                        <div class="unlockInfo">
                            <strong>{{isset($totalDomains) ? $totalDomains : 0}}</strong> domains found against your search
                        </div>
                        <div class="dataTableHeaderRight">
                            {{-- <button class="refineSearch">
                                <div class="icon"><img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/Icon_refine_search.png" alt=""></div>
                                <p>Refine your search</p>
                            </button> --}}
                            <div class="pageViewControl">
                            <label for="">SHOW : </label>
                                <div class="selectBox">
                                    <select id="slect-pagination-box" class="selectpage">
                                        <option {{Request::has('pagination') && Request::get('pagination') == 10 ? 'selected' : ''}} value="10">10 per page</option>
                                        <option {{Request::has('pagination') && Request::get('pagination') == 20 ? 'selected' : ''}} value="20">20 per page</option>
                                        <option {{Request::has('pagination') && Request::get('pagination') == 50 ? 'selected' : ''}} value="50">50 per page</option>
                                        <option {{Request::has('pagination') && Request::get('pagination') == 100 ? 'selected' : ''}} value="100">100 per page</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- All leads unlocked goes here --}}
                    <div class="datatable" id="search-result-container">
                        <table cellpadding="0" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>DOMAIN<br>NAME</th>
                                    <th>REGISTRANT<br>NAME, EMAIL</th>
                                    <th>REGISTRANT<br>PHONE</th>
                                    <th>CREATED<br>DATE</th>
                                    <th>EXPIRY<br>DATE</th>
                                    <th>REGISTRANT<br>COMPANY</th>
                                    <th>DATE OF UNLOCK</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($leads as $key => $each)
                                    <tr>
                                        <td>
                                            <p data-restrict="1" id="domain_name_{{$key}}"> {{$each->domain_name}}</p>
                                        </td>
                                        <td>
                                            <p class="name">
                                                {{$each->registrant_fname}}&nbsp;{{$each->registrant_lname}}
                                            </p>
                                            <p class="email">
                                                {{$each->registrant_email}}
                                            </p>
                                        </td>
                                        <td>
                                            @if($each->lead)
                                                @php 
                                                    $phone = $each->lead->registrant_phone; 
                                                    $phoneType = $each->lead->valid_phone ? $each->lead->valid_phone->number_type : null;
                                                @endphp
                                                <p class="phone">
                                                    @if(strtolower(trim($phoneType)) == 'cell number')
                                                        <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_mobile.png" alt="">
                                                    @elseif(strtolower(trim($phoneType)) == 'landline')
                                                        <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_land_phone.png" alt="">
                                                    @endif
                                                    <span>{{$phone}}</span>
                                                </p>
                                            @else

                                            @endif
                                        </td>
                                        <td>
                                            <p><span>{{$each->domains_create_date}}</span></p>
                                            
                                        </td>
                                        <td>
                                            <p><span>{{$each->expiry_date}}</span></p>
                                        </td>
                                        <td>
                                            <p><span>{{$each->registrant_company}}</span></p>
                                        </td>
                                        <td>
                                            <p><span>{{$each->created_at}}</span></p>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($leads->count() > 0)
                            <div class="paginate">
                                {{$leads->appends(['date' => Request::has('date') ? Request::get('date') : null,
                                'perpage' => Request::has('perpage') ? Request::get('perpage') : 20])->links()}}
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
    <script>

        var req_pagination = "{{Request::has('pagination') ? Request::get('pagination') : 10}}";
        
        var submitFormCustom = function() {
            $('#loader-icon').show();
            $('#postSearchDataForm').submit();
        }
        
        $(document).ready(function(){
            $(".refineSearch").click(function(){
                $(".filterPopup").fadeIn();
            });
            $(".closeFilterPopup").click(function(){
                $(".filterPopup").fadeOut();
            });

            setTimeout(() => {
                $('#loader-icon').hide();
                console.log('I am executing');
            }, 300);

            $('#slect-pagination-box').change(function(e) {
                console.log(e);
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
                $('#pagination').val(thisVal.val());
                submitFormCustom();
            });

            $(document).click(function() {
                styledSelect.removeClass('active');
                list.fadeOut(200);
            });
        });

        var canvas = document.getElementById('crart');
        var context = canvas.getContext('2d');
        var al=0;
        var av = 0;
        var start=4.72;
        var cw=context.canvas.width/2;
        var ch=context.canvas.height/2;
        var diff;

        var targetVal = 50;
        var currentVal = 40;

        var radius = 60;
        var chartVal = (currentVal / targetVal) * 100;

        var gradient = context.createLinearGradient(0, 0, 0, 140);
            gradient.addColorStop(0, '#48e4b3');
            gradient.addColorStop(0.5, '#3cbec1');
            gradient.addColorStop(1, '#48e4b3');

        function progressBar(){
            diff=(al/100)*Math.PI*2;
            context.clearRect(0,0,400,400);
            context.beginPath();
            context.arc(cw,ch,radius,0,2*Math.PI,false);
            context.fillStyle='#FFF';
            context.fill();
            context.strokeStyle='#f6f6f6';
            context.stroke();
            context.fillStyle='#000';
            context.strokeStyle= gradient;
            context.textAlign='center';
            context.lineWidth=10;
            context.font = '21px "Avenir LT Std 95 Black"';
            context.fillStyle = '#333';
            context.beginPath();
            context.arc(cw,ch,radius,start,diff+start,false);
            context.stroke();
            context.lineCap = 'round';
            context.fillText(av+"/50" ,65, 75 );
            if(al>=chartVal){
                clearTimeout(bar);
            }
                al++;
                av++;
            if(av>=currentVal){
                av = currentVal;
            }
                
        }
        var bar = setInterval(progressBar, 10);
    </script>
</body>
</html>
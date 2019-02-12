<!DOCTYPE html>
<html lang="en">
    <link rel="stylesheet" href="{{config('settings.APPLICATION-DOMAIN')}}/public/css/new_design/jquery.mCustomScrollbar.min.css">
    @include('new_version.section.user_panel_head', ['title' => 'Cancel Membership'])
<body>
    <div class="container noWidth">
        <div class="rightPanTgl">   
            <span></span>
            <span></span>
            <span></span>
        </div>
        @include('new_version.section.user_panel_header', ['user' => $user])
        @include('new_version.shared.loader')
        <section class="mainBody">
            
            @include('new_version.shared.right-panel')

            <div class="leftPanel leadUnlock">
                @include('new_version.shared.profile-panel-header')
 
                <div id="membership" class="eachItem cancellationMemberShip">
                    <h2>Cancellation of your Domain Leads membership</h2>
                    <p>We are so sad to see you go.We would like you to tell us the reason of cancellation of your membership.This would help us to serve the community better experience.</p>
                    <form action="{{route('cancelMembershipPost')}}" method="POST" id="cancel-memership-form">
                        <div class="formRow">
                            <div class="fieldWrap">
                                <label for="">select your reason</label>
                                <div class="largeSelectBox">
                                    <select data-stopsubmit='1' class="selectpage" id="sort" name="sort">
                                        @php $limit = count(getCancelMembershipReasons())-1; @endphp
                                        <option value="-1">-- Select Options --</option>
                                        @foreach (getCancelMembershipReasons() as $key => $item)
                                            <option value="{{$key}}">{{$item}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="fieldWrap" id="other-reason" style="display: none;">
                                <label for="">enter your custom reason</label>
                                <input type="text" class="form-control" name="" id="">
                                <div id="fname_err" class="errorMsg"></div>
                            </div>
                            <div class="fieldWrap">
                                <button id="advanced-search-btn" type="submit" class="orangeBtn">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Include footer --}}
            @include('new_version.shared.dashboard-footer-mobile')
        </section>

        {{-- Include footer --}}
        @include('new_version.shared.dashboard-footer')
    </div>

    <div class="loader" style="display: none;">
        <div class="loaderContainer">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>


    
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/right-panel.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/right-panel.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/custom.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/jquery.mCustomScrollbar.concat.min.js"></script>


    <script type="text/javascript">
    $(document).ready(function(){
    //responsive menu

    $('.menu-button').click(function(){
        $('.bottomRight').addClass('pull');
    });
    $('.menuClose').click(function(){
        $('.bottomRight').removeClass('pull');
    });

    // $('.fancybox').fancybox();
    
    });




    // sidepopup
    function PopUp(hideOrshow) {
    if (hideOrshow == 'hide') document.getElementById('stickyBoxWrap').style.display = "none";
    else document.getElementById('stickyBoxWrap').removeAttribute('style');
    }

    $(document).ready(function() {
        // for custom dropdown
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
            };

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
                // $(document).trigger('click');
                console.log('clicked');
                var textValue = $(this).text();
                thisVal.val($(this).attr('rel'));
                list.fadeOut(200, 0, function() {
                    styledSelect.text(textValue).removeClass('active');
                });
            });
            $(document).click(function() {
                styledSelect.removeClass('active');
                list.fadeOut(200);
            });
        });
        
    });

    
    </script>
</body>
</html>
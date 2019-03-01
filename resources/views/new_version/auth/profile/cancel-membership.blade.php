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

            <div class="dont-show-all" id="dont-show-all" style="display: none;">
                <div class="innerContent clearfix">
                    <div class="container customCont cancelDomain">
                        <div class="col-sm-8 innerContentWrap">
                            <div class="col-sm-12 createForm">
                                <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_done.png" alt="domain cancel">
                                <h2>Cancellation of your Domain Leads membership</h2>
                                <p>Hey {{$user->name}}, Thanks for being a Standard Member of Domain Leads so far. Your account has been suspended and you can subscribe for any plan in future using your same login credential.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="show-all" id="show-all">
                @include('new_version.shared.right-panel')
                
                <div class="leftPanel leadUnlock">

                    @include('new_version.shared.profile-panel-header')
                    
                    @include('new_version.shared.messages')

                    <div id="membership" class="eachItem cancellationMemberShip">
                        <h2>Cancellation of your Domain Leads membership</h2>
                        <p>We are so sad to see you go. We would like you to tell us the reason of cancellation of your membership. This would help us to serve the community with better experience.</p>
                        
                        <div class="formRow">
                            <div class="fieldWrap">
                                <label for="">select your reason</label>
                                <div class="largeSelectBox">
                                    <select data-stopsubmit='1' class="selectpage" id="drop-down-reasons" name="drop-down-reasons">
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
                                <input type="text" class="form-control" name="reason-specified" id="reason-specified">
                                <div id="fname_err" class="errorMsg"></div>
                            </div>
                            <div class="fieldWrap">
                                <button id="cancel-membership-btn" type="button" class="orangeBtn">Submit</button>
                            </div>
                        </div>
                        
                    </div>
                </div>
    
                {{-- Include footer --}}
                @include('new_version.shared.dashboard-footer-mobile')
            </div>
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

            $('#cancel-membership-btn').click(function(e) {
                e.preventDefault();
                var value   =   $("#drop-down-reasons option:selected").val();
                var reason  =   $("#drop-down-reasons option:selected").text();
                var reasonOther = $("#reason-specified").val().trim();
                // console.log(value, reason, reasonOther);
                // return false;
                if( (reason.toLowerCase() == 'others' && reasonOther.length == 0) || value < 0 ) {
                    $('#ajax-msg-box').removeClass('success').removeClass('error').addClass('error').show().find('.message-body-ajax').text('Please help us to serve the community with better experience in the future and give us your feedback.');
                    return false;
                }
                
                return false;
                if(reasonOther.length > 0) {
                    reason = reasonOther;
                }
                $.ajax({
                    url :   "{{route('cancelMembershipPost')}}",
                    type:   "post",
                    data:   {_token: "{{csrf_token()}}", reason: reason},
                    beforeSend : function() {
                        $('#loader-icon').show();
                    },
                    success : function(resp) {
                        $('#loader-icon').hide();
                        if(resp.status == true) {
                            $('#show-all').hide();
                            $('#dont-show-all').show();
                            setTimeout(function() {
                                window.location.replace("{{route('loginPage')}}");
                            }, 3000);
                        } else {
                            $('#dont-show-all').hide();
                            $('#show-all').show();
                            $('#ajax-msg-box').removeClass('success').removeClass('error').addClass('error').show().find('.message-body-ajax').text(resp.message);
                        }
                    }, error : function(err) {
                        $('#loader-icon').hide();
                        console.error(err);
                        if(err.status == 401) {
                            window.location.replace("{{route('loginPage')}}");
                        }
                    }
                });
            });

            // for custom dropdown
            $('.selectpage').each(function() {
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
                        if(textValue.toLowerCase() == "others") {
                            $('#other-reason').show();
                        } else {
                            $("#reason-specified").val('');
                            $('#other-reason').hide();
                        }
                    });
                });
                $(document).click(function() {
                    styledSelect.removeClass('active');
                    list.fadeOut(200);
                });
            });
        });

        // sidepopup
        function PopUp(hideOrshow) {
            if (hideOrshow == 'hide') document.getElementById('stickyBoxWrap').style.display = "none";
            else document.getElementById('stickyBoxWrap').removeAttribute('style');
        }
    </script>
</body>
</html>
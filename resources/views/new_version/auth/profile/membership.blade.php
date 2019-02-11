<!DOCTYPE html>
<html lang="en">
    @include('new_version.section.user_panel_head', ['title' => 'Membership'])
    
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
                <div id="ajax-msg-box" class="alertBox" style="display: none;">
                    <p id="ajax-body" class="message-body-ajax"></p>
                    <span class="close"></span>
                </div>
                <br><br>

                <div id="membership" class="eachItem">
                    <h2>your membership plan</h2>
                    <p>Upgrade or downgrade your membership plan anytime.</p>
                    <div class="plans">
                        <div class="container">
                            <div class="eachPlanContainer clearfix">
                                @foreach (config('settings.PLAN.NAMEMAP') as $key=>$item)
                                    @php if($item == 2) continue; @endphp
                                    <div class="eachPlanOuter">
                                        <div class="eachPlan">
                                            <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/basic_plan.png">
                                            <h4>basic</h4>
                                            <ul class="features">
                                                <li>{{config('settings.PLAN.PUBLISHABLE.'.$item)[0] < 0 ? 'UNLIMITED' : config('settings.PLAN.PUBLISHABLE.'.$item)[0]}} leads a day</li>
                                                <li>location filters</li>
                                                <li>keywords filters</li>
                                                <li>TLD filters</li>
                                                <li>lead exports</li>
                                            </ul>
                                            <h3>${{config('settings.PLAN.PUBLISHABLE.'.$item)[1]}}</h3>
                                            <span>Billed monthly, no set up fee.</span>
                                            
                                            @if($user->user_type == config('settings.PLAN.L').$item)
                                                <a href="javascript:void(0)" id="plan-{{$item}}" data-plan='{{$item}}' class="button planBtn greyButton">current plan</a>
                                            @elseif($user->user_type > config('settings.PLAN.L').$item)
                                                <a href="javascript:void(0)" id="plan-{{$item}}" data-plan='{{$item}}' class="button planBtn gradiant-green">downgrade</a>
                                            @elseif($user->user_type < config('settings.PLAN.L').$item)                                                
                                                <a href="javascript:void(0)" id="plan-{{$item}}" data-plan='{{$item}}' class="button planBtn gradiant-orange">get started</a>
                                            @endif

                                            <button class="viewMore1">View more</button>
                                            <ul class="viewMorePanel1">
                                                <li>{{config('settings.PLAN.PUBLISHABLE.'.$item)[0] < 0 ? 'UNLIMITED' : config('settings.PLAN.PUBLISHABLE.'.$item)[0]}} leads a day,</li>
                                                <li>location filters,</li>
                                                <li>keywords filters,</li>
                                                <li>TLD filters,</li>
                                                <li>lead exports</li>
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <p>I want to <a href="#" class="cancelMembership">cancel my membership</a> now</p>
                </div>
            </div>

            {{-- Include footer --}}
            @include('new_version.shared.dashboard-footer-mobile')
        </section>

       {{-- Include footer --}}
       @include('new_version.shared.dashboard-footer')
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/right-panel.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/custom.js"></script>
    
    <!-- for dasboard page tab -->
    <script src="https://checkout.stripe.com/checkout.js"></script>
    <script type="text/javascript">
        var username            =   "{{$user->name}}";
        var email               =   "{{$user->email}}";
        var publicKey           =   "{{$stripeDetails->public_key}}";
        var userStoredImagePath =   "{{$user->image_path}}";
        var planToUpgrade       =   null;
        var currentPlan         =   "{{$user->user_type}}";

        
        $(window).on('popstate', function() {
            handler.close();
        });

        var handler = StripeCheckout.configure({
            key:    publicKey,
            image:  userStoredImagePath,
            locale: 'auto',
            token: function(token) {
                $.ajax({
                    url: "{{route('updateCardDetailsAndSubscribe')}}",
                    data: {
                        stripe_token    :   token.id,
                        _token          :   "{{csrf_token()}}",
                        plan            :   planToUpgrade
                    },
                    type :"post",
                    beforeSend : function() {
                        $('#loader-icon').show();
                    },  success: function(resp) {
                        console.log(resp);
                        if(resp.status) {
                            $('#loader-icon').hide();
                            if(resp.processComplete) {
                                $('#ajax-msg-box').removeClass('success').removeClass('error').addClass('success').show().find('.message-body-ajax').text(resp.message);
                                adjustNewButtons(resp.newPlan);
                            } else {
                                $('#ajax-msg-box').removeClass('success').removeClass('error').addClass('error').show().find('.message-body-ajax').text(resp.message);
                            }
                        } else {
                            $('#ajax-msg-box').removeClass('success').removeClass('error').addClass('error').show().find('.message-body-ajax').text(resp.message);
                        }
                    }, error : function(err) {
                        $('#loader-icon').hide();
                        if(err.status == 401) {
                            window.location.replace("{{route('loginPage')}}");
                        } else if(err.status == 500) {
                            $('#ajax-msg-box').removeClass('success').removeClass('error').addClass('error').show().find('.message-body-ajax').text('Error occoured while updating card details.');
                        }
                        console.log('came here 1 : ', err.status);
                    }
                });
            }
        });

        var openStripeForm = function() {
            handler.open({
                name        : username,
                description : 'Update card and pay for subscription',
                label       : 'Update Card Details',
                email       : email
            });
        }
    </script>

    <script>
        
        var adjustNewButtons = function(newPlan) {
            newPlan = parseInt(newPlan);
            // currentPlan holds the current plan the user is in.
            $(".planBtn").removeClass("gradiant-green").removeClass("gradiant-orange").removeClass("greyButton");
            currentPlan = newPlan;
            for(var i = 1; i <= parseInt("{{count(config('settings.PLAN.NAMEMAP'))}}"); i++) {
                var id = "#plan-"+i;
                if(i < newPlan) {
                    $(id).addClass("gradiant-green");
                    $(id).text('downgrade');
                } else if(i == newPlan) {
                    $(id).addClass("greyButton");
                    $(id).text('current plan');
                } else {
                    $(id).addClass("gradiant-orange");
                    $(id).text('get started');
                }
            }
        }
        

        var changePlan = function(t) {
            console.log('in func');
            // If user has card info saved charge himright away, else show him stripe form.
            planToUpgrade = $(t).data('plan');
            if(planToUpgrade == currentPlan) {
                return false;
            }
            $.ajax({
                type    :   "post",
                url     :   "{{route('upgradeOrDowngradePlan')}}",
                data    :   {_token: "{{csrf_token()}}", plan: planToUpgrade},
                beforeSend: function() {
                    console.log('before load');
                    $('#loader-icon').show();
                }, success: function(resp) {
                    $('#loader-icon').hide();
                    console.log(resp);
                    if(resp.status) {
                        if(resp.processComplete) {
                            adjustNewButtons(resp.newPlan);
                            $('#ajax-msg-box').removeClass('success').removeClass('error').addClass('success').show().find('.message-body-ajax').text(resp.message);
                        } else {
                            openStripeForm();
                        }
                    } else {
                        openStripeForm();
                    }
                }, error: function(er) {
                    $('#loader-icon').hide();
                    if(er.status == 401) {
                        window.location.replace("{{route('loginPage')}}");
                    } else if(er.status == 500) {
                        $('#ajax-msg-box').removeClass('success').removeClass('error').addClass('error').show().find('.message-body-ajax').text('Error occoured while subscriuption.');
                    }
                    console.error('came here 2 : ', er.status);
                }
            });
        }

        // $(document).ready(function() {
        //     // setTimeout(function() {
        //     //     openStripeForm();
        //     // }, 3000);
        //     Cookies.remove('username'); 
        //     // alert(Cookies.get('username')); 
        // });

        $(document).ready(function() {
            $('.close').click(function() {
                $(this).parent().removeClass('error').removeClass('success').hide();
                $(".message-body-ajax").text('');
            });

            $(".planBtn").on('click', function() {
                changePlan(this);
            });
        });

        function openItem(itemName) {
            var i;
            var x = document.getElementsByClassName("eachItem");
            for (i = 0; i < x.length; i++) {
                x[i].style.display = "none";  
            }
            document.getElementById(itemName).style.display = "block";  
        }
    </script>
</body>
</html>
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
                    
                <div id="profile_info" class="eachItem" style="display:none">
                    <h2>Edit your profile information layout will be here.</h2>
                    <p>some content</p>
                </div>
                
                <div id="change _password" class="eachItem" style="display:none">
                    <h2>Change your password layout will be here.</h2>
                    <p>some text</p> 
                </div>
                
                <div id="payment_info" class="eachItem" style="display:none">
                    <h2>hfghfhfhfhf</h2>
                    <p>Tokyo is the capital of Japan.</p>
                </div>
                
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
                                                <button class="greyButton">current plan</button>
                                            @elseif($user->user_type > config('settings.PLAN.L').$item)
                                                <a href="javascript:void(0)" class="button gradiant-green">downgrade</a>
                                            @elseif($user->user_type < config('settings.PLAN.L').$item)                                                
                                                <a href="javascript:void(0)" data-plan='{{$key}}' onclick="upgradePlan(this)" class="button gradiant-orange">get started</a>
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

        $(window).on('popstate', function() {
            handler.close();
        });

        var handler = StripeCheckout.configure({
            key: publicKey,
            image: userStoredImagePath,
            locale: 'auto',
            token: function(token) {
                $.ajax({
                    url: "{{route('updateCardDetailsAndSubscribe')}}",
                    data: {
                        stripe_token    :   token.id,
                        _token  :   "{{csrf_token()}}"
                    },
                    type :"post",
                    beforeSend : function() {
                        $('#loader-icon').show();
                    }, success: function(resp) {
                        console.log(resp);
                        if(resp.status) {
                            $('#loader-icon').hide();
                            var last4 = response.card;
                            var exp_month   = response.card['exp_month'];
                            var exp_year    = response.card['exp_year'];
                        } else {
                            $('#loader-icon').hide();
                        }
                    }, error : function(err) {
                        $('#loader-icon').hide();
                        if(err.status == 401) {
                            window.location.replace("{{route('loginPage')}}");
                        }
                        console.log(err.status);
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

        

        var upgradePlan = function(t) {
            console.log('in func');
            // If user has card info saved charge himright away, else show him stripe form.
            var plan = $(t).data('plan');
            $.ajax({
                type    :   "post",
                url     :   "{{route('upgradePlan')}}",
                data    :   {_token: "{{csrf_token()}}", plan: plan},
                beforeSend: function() {
                    console.log('before load');
                    $('#loader-icon').show();
                }, success: function(resp) {
                    $('#loader-icon').hide();
                    console.log(resp);
                    if(resp.status) {
                        // success
                        if(resp.cardUpdated) {
                            alert('Subscription successful');
                        } else {
                            console.log('modal open');
                            openStripeForm();
                        }
                    } else {
                        // card is not updated so open stripe modal
                    }
                }, error: function(er) {
                    $('#loader-icon').hide();
                    if(er.status == 401) {
                        window.location.replace("{{route('loginPage')}}");
                    }
                }
            });
            console.log(plan);
        }

        $(document).ready(function() {
            // setTimeout(function() {
            //     openStripeForm();
            // }, 3000);
            Cookies.remove('username'); 
            // alert(Cookies.get('username')); 
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
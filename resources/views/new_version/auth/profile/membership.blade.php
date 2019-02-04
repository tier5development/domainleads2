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
                            <div class="eachPlanOuter">
                                <div class="eachPlan">
                                <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/basic_plan.png">
                                <h4>basic</h4>
                                <ul class="features">
                                    <li>50 leads a day</li>
                                    <li>location filters</li>
                                    <li>keywords filters</li>
                                    <li>TLD filters</li>
                                    <li>lead exports</li>
                                </ul>
                                <h3>$47</h3>
                                <span>Billed monthly, no set up fee.</span>
                                <button class="greyButton">current plan</button>
                                <!-- <a href="#" class="button gradiant-orange">get started</a> -->
                                <button class="viewMore1">View more</button>
                                <ul class="viewMorePanel1">
                                    <li>50 leads a day,</li>
                                    <li> location filters,</li>
                                    <li> keywords filters,</li>
                                    <li> TLD filters,</li>
                                    <li> lead exports</li>
                                </ul>
                                </div>
                            </div>
                            <div class="eachPlanOuter">
                                <div class="eachPlan">
                                <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/pro_plan.png">
                                <h4>pro</h4>
                                <ul class="features">
                                    <li>50 leads a day</li>
                                    <li>location filters</li>
                                    <li>keywords filters</li>
                                    <li>TLD filters</li>
                                    <li>lead exports</li>
                                </ul>
                                <h3>$97</h3>
                                <span>Billed monthly, no set up fee.</span>
                                <a href="#" class="button gradiant-orange">get started</a>
                                <button class="viewMore2">View more</button>
                                <ul class="viewMorePanel2">
                                    <li>50 leads a day,</li>
                                    <li> location filters,</li>
                                    <li> keywords filters,</li>
                                    <li> TLD filters,</li>
                                    <li> lead exports</li>
                                </ul>
                                </div>
                            </div>
                            <div class="eachPlanOuter">
                                <div class="eachPlan">
                                    <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/agency_plan.png">
                                    <h4>agency</h4>
                                    <ul class="features">
                                        <li>50 leads a day</li>
                                        <li>location filters</li>
                                        <li>keywords filters</li>
                                        <li>TLD filters</li>
                                        <li>lead exports</li>
                                    </ul>
                                    <h3>$197</h3>
                                    <span>Billed monthly, no set up fee.</span>
                                    <a href="#" class="button gradiant-orange">get started</a>
                                    <button class="viewMore3">View more</button>
                                    <ul class="viewMorePanel3">
                                        <li>50 leads a day,</li>
                                        <li> location filters,</li>
                                        <li> keywords filters,</li>
                                        <li> TLD filters,</li>
                                        <li> lead exports</li>
                                    </ul>
                                </div>
                            </div>
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
    <script>

        $(document).ready(function() {
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
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
                @if($user->card_updated == 1 && count($cards) > 0 && isset($card['last4']) && isset($card['exp_month']) && isset($card['exp_year']))
                    <div id="payment_info" class="eachItem">
                        <h2>My Payment Information</h2>
                        <p>You have added below card information to your account.</p>
                        <div class="updateCardWrap">
                            <p>credit card information</p>
                            <div class="updateCard">
                                <div class="cardInfo cardNumber">
                                    <h4>card number</h4>
                                    <h3>xxxx xxxx xxxx {{$card['last4']}}</h3>
                                </div>
                                <div class="cardInfo expiryDate">
                                    <h4>expiry (mm/yy)</h4>
                                    <h3>{{$card['exp_month']}}/{{$card['exp_year']}}</h3>
                                </div>
                                <button id="update-card-btn" type="submit" class="orangeBtn">update card</button>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="innerContent clearfix">
                        <div class="container customCont cancelDomain">
                            <div class="col-sm-8 innerContentWrap">
                                <div class="col-sm-12 createForm">
                                    <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/sad-face.png" alt="domain cancel">
                                    <h2>Oops! we did not find any cards attached to your account.</h2>
                                    <p>Please provide your card information after clicking the update button.</p>
                                </div>
                                <button id="update-card-btn" type="submit" class="orangeBtn orangeBtnCentered">update card</button>
                            </div>
                        </div>
                    </div>
                @endif
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
                    url: "{{route('updateCardDetails')}}",
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
                            
                        } else {

                        }
                    }, error : function(err) {
                        if(err.status == 401) {
                            window.location.replace("{{route('loginPage')}}");
                        }
                        console.log(err.status);
                    }
                });
            }
        });

        $('#update-card-btn').on('click', function(e) {
            e.preventDefault();
            handler.open({
                name        : username,
                description : 'Stripe Card Update',
                label       : 'Update Card Details',
                email       : email
            });
        });
    </script>

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
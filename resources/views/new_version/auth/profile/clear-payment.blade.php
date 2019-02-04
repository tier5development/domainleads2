<!DOCTYPE html>
<html lang="en">
    @include('new_version.section.user_panel_head', ['title' => 'Pay Unpaid Invoice'])
    
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

                @include('new_version.shared.messages')

                <div id="embeded-card">
                    <div class="innerContent clearfix">
                        <div class="container customCont cancelDomain">
                            <div class="col-sm-8 innerContentWrap">
                                <div class="col-sm-12 createForm">
                                    <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/sad-face.png" alt="domain cancel">
                                    <h2>Please clear your pending payments.</h2>
                                    <p>You seem to have pending payments. Please clear your last pending payment of ${{config('settings.PLAN.PUBLISHABLE.'.$user->user_type)[1]}} to continue using domainleads.</p>
                                </div>
                                <form action="{{route('failedSubscriptionPost')}}" method="post" id="clear-last-payment-form">
                                    <button id="clear-pending-payments" class="orangeBtn orangeBtnCentered" type="submit">Clear Last Payment</button>
                                    <input type="hidden" name="stripe_token" id="stripe_token_field">
                                    {{csrf_field()}}
                                </form>
                            </div>
                        </div>
                    </div>
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
    <script src="https://checkout.stripe.com/checkout.js"></script>

    <script type="text/javascript">
    /**@argument
    in_1E6ch3CCfMEOiCCfIW0ZjHfn
    **/
        var username            =   "{{$user->name}}";
        var email               =   "{{$user->email}}";
        var publicKey           =   "{{$stripeDetails->public_key}}";
        var userStoredImagePath =   "{{$user->image_path}}";

        $(window).on('popstate', function() {
            handler.close();
        });

        $(window).bind("pageshow", function(event) {
            $("#loader-icon").hide();
        });

        var handler = StripeCheckout.configure({
            key: publicKey,
            image: userStoredImagePath,
            locale: 'auto',
            panelLabel: 'UPDATE',
            token: function(token) {
                $('#loader-icon').show();
                $('#stripe-token-field').val(token.id);
                $('#clear-last-payment-form').submit();
            }
        });

        $('#clear-pending-payments').on('click', function(e) {
            e.preventDefault();
            handler.open({
                name        : username,
                description : 'Stripe Card Update',
                label       : 'Update Card Details',
                email       : email
            });
        });
    </script>
</body>
</html>
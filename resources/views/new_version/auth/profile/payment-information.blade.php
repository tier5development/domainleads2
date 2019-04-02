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

                @include('new_version.shared.messages')

                <div id="embeded-card">
                    @if($user->card_updated == 1 && count($card) > 0 && isset($card['last4']) && isset($card['exp_month']) && isset($card['exp_year']))
                        @include('new_version.shared.embeded-card', ['user' => $user, 'card' => $card])
                    @else
                        @include('new_version.shared.ask-for-update-card')
                    @endif
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
            panelLabel: 'UPDATE',
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
                            $('#loader-icon').hide();
                            $('#embeded-card').empty().append(resp.html);
                            $('#ajax-msg-box').removeClass('success').removeClass('error').addClass('success').show().find('.message-body-ajax').text(resp.message);
                        } else {
                            $('#loader-icon').hide();
                            $('#ajax-msg-box').removeClass('success').removeClass('error').addClass('error').show().find('.message-body-ajax').text(resp.message);
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

        $(document).on('click', '#update-card-btn', function(e) {
            e.preventDefault();
            handler.open({
                name        : username,
                description : 'Stripe Card Update',
                label       : 'Update Card Details',
                email       : email
            });
        });

        // $('#update-card-btn').on('click', function(e) {
            
        // });
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
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

            <div class="leftPanel">
                @include('new_version.shared.profile-panel-header')

                {{-- Error or Success Message --}}
                
                
                <h2 class="editProfileHeading">Update your stripe keys below.</h2>
                <div class="profileFormArea">
                    <form action="{{route('updatePaymentKeysPost')}}" method="POST" id="updateStripeDetails">
                        @include('new_version.shared.messages')
                        <div class="formRow">
                            <div class="fieldWrap">
                                <input type="text" name="public_key" id="public_key" placeholder="strip public key" value="{{isset($stripeDetails) ? $stripeDetails->public_key : ''}}">
                                <div id="publickey_err" class="errorMsg"></div>
                            </div>
                        </div>
                        <div class="formRow">
                            <div class="fieldWrap">
                                <input type="text" name="private_key" id="private_key" placeholder="strip private key" value="{{isset($stripeDetails) ? $stripeDetails->private_key : ''}}">
                                <div id="privatekey_err" class="errorMsg"></div>
                            </div>
                        </div>
                        <div class="formRow">
                            <button type="submit" id="updateStripeDetailsBtn" class="orangeBtn">UPDATE</button>
                        </div>
                        {{csrf_field()}}
                    </form>
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

        var checkStripeForm = function() {
            // Check the form
            $('#publickey_err').text('');
            $('#publickey_err').parent().removeClass('error');
            $('#privatekey_err').text('');
            $('#privatekey_err').parent().removeClass('error');

            var flag = true;
            var stripePublicKey = $('#public_key').val();
            var stripePrivateKey = $('#private_key').val();
            
            if(stripePublicKey.trim().length == 0) {
                var msg = 'Your public key is invalid.'
                $('#publickey_err').text(msg);
                $('#publickey_err').parent().addClass('error');
                return {msg : msg, flag: false}
            } else if(stripePrivateKey.trim().length == 0) {
                var msg = 'Your private key is invalid.'
                $('#privatekey_err').text(msg);
                $('#privatekey_err').parent().addClass('error');
                return {msg : msg, flag: false}
            }
            return {msg : 'success', flag: true};
        }

        $(document).ready(function() {
            // Cookies.remove('username'); 
            // alert(Cookies.get('username')); 

            $('#updateStripeDetailsBtn').on('click', function(e) {
                e.preventDefault();
                var obj = checkStripeForm();
                if(obj.flag) {
                    $("#loader-icon").show();
                    $('#updateStripeDetails').submit();
                }
                console.log('error found so not submitting the form.')
            });
        });

        $(window).bind("pageshow", function(event) {
            $("#loader-icon").hide();
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
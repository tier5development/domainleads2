<!DOCTYPE html>
<html lang="en">
  @include('new_version.section.head')
  <body>

    @if(config('settings.ISLIVE') == true)
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f.fbq)f.fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '649270039158137');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=649270039158137&ev=PageView&noscript=1" />
    </noscript>
    <!-- End Facebook Pixel Code -->
    @endif

  <!-- banner -->
  <section class="banner">
    <!-- header -->
    @include('new_version.section.header_menu')

    @include('new_version.shared.loader')
    <!-- inner content -->
    <div class="innerContent signUp clearfix">
        <div class="container customCont">
            <div class="innerContentWrap">
                
                <form method="post" action="{{route('signupPost')}}" id="registration_form">
                    <input type="hidden" name="affiliate_id" id="affiliate_id">
                    <div class="leftSide">
                        <h2>Get an account to unlock leads</h2>
                        @include('new_version.shared.messages')
                        <h3>personal information</h3>
                        <div class="eachFieldWrap small">
                            <div class="fieldWrap small">    
                            <input type="text" name="full_name" id="full_name" class="form-control" placeholder="full name" value="{{old('full_name')}}">
                                <div id="fullname_err" class="errorMsg"></div>
                            </div>
                            <div class="fieldWrap small">    
                                <input type="email" name="email" class="form-control" placeholder="email" value="{{old('email')}}">
                                <div id="email_err" class="errorMsg"></div>
                            </div>
                            <div class="fieldWrap small">    
                                <input type="password" name="password" class="form-control" placeholder="password">
                                <div id="password_err" class="errorMsg"></div>
                            </div>
                            <div class="fieldWrap small">    
                                <input type="password" name="cpassword" class="form-control" placeholder="confirm password">
                                <div id="cpassword_err" class="errorMsg"></div>
                            </div>
                        </div>

                        <h3>Card Information</h3>
                        <div class="fieldWrap clearfix">
                            <div class="cardBackground clearfix"> <!-- vibrate -->
                                <label id="custom_card" > 
                                    <span>Card number</span>
                                    <div id="card-number-element" class="field"></div>
                                    <span class="brand"><i class="pf pf-credit-card" id="brand-icon"></i></span>
                                </label>
                                <label id="custom_cvc"> <!-- redline -->
                                    <span>CVC</span>
                                    <div id="card-cvc-element" class="field"></div>
                                </label>
                                <label id="custom_expiry">
                                    <span>Expiry date</span>
                                    <div id="card-expiry-element" class="field"></div>
                                </label>
                            </div>
                        </div>
                        <div class="fieldWrap spaceTop">
                            <button id="register-btn" type="submit" class="orangeBtn">get an account</button>
                            <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/cards.webp" alt="cards">
                            <span>Have an account?</span>
                            <a href="{{route('loginPage')}}">login now!</a>
                        </div> 
                    </div>

                    <div class="rightSide">
                        <h3>subcription plans</h3>
                        <div class="fieldWrap">
                            @foreach (config('settings.PLAN.NAMEMAP') as $key => $item)
                                @php 
                                    if($item[0] == config('settings.PLAN.NON-DISPLAYABLE')) continue; 
                                    $planAlias = config('settings.PLAN.PUBLISHABLE.'.$item[0])[3];
                                    $planNum = $item[0];
                                    $planAmount = config('settings.PLAN.PUBLISHABLE.'.$item[0])[1];
                                @endphp
                                <label class="radioItem">{{$planAlias}}
                                    <p>${{$planAmount}}/m</p>
                                    <input data-amt="{{$planAmount}}" class="radio-selector" type="radio" value="{{$planNum}}" checked="checked" name="plan">
                                    <span class="checkmark"></span>
                                </label>
                            @endforeach
                        </div>
                        <div class="cartWrap">
                            <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/cart.png" alt="cart">
                            <span>order total</span>
                            <h4 id="total-order-id"></h4>
                        </div>
                    </div>
                    <input type="hidden" name="stripe_token" value="" id="stripe_token_field">
                    {{csrf_field()}}
                </form>
            </div>
        </div>
    </div>
    @include('new_version.section.signin-footer')
  </section>

  
   <!-- footer -->
   @include('section.footer_menu')

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/bootstrap.min.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/custom.js"></script>
    <script src="https://js.stripe.com/v3/"></script>

    <script>
        var publicKey   =   "{{$stripeDetails->public_key}}";
        var stripe      =   Stripe(publicKey);
        var elements    =   stripe.elements();

        // var cardErrorNumberArray     = ['card_declined', 'expired_card', 'incorrect_number', 'invalid_number', 'invalid_card_type'];
        // var cardErrorCsvArray        = ['incorrect_cvc', 'invalid_csv'];
        // var cardErrorExpiryArray     = ['invalid_expiry_year', 'invalid_expiry_month'];// invalid_expiry_year_past

        var style = {
            base: {
                color: '#6c6c6c',
                fontSize: '15px',
                fontWeight:'700',
                fontFamily: 'Courier', //Avenir LT Std 55 Roman
                fontSmoothing: 'antialiased',
                letterSpacing: '2px',
                '::placeholder': {
                color: '#CFD7DF',
                },
            },
            empty: {
                fontSize: '12px',
            },
            invalid: {
                color: '#eb1c26',
                ':focus': {
                color: '#eb1c26',
                },
            },
        };

        var cardNumberElement = elements.create('cardNumber', {
            style: style
        });
        cardNumberElement.mount('#card-number-element');

        var cardExpiryElement = elements.create('cardExpiry', {
            style: style
        });
        cardExpiryElement.mount('#card-expiry-element');

        var cardCvcElement = elements.create('cardCvc', {
            style: style
        });
        cardCvcElement.mount('#card-cvc-element');

        function setOutcome(result) {
            if (result.error) {
                return {
                    status  : false,
                    id  : null,
                    message : result.error.message,
                    obj : result
                }
            } else if(result.token) {
                return {
                    status  : true,
                    id  : result.token.id,
                    message : 'Success',
                    obj : result
                }
            } else {
                return {
                    status  : null,
                    id  : null,
                    message : 'Error',
                    obj : result
                }
            }
        }

        var cardBrandToPfClass = {
            'visa': 'pf-visa',
            'mastercard': 'pf-mastercard',
            'amex': 'pf-american-express',
            'discover': 'pf-discover',
            'diners': 'pf-diners',
            'jcb': 'pf-jcb',
            'unknown': 'pf-credit-card',
        }
        
        function setBrandIcon(brand) {
            var brandIconElement = document.getElementById('brand-icon');
            var pfClass = 'pf-credit-card';
            if (brand in cardBrandToPfClass) {
                pfClass = cardBrandToPfClass[brand];
            }
            for (var i = brandIconElement.classList.length - 1; i >= 0; i--) {
                brandIconElement.classList.remove(brandIconElement.classList[i]);
            }
            brandIconElement.classList.add('pf');
            brandIconElement.classList.add(pfClass);
        }

        cardNumberElement.on('change', function(event) {
            // if (event.brand) {
            //     setBrandIcon(event.brand);
            // }
            if(typeof event.error != 'undefined') {
                showCardErr();
            } else {
                clearCardErr();
            }
            // setOutcome(event);
        });

        
        cardExpiryElement.on('change', function(event) {
            if(typeof event.error != 'undefined') {
                showExpiryErr();
            } else {
                clearExpiryErr();
            }
        });

        cardCvcElement.on('change', function(event) {
            if(typeof event.error != 'undefined') {
                showCsvErr();
            } else {
                clearCsvErr();
            }
        });

        var showCardErr = function() {
            $("#custom_card").addClass('redline');
        }

        var clearCardErr = function() {
            $("#custom_card").removeClass('redline');
        }

        var showCsvErr = function() {
            $("#custom_cvc").addClass('redline');
        }
        
        var clearCsvErr = function() {
            $("#custom_cvc").removeClass('redline');
        }

        var showExpiryErr = function() {
            $("#custom_expiry").addClass('redline');
        }
        
        var clearExpiryErr = function() {
            $("#custom_expiry").removeClass('redline');
        }

        function validateEmail(email){
            var reg = /[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/igm;
            return reg.test(email);
        }

        function nameValidation(name) {
            var reg = /^[A-Za-z.\s_-]+$/;
            return reg.test(name);
        }

        var passValidation = function(pass) {
            var reg = /^.{6,}$/;
            return reg.test(pass);
        }

        var submitPaymentForm = function(resp) {
            console.log(resp);
        }

        var validateEmailField = function() {
            var email = $('#registration_form input[name=email]').val();
            if(validateEmail(email) == false) {
                $('#email_err').text('*Invalid Email Provided.');
                $('#email_err').parent().addClass('error').removeClass('success');
                // $('#customerDetailsAchForm input[name=email]').focus();
                return false;
            } else {
                $('#email_err').text('');
                $('#email_err').parent().removeClass('error').addClass('success');
                return true;
            }
        }

        var validateNameField = function () {
            
            var name = $('#registration_form input[name=full_name]').val();
            if(name.trim().length == 0 || nameValidation(name) == false) {
                // $('#customerDetailsAchForm input[name=firstName]').focus();
                $('#fullname_err').text('*Please provide a valid name.');
                $('#fullname_err').parent().addClass('error').removeClass('success');
                return false;
            } else {
                $('#fullname_err').text('');
                $('#fullname_err').parent().removeClass('error').addClass('success');
                return true;
            }
        }

        var validatePasswordField = function() {
            var password = $('#registration_form input[name=password]').val();
            if(password.trim().length == 0 || passValidation(password) == false) {
                // $('#customerDetailsAchForm input[name=firstName]').focus();
                $('#password_err').text('*Password should be minimun 6 characters.');
                $('#password_err').parent().addClass('error').removeClass('success');
                return false;
            } else {
                $('#password_err').text('');
                $('#password_err').parent().removeClass('error').addClass('success');
                return true;
            }
        }

        var validateConfirmPasswordField = function() {
            var password = $('#registration_form input[name=password]').val();
            var cpassword = $('#registration_form input[name=cpassword]').val();
            if(password !== cpassword) {
                // $('#customerDetailsAchForm input[name=firstName]').focus();
                $('#cpassword_err').text('*Confirm password should be same as password.');
                $('#cpassword_err').parent().addClass('error').removeClass('success');
                return false;
            } else {
                $('#cpassword_err').text('');
                $('#cpassword_err').parent().removeClass('error').addClass('success');
                return true;
            }
        }

        var clearOutCardError = function() {
            setTimeout(function() {
                $('.cardBackground').removeClass('vibrate');
            }, 5000);
        }

        var submitRegistrationForm = function () {
            if(checkRegistrationForm()) { 
                $("#registration_form").submit();
                return true;
            } else {
                $("#loader-icon").hide();
                return false;
            }
        }

        var checkRegistrationForm = function() {
            if(validateNameField() && validateEmailField() && validatePasswordField() && validateConfirmPasswordField()) {
                // Ready to submit form
                return true;
            } else {
                return false;
            }
        }

        $(document).ready(function(){

            $(window).bind("pageshow", function(event) {
                $("#loader-icon").hide();
            });

            console.log('plan :: ', Cookies.get('plan'));
            if(Cookies.get('plan') != 'undefined') {
                console.log('executing in here');
                $("#registration_form input[name=plan][value=" + Cookies.get('plan') + "]").prop('checked', 'checked');
            }

            $('#registration_form input').bind({
                blur: function(evt) {
                var name = $(this).attr('name');
                console.log('input name : ', name);
                    switch(name) {
                        case 'email'        : return validateEmailField();
                        case 'full_name'    : return validateNameField();
                        case 'password'     : return validatePasswordField();
                        case 'cpassword'    : return validateConfirmPasswordField();
                        default             : return true; 
                    }
                },
                keypress: function(evt) {
                    var name = $(this).attr('name');
                    if(name == 'full_name') {
                        // Not allowing any numeric value in name field
                        var charCode = (evt.which) ? evt.which : evt.keyCode;
                        return (charCode >= 48 && charCode <= 57) ? false : true;
                    }
                    return true;
                }
            });

            $("#loader-icon").hide();

            var optionScrollWidth;
            // for responsive menu

            (function() {
                var amt = $(".radio-selector:checked").data('amt');
                $("#total-order-id").text('$'+amt+'/m');
            })();

            $('.radio-selector').change(function(e) {
                var amt = $(this).data('amt');
                $("#total-order-id").text('$'+amt+'/m');
            });

            $('.menu-button').click(function() {
                $('.bottomRight').addClass('pull');
            });

            $('.menuClose').click(function() {
                $('.bottomRight').removeClass('pull');
            });
            // for vier more toggle

            $(".viewMore1").click(function() {
                $(".viewMorePanel1").toggle();
            });

            $(".viewMore2").click(function() {
                $(".viewMorePanel2").toggle();
            });

            $(".viewMore3").click(function() {
                $(".viewMorePanel3").toggle();
            });

            $('#register-btn').on('click', function(e) {
                e.preventDefault();
                $("#loader-icon").show();
                stripe.createToken(cardNumberElement).then(setOutcome).then(function(resp) {
                    if(!resp.status) {
                        // Show error message
                        if(typeof resp.obj !== 'undefined' && typeof resp.obj.error !== 'undefined' && typeof resp.obj.error.code !== 'undefined') {
                            $("#loader-icon").hide();
                            var code = resp.obj.error.code;
                            var cardMessage = resp.obj.error.message;
                            if(code == 'card_declined') {
                                alert(cardMessage);
                            } else {
                                console.error('Stripe Error Says : ', cardMessage);
                            }
                            $('.cardBackground').removeClass('vibrate').addClass('vibrate');
                            clearOutCardError();
                        }
                    }  else if(resp.status && typeof resp.id != null ) {
                        console.log('stripe success detected : ', resp);
                        // Trigger form submit
                        $("#stripe_token_field").val(resp.id);
                        console.log('affiliate_id', Cookies.get('affiliate_id'));
                        if(Cookies.get('affiliate_id') != undefined) {
                            $("#affiliate_id").val(Cookies.get('affiliate_id'));
                        }
                        submitRegistrationForm();
                    } else {
                        $("#loader-icon").hide();
                        console.log('stripe -some error occoured- : ', resp);
                    }
                });
                // console.log('stripe : ', stripe_token);
            });
        });

    </script>
  </body>
</html>

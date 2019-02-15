<!DOCTYPE html>
<html lang="en">
  @include('new_version.section.head')
  <body>

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
                    <div class="leftSide">
                        <h2>Get an account to unlock leads</h2>
                        @include('new_version.shared.messages')
                        <h3>personal information</h3>
                        <div class="fieldWrap">
                            {{-- <div class="fieldWrap small error">
                                
                                <input type="text" name="full_name" id="full_name" class="form-control" placeholder="full name">
                                <div id="fname_err" class="errorMsg">Some error is there.</div>
                            </div> --}}

                            <input type="text" name="full_name" class="form-control" placeholder="full name">
                            <input type="email" name="email" class="form-control" placeholder="email">
                            <input type="password" name="password" class="form-control" placeholder="password">
                            <input type="password" name="c_password" class="form-control" placeholder="confirm password">
                        </div>

                        <h3>Card Information</h3>
                        <div class="fieldWrap clearfix">
                            <div class="cardBackground clearfix">
                                <label>
                                    <span>Card number</span>
                                    <div id="card-number-element" class="field"></div>
                                    <span class="brand"><i class="pf pf-credit-card" id="brand-icon"></i></span>
                                </label>
                                <label id="custom_cvc">
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
                            <button id="advanced-search-btn" type="submit" class="orangeBtn">get an account</button>
                            <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/cards.webp" alt="cards">
                            <span>Have an account?</span>
                            <a href="#">login now!</a>
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
    <footer class="footer clearboth">
        <div class="container">
            <div class="col-md-12 pull-left">
                <span>&copy; 2017 Powered by Tier5</span>
                <a href="#">privacy policy</a>
                <a href="#">terms of use</a>
            </div>
        </div>
    </footer>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/bootstrap.min.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/custom.js"></script>
    <script src="https://js.stripe.com/v3/"></script>

    <script>
        var publicKey   =   "{{$stripeDetails->public_key}}";
        var stripe      = Stripe(publicKey);
        var elements    = stripe.elements();
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
            try {
                if (event.brand) {
                    setBrandIcon(event.brand);
                }
                setOutcome(event);
            } catch(err) {
                console.error('card change error : ',err);
            }
        });

        function validateEmail(email){
            var reg = /[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/igm;
            return reg.test(email);
        }

        function nameValidation(name) {
            var reg = /^[A-Za-z.\s_-]+$/;
            return reg.test(name);
        }

        var submitPaymentForm = function(resp) {
            console.log(resp);
        }

        // document.querySelector('form').addEventListener('submit', function(e) {
        //     e.preventDefault();
        //     stripe.createToken(cardNumberElement).then(setOutcome);
        // });

        $(document).ready(function(){

            $(window).bind("pageshow", function(event) {
                $("#loader-icon").hide();
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

            $('#advanced-search-btn').on('click', function(e) {
                e.preventDefault();
                // var name = $('input[name=rbnNumber]:checked').val()

                stripe.createToken(cardNumberElement).then(setOutcome).then(function(resp) {
                    if(!resp.status) {
                        // Show error message
                        console.log('error detected : ', resp);
                        return false;
                    } else if(resp.status && typeof resp.id != null ) {
                        console.log('success detected : ', resp);
                        // Trigger form submit
                        $("#stripe_token_field").val(resp.id);
                        $("#registration_form").submit();
                        $("#loader-icon").show();
                    }
                });
                // console.log('stripe : ', stripe_token);
            });
        });

    </script>
  </body>
</html>

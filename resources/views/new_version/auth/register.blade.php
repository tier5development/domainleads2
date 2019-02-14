<!DOCTYPE html>
<html lang="en">
  @include('new_version.section.head')
  <body>

  <!-- banner -->
  <section class="banner">
    <!-- header -->
    @include('new_version.section.header_menu')

    <!-- inner content -->
    <div class="innerContent signUp clearfix">
        <div class="container customCont">
            <div class="innerContentWrap">
                <form action="#">
                    <div class="leftSide">
                        <h2>Get an account to unlock leads</h2>
                        <h3>personal information</h3>
                            <div class="fieldWrap">
                                <input type="text" name="full_name" class="form-control" placeholder="full name">
                                <input type="text" name="email" class="form-control" placeholder="email">
                                <input type="text" name="password" class="form-control" placeholder="password">
                            </div>
                        <h3>credit card information</h3>
                        <div class="outcome">
                            <div class="error"></div>
                            <div class="success">
                                Success! Your Stripe token is <span class="token"></span>
                            </div>
                        </div>
                        <div class="fieldWrap clearfix">
                            <label>
                                <span>Card number</span>
                                <div id="card-number-element" class="field"></div>
                                <span class="brand"><i class="pf pf-credit-card" id="brand-icon"></i></span>
                            </label>
                            <label>
                                <span>CVC</span>
                                <div id="card-cvc-element" class="field"></div>
                            </label>
                            <label>
                                <span>Expiry date</span>
                                <div id="card-expiry-element" class="field"></div>
                            </label>
                            
                            {{-- <label>
                                <span>Postal code</span>
                                <input id="postal-code" name="postal_code" class="field" placeholder="90210" />
                            </label> --}}
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
                                    <input data-name="{{$planAlias}}" class="radio-selector" type="radio" value="{{$planNum}}" checked="checked" name="radio">
                                    <span class="checkmark"></span>
                                </label>
                            @endforeach
                        </div>
                        <div class="cartWrap" style="display: none;">
                            <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/cart.png" alt="cart">
                            <span>order total</span>
                            <h4 id="total-order-id"></h4>
                        </div>
                    </div>
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
        var stripe = Stripe(publicKey);
        var elements = stripe.elements();
        var style = {
        base: {
                iconColor: '#666EE8',
                color: '#31325F',
                lineHeight: '40px',
                fontWeight: 300,
                fontFamily: 'Helvetica Neue',
                fontSize: '15px',
                border: '1px solid #000',

                '::placeholder': {
                color: '#CFD7E0',
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
            var successElement = document.querySelector('.success');
            var errorElement = document.querySelector('.error');
            successElement.classList.remove('visible');
            errorElement.classList.remove('visible');

            if (result.token) {
                // In this example, we're simply displaying the token
                successElement.querySelector('.token').textContent = result.token.id;
                successElement.classList.add('visible');

            } else if (result.error) {
                errorElement.textContent = result.error.message;
                errorElement.classList.add('visible');
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
            // Switch brand logo
            try {
                console.log('change : ', event.brand);

                if (event.brand) {
                    setBrandIcon(event.brand);
                }
                setOutcome(event);
            } catch(err) {
                console.error('our error : ',err);
            }
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            // var options = {
            //     address_zip: document.getElementById('postal-code').value,
            // };
            stripe.createToken(cardNumberElement).then(setOutcome);
        });

        
        

        $(document).ready(function(){
      
      
        // for custom dropdown
        $('.selectpage').each(function(){
            var thisInstance = $(this);
            var thisVal = $(this), numberOfOptions = $(this).children('option').length;
            thisVal.addClass('select-hidden'); 
            thisVal.wrap('<div class="select"></div>');
            thisVal.after('<div class="select-styled"></div>');
            var styledSelect = thisVal.next('div.select-styled');
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
                console.log('clicked');
                styledSelect.text($(this).text()).removeClass('active');
                
                list.fadeOut(200, 0, function() {
                    thisVal.val($(this).attr('rel'));  
                });
        
                if(thisInstance.data('pagination') !== undefined) {
                    req_pagination = thisVal.val();
                    console.log('pagination', thisInstance.data('pagination'), thisVal.val());
                    $('#pagination').val(thisVal.val());
                }
        
                // Used for advanced-search-box
                if(thisInstance.data('stopsubmit') === undefined) {
                    console.log('not pagination', thisInstance.data('stopsubmit'));
                    submitFormCustom(); 
                }
                // console.log('afsdckhjtaykj kjfy', thisInstance.data('stopsubmit'));
                // submitFormCustom();
            });
        
            $(document).click(function() {
                styledSelect.removeClass('active');
                list.fadeOut(200);
            });
        });
      
      });
      
      </script>
      <script>
    $(document).ready(function(){

        var optionScrollWidth;
        // for responsive menu

        $('.radio-selector').change(function(e) {
            console.log('adkjhyfvk jyhf ', $(this).val(), $(this).data('name'));
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
    });

    </script>
  </body>
</html>

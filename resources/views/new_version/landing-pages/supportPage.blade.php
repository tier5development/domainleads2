<!DOCTYPE html>
<html lang="en">
    <!-- head -->
    @include('new_version.section.head')
    <link rel="stylesheet" href="{{config('settings.APPLICATION-DOMAIN')}}/public/css/new_design/termspolicy.css">
    <body>

    <!-- banner -->
    <section class="banner">
    
        <!-- header -->
        @include("new_version.section.header_menu")
        
        <!-- inner content -->
        
            <style>
                /* iframe's parent node */
                div#root {
                    position: fixed;
                    width: 100%;
                    height: 100%;
                }
            
                /* iframe itself */
                div#root > iframe {
                    display: block;
                    width: 100%;
                    height: 80%;
                    border: none;
                }
            
                p {
                    margin-top: 10px;
                    text-align: center;
                    font-size: 16px;
                    letter-spacing: 0.5px;
                }
                .support_container {
                    max-width: 800px;
                    position: absolute;
                    margin: 100px, 400px;
                    margin: 350px;
                    background: #fff;
                    padding: 20px;
                    border-radius: 15px;

                }
                .support_text {
                    font-size: 12px;
                    padding: 13px;
                }


                
                a.nolt-feedback-button {
                    background: #3b4c78;
                }

                a.nolt-feedback-button:before {
                    background-image: url(https://www.domainleads.io/public/images/favicon-dl.ico);
                }

                .nolt-holder {
                    text-align: center;
                    padding: 100px 15px;
                    background-color: #fff;
                }

                .nolt-holder .nolt-feedback-button {
                    margin-top: 15px;
                    margin-bottom: 15px;
                }
                .nolt-holder_Mail {
                    text-decoration: none;
                    color: #000;
                }

                .nolt-holder_Mail:hover {
                    color: #3578e5;
                }

                .nolt-holder .nolt-feedback-button:hover {
                    background: #72a6f4!important;
                }

            </style> 
                        
            <div id="root">
                
            <div class="nolt-holder">
                <h3>EMAIL US AT: 
                    <a class="nolt-holder_Mail" href="mailto:{{config('settings.SUPPORT-EMAIL')}}">{{config('settings.SUPPORT-EMAIL')}}</a>
                </h3>
                <h3>CALL US AT: {{config('settings.CONTACT-NUMBER')}}</h3>
                <p> IF YOU WANT TO LOG ANY FEEDBACK OR WANT TO LOG ANY FEATURE REQUEST CLICK ON THIS BUTTON</p>
                <a data-nolt="button" href="https://domainleads.nolt.io" class="nolt-feedback-button">Request a feature</a>
                <p>IF YOU WANT TO SPEAK WITH OUR SUPPORT ENGINEER PLEASE CLICK ON THE MESSENGER ICON.</p>
            </div>

            </div>
        
    </section>

    {{-- @include('section.footer_menu') --}}

    @if(config('settings.ISLIVE'))
        <script type="text/javascript">
            var div = document.createElement('div');
            div.className = 'fb-customerchat';
            div.setAttribute('page_id', '793790744302370');
            div.setAttribute('ref', 'b64:Q2hhdC1XaWRnZXQ=');
            document.body.appendChild(div);
            window.fbMessengerPlugins = window.fbMessengerPlugins || {
                init: function () {
                FB.init({
                    appId            : '1678638095724206',
                    autoLogAppEvents : true,
                    xfbml            : true,
                    version          : 'v3.0'
                });
                }, callable: []
            };
            window.fbAsyncInit = window.fbAsyncInit || function () {
                window.fbMessengerPlugins.callable.forEach(function (item) { item(); });
                window.fbMessengerPlugins.init();
            };
            setTimeout(function () {
                (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) { return; }
                js = d.createElement(s);
                js.id = id;
                js.src = "//connect.facebook.net/en_US/sdk/xfbml.customerchat.js";
                fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));
            }, 0);
        </script>
    @endif

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/jQuery-min-3.3.1.min.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/bootstrap.min.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/lity-2.3.1.min.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/custom.js"></script>
  </body>
</html>

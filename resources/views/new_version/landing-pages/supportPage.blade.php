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
            </style> 
                        
            <div id="root">
                <iframe src="https://domainleads.nolt.io" class="iframe-div"></iframe>
                <div>
                    <p>
                        For any product related support please click on the messenger button showing on the right bottom corner of this page and connect with us.
                    </p>
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

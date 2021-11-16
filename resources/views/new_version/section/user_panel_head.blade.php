<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{isset($title) ? $title : 'Document'}}</title>
    <link rel="shortcut icon" type="text/css" href="{{config('settings.APPLICATION-DOMAIN')}}/public/images/favicon-dl.ico">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.min.css">
    <link rel="stylesheet" href="{{config('settings.APPLICATION-DOMAIN')}}/public/css/new_design/dashboard.css">
    <link rel="stylesheet" href="{{config('settings.APPLICATION-DOMAIN')}}/public/css/new_design/loader.css">
    <link rel="stylesheet" href="{{config('settings.APPLICATION-DOMAIN')}}/public/css/new_design/custom_msg.css">
    <link rel="stylesheet" href="{{config('settings.APPLICATION-DOMAIN')}}/public/css/new_design/mobile-view.css">
    <link rel="stylesheet" href="{{config('settings.APPLICATION-DOMAIN')}}/public/css/new_design/profile-pic.css">
    <link rel="stylesheet" href="{{config('settings.APPLICATION-DOMAIN')}}/public/css/new_design/style.css">
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/jQuery-min-3.3.1.min.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/jquery-ui.min.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/touch-punch.min.js"></script>
    {{-- <script src="jquery.ui.mouse.js"></script> --}}
    {{-- <script src="https://raw.githubusercontent.com/furf/jquery-ui-touch-punch/master/jquery.ui.touch-punch.min.js"></script> --}}
    <!-- Global site tag (gtag.js) - Google Ads: 977037556 -->

    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-977037556"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'AW-977037556');
    </script>

    <!-- Facebook Pixel Code -->

    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1040483139723270');
        fbq('track', 'PageView');
    </script>

    <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1040483139723270&ev=PageView&noscript=1" />
    </noscript>

    <!-- End Facebook Pixel Code -->

    <!-- Facebook Pixel Code -->

    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '2155902194542435');
        fbq('track', 'PageView');
    </script>

    <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=2155902194542435&ev=PageView&noscript=1" />
    </noscript>

    <!-- End Facebook Pixel Code -->
    
    <script>
    window.Userback = window.Userback || {};
    Userback.access_token = '5976|61007|Hpb1VDyWk5WhiMswz09JSD1DRKKY3RnzXXKH61CZ6MYm5FbyVA';
    (function(d) {
    var s = d.createElement('script');s.async = true;
    s.src = 'https://static.userback.io/widget/v1.js';
    (d.head || d.body).appendChild(s);
    })(document);
    </script>

</head>
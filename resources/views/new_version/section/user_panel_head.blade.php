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
        fbq('init', '1544020915892734');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1544020915892734&ev=PageView&noscript=1" />
    </noscript>
    <!-- End Facebook Pixel Code -->
    @endif
</head>
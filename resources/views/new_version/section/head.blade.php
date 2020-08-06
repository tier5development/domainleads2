<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Domain Leads</title>
    <link rel="shortcut icon" type="text/css" href="{{config('settings.APPLICATION-DOMAIN')}}/public/images/favicon-dl.ico">

    <!-- Bootstrap -->
    <link href="{{config('settings.APPLICATION-DOMAIN')}}/public/css/new_design/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{config('settings.APPLICATION-DOMAIN')}}/public/css/new_design/dashboard.css">
    <link rel="stylesheet" href="{{config('settings.APPLICATION-DOMAIN')}}/public/css/new_design/loader.css">
    <link href="{{config('settings.APPLICATION-DOMAIN')}}/public/css/new_design/style.css" rel="stylesheet">
    <link rel="stylesheet" href="{{config('settings.APPLICATION-DOMAIN')}}/public/css/new_design/custom_msg.css">
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lity/2.3.1/lity.min.css"> --}}
    <link rel="stylesheet" href="{{config('settings.APPLICATION-DOMAIN')}}/public/css/new_design/lity.min.css">

    <!-- fonts -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/jQuery-min-3.3.1.min.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/jquery-ui.min.js"></script>

    @if(config('settings.ISLIVE') == true)
        <meta name="description" content="Latest registered domains available.">
    @endif
    <meta name="csrf-token" content="{{csrf_token()}}">
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
</head>
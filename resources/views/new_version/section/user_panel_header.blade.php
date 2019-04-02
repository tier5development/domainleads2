<header class="dashHeader">
    
        <div class="headerLeft">
            <a href="{{route('home')}}"><img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/logo.png" alt=""></a>
        </div>
        <div class="headerRight">
            <div class="heaverNav">
                <div class="reusable-user-panel-header">
                    @include('new_version.shared.reusable-user-panel-header', ['user' => $user])
                </div>
            </div>
            @include('new_version.shared.user-settings-dropdown', ['user' => $user])
        </div>
        <div class="clear"></div>
</header>

<!-- Facebook Pixel Code -->

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
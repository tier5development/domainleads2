<!DOCTYPE html>
<html lang="en">
    @include('new_version.section.user_panel_head', ['title' => 'Domainleads Search'])

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
    <!-- Event snippet for Software User conversion page -->
    <script>
        gtag('event', 'conversion', {'send_to': 'AW-977037556/WnBCCJjDytoBEPTR8dED'});
    </script>
    <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=649270039158137&ev=PageView&noscript=1" />
    </noscript>
    <!-- End Facebook Pixel Code -->
    @endif

<body>
    @include('new_version.shared.loader')
    <div class="container">

        @include('new_version.section.user_panel_header', ['user' => $user])
        <section class="mainBody">
            {{-- Include common dashboard right panel --}}
            @include('new_version.shared.right-panel')

            <div class="leftPanel">
                <div class="leftPanelHeader">
                    @if(!Session::has('first_visit'))
                        <div class="clientImg">
                            @if(strlen($user->profile_image_icon) > 0)
                                {!! $user->profile_image_icon !!}
                            @else
                                <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/dl_default_user_pic_40x40.png" alt="">
                            @endif
                        </div>
                        <div class="clientInfo">
                            <h1>Hey {{ucwords(strtolower(explode(' ', $user->name)[0]))}},</h1>
                            <p>Welcome to Domain Leads. Start your domain search right here.</p>
                        </div>
                    @endif
                    @include('new_version.shared.messages')
                </div>
                {{-- Search form lies here --}}

                
                @if(Session::has('first_visit'))
                    @if(config('settings.ISLIVE') == true)
                        <script>
                            fbq('track', 'Purchase', {
                                value: "{{config('settings.PLAN.PUBLISHABLE.'.$user->user_type)[1]}}",
                                currency: 'USD',
                            });
                        </script>
                    @endif
                    <div class="innerContent" style="margin: -20px;">
                        <div class="container customCont cancelDomain signUpConfirmation">
                          <div class="col-sm-8 innerContentWrap">
                              <div class="col-sm-12 createForm">
                                <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_success.png" alt="domain cancel">
                                <h2>Congratulation <span>{{$user->name}}!</span></h2><br>
                                <p>You have succesfully signed up for your Domain Leads Account.<br><br>Please check your email to validate your email account.</p><br>
                                <p>Continue to domainleads. <a href="{{route('search')}}" class="greenBtn anchor-btn">Search Domains</a></p><br>
                            </div>
                          </div>
                        </div>
                    </div>
                @else
                    @include('new_version.search.search-form')
                @endif
            </div>

            {{-- Include footer --}}
            @include('new_version.shared.dashboard-footer-mobile')
        </section>
        
        {{-- Include footer --}}
        @include('new_version.shared.dashboard-footer')
    </div>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/custom2.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/right-panel.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/common.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>

    <script type="text/javascript">
        
    </script>
</body>
</html>
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
        fbq('init', '1544020915892734');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1544020915892734&ev=PageView&noscript=1" />
    </noscript>
    <!-- End Facebook Pixel Code -->
    @endif 

  <!-- banner -->
  <section class="banner">
    <!-- header -->
    @include('new_version.section.header_menu')
    <!-- inner content -->
    <div class="innerContent login clearfix">
        <div class="container customCont">
            <div class="col-sm-8 col-xs-12 innerContentWrap">
                <div class="col-md-6 col-sm-7 createForm">
                    <h2>Login</h2><br>
                    {{-- Error or Success Message --}}
                    @include('new_version.shared.messages')
                    
                    <form action="{{route('loginPost')}}" method="post">
                        <div class="form-group">
                            <input name="email" type="email" class="form-control" placeholder="email">
                        </div>
                        <div class="form-group">
                            <input name="password" type="password" class="form-control" id="" placeholder="*********">
                        </div>
                        <div class="forgot clearfix">
                            <label class="forgotText">remember me
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                            <a href="{{route('forgotPassword')}}">forgot?</a>
                            {{csrf_field()}}
                            {{-- <a href="#">forgot?</a> --}}
                        </div>
                        <button type="submit" class="btn button gradiant-orange">login</button>
                        <span>Don't have an account?</span>
                        <a href="{{route('signupPage')}}">register now!</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <br><br>
    <!-- footer -->
    @include('section.footer_menu')
  </section>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/bootstrap.min.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/custom.js"></script> 
  </body>
</html>

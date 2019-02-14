<!DOCTYPE html>
<html lang="en">
  @include('new_version.section.head')
  <body>

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
    <!-- footer -->
    <div class="footer clearfix">
      <div class="container">
        <div class="col-md-12 pull-left">
          <span>&copy; 2017 Powered by Tier5</span>
          <a href="#">privacy policy</a>
          <a href="#">terms of use</a>
        </div>
      </div>
    </div>
  </section>
  @include('new_version.shared.sticky-note')
  <!-- sticky text -->
  {{-- <div class="stickyBox" id="stickyBoxWrap" style="display: none;">
    <div id="popup">
      <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/sticky_Logo.png">
      <div class="stickyText">
        <p>You have unlocked 4 leads today.</p>
        <p>You can unlocked upto <span>50</span> leads per day.</p>
      </div>
    </div>
  </div>  --}}


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/bootstrap.min.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/custom.js"></script> 
  </body>
</html>

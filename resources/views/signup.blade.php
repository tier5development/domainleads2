<!DOCTYPE html>
<html lang="en">
    @include('section.head', ['title' => '|| Sign up ||Domain Leads'])
    <body>

    <!-- banner -->
    <section class="banner">
        <!-- header -->
        @include('section.header_menu')
        <!-- inner content -->
        <div class="innerContent clearfix">
            <div class="container customCont">
                <div class="col-sm-8 innerContentWrap">
                    <div class="ol-md-6 col-sm-7 createForm">
                        <h2>Get an account to unlock leads</h2>
                        <div>
                            @if(Session::has('error'))
                                <div class="form-group">
                                    <br>
                                    <small style="color : #e95311">{{strtoupper(Session::get('error'))}}</small>
                                    {{Session::forget('error')}}
                                </div>
                            @elseif($errors->any()) 
                                <div class="form-group">
                                    @foreach ($errors->all() as $error)
                                        <small style="color : #e95311">{{$error}}</small> <br>       
                                    @endforeach
                                </div>
                            @endif

                            <form method="POST" action="{{route('signupPost')}}" id="signup-form">
                                    <div class="form-group nameField">
                                        <div>
                                            <label for="first_name"> *Name</label>
                                        </div>
                                        <div>
                                            <input type="text" name="first_name" class="form-control" id="first_name" placeholder="first name">
                                            <input type="text" name="last_name" class="form-control" id="last_name" placeholder="last name">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div>
                                            <label for="email"> *Email</label>
                                        </div>
                                        <div>
                                            <input type="email" name="email" class="form-control" id="email" placeholder="email">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div>
                                            <label for="password"> *Password</label>
                                        </div>
                                        <div>
                                            <input type="password" name="password" class="form-control" id="password" placeholder="******">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div>
                                            <label for="password"> *Confirm Password</label>
                                        </div>
                                        <div>
                                            <input type="password" name="c_password" class="form-control" id="c_password" placeholder="******">
                                        </div>
                                    </div>
                                    {{csrf_field()}}
                                    <button type="submit" id="submit-form" class="btn button gradiant-orange">get an account</button>
                                    <span>have an account?</span>
                                    <a href="{{route('loginPage')}}">login now!</a>
                                </form>

                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        
    </section>
    @include('section.footer_menu')
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/jQuery-min-3.3.1.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/bootstrap.min.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/custom.js"></script>
  </body>

  <script type="text/javascript">

    var validateName = function(name) {
        // General name validation regex
        var regex = /^[a-zA-Z ]{2,40}$/;
        return regex.test(name);
    }
    var validateEmail = function(email) {
        // Regex used by laravel to validate email
        var regex = /^.+@.+$/i;
        return regex.test(email);
    }
    var validatePassword = function(pass) {
        // Regex used to validate password
        var regex = /^.{6,}$/;
        return regex.test(pass);
    }

    var checkForm = function() {
        // Validate the form
        var flag = true;
        var msg = '';
        var f_name = $('#first_name').val();
        if(!validateName(f_name)) {
            flag = false;
            msg = 'Invalid name';
        }
        var l_name = $('#last_name').val();
        if(!validateName(l_name)) {
            flag = false;
            msg = 'Invalid name';
        }
        var email = $('#email').val();
        if(!validateEmail(email)) {
            flag = false;
            msg = 'Invalid email';
        }
        var password = $('#password').val();
        if(!validatePassword(password)) {
            flag = false;
            msg = 'Invalid password (must be atleast 6 characters long)';
        }
        var c_password = $('#c_password').val();
        if(!validatePassword(password)) {
            flag = false;
            msg = 'Invalid confirm password (must be atleast 6 characters long)';
        }
        if(password !== c_password) {
            flag = false;
            msg = 'Password and confirm password did not match!';
        }
        return {flag : flag, msg : msg};
    }
    $(document).ready(function() {
        $('#submit-form').on('click', function(e) {
            e.preventDefault();
            var validation = checkForm();
            if(validation.flag == true) {
                $('#signup-form').submit();
            } else {
                alert(validation.msg);
            }
        });
    });
  </script>
</html>

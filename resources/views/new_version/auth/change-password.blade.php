<!DOCTYPE html>
<html lang="en">
    @include('section.user_panel_head', ['title' => 'Edit Profile'])
<body>
    <div class="container">
        @include('section.user_panel_header', ['user' => $user])
        
        @include('new_version.shared.loader')

        <section class="mainBody">
            <div class="leftPanel leadUnlock">
                
                <h2 class="editProfileHeading">Change your password below</h2>        
                    <div class="profileFormArea">
                        {{-- <div class="mesg">
                            @if(Session::has('fail'))
                                <div class="formHeading" style="margin-top:18px;">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                                    <strong>Error!</strong> {{Session::get('fail')}}
                                </div>
                                @php Session::forget('fail') @endphp
                            @elseif(Session::has('success'))
                                <div class="formHeading" style="margin-top:18px;">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                                    <strong>Success!</strong> {{Session::get('success')}}
                                </div>
                                @php Session::forget('success') @endphp
                            @endif   
                        </div> --}}

                        @if(Session::has('fail'))
                            <div class="alertBox error">
                                <p>{{Session::get('fail')}}</p>
                                <span class="close"></span>
                            </div>
                        @elseif(Session::has('success'))
                            <div class="alertBox success">
                                <p>{{Session::get('success')}}</p>
                                <span class="close"></span>
                            </div>
                        @endif

                        
                        <form action="{{route('changePasswordPost')}}" method="POST" class="change-password form-group" id="changePasswordForm">
                            <div class="formRow">
                                <div class="fieldWrap">
                                    <input type="password" name="o_pass" id="o_pass" placeholder="Current Password">
                                    <div id="o_pass_err" class="errorMsg"></div>
                                </div>
                            </div>
                            <div class="formRow">
                                <div class="fieldWrap small">
                                    <input type="password" name="pass" id="pass" placeholder="New Password">
                                    <div id="pass_err" class="errorMsg"></div>
                                </div>
                                <div class="fieldWrap small">
                                    <input type="password" name="c_pass" id="c_pass" placeholder="Confirm New Password">
                                    <div id="c_pass_err" class="errorMsg"></div>
                                </div>
                            </div>
                            
                            <div class="formRow">
                                <button type="submit" id="submitChangePasswordForm" class="orangeBtn">UPDATE</button>
                            </div>
                            {{csrf_field()}}
                        </form>
                    </div>
                
            </div>

            {{-- Right panel of dashboard comes here --}}
            @include('new_version.shared.right-panel')

            <footer class="footer mobileOnly">
                &copy; 2017 Powered by Tier5 <span><a href="">Privacy Policy</a> / <a href="">Terms of Use</a></span>
            </footer>
        </section>

        <footer class="footer">
            &copy; 2017 Powered by Tier5 <a href="">Privacy Policy</a> / <a href="">Terms of Use</a>
        </footer>
    </div>


    {{-- <div class="alert">
        <div class="alertLeft">
            <img src="images/Logo_symbol_green.png" alt="">
        </div>
        <div class="alertRight">
            <p>You have unlocked 4 leads today.
                <br>
                You can unlocked upto <span>50</span> leads per day.
            </p>
        </div>
    </div> --}}

    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/custom2.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/common.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/right-panel.js"></script>

    <script type="text/javascript">
    
        var checkPass = function(pass) {
            var pattern = /^.{6,}$/;
            return pattern.test(pass);
        }
        var clearAll = function() {
            $('#email_err').text('');
            $('#pass_err').text('');
            $('#c_pass_err').text('');
        }

        var checkChangePasswordForm = function() {
            // Check the form
            var flag = true;
            var pass = $('#pass').val();
            var c_pass = $('#c_pass').val();
            var o_pass = $('#o_pass').val();
            
            if(!checkPass(pass)) {
                var msg = 'Password should be minimum of 6 characters.';
                $('#pass_err').text(msg);
                $('#pass_err').parent().addClass('error');
                return {msg : msg, flag: false}
            } else {
                $('#pass_err').text('');
                $('#pass_err').parent().removeClass('error');
            } 
            
            
            if(pass !== c_pass) {
                var msg = 'Password and confirm password should match.'
                $('#c_pass_err').text(msg);
                $('#c_pass_err').parent().addClass('error');
                return {msg : msg, flag: false}
            } else {
                $('#c_pass_err').text('');
                $('#c_pass_err').parent().removeClass('error');
            }
            
            
            if(o_pass.trim().length == 0) {
                var msg = 'You have to enter your old password.'
                $('#o_pass_err').text(msg);
                $('#o_pass_err').parent().addClass('error');
                return {msg : msg, flag: false}
            } else {
                $('#o_pass_err').text('');
                $('#o_pass_err').parent().removeClass('error');
            }
            return {msg : 'success', flag: true};
        }

        $(document).ready(function() {
            setTimeout(() => {
                $('#loader-icon').hide();
            }, 400);

            $('#submitChangePasswordForm').click(function(e) {
                e.preventDefault();
                clearAll();
                var ob = checkChangePasswordForm();
                if(ob.flag) {
                    $('#changePasswordForm').submit();
                    $('#loader-icon').show();
                }
            });

            // $('input').blur(function() {
            //     checkChangePasswordForm();
            // });

            $('.close').click(function() {
                $(this).parent().removeClass('error').removeClass('success').hide();
            });
        });
    </script>
</body>
</html>
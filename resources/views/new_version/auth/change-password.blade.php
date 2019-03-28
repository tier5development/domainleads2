<!DOCTYPE html>
<html lang="en">
    @include('new_version.section.user_panel_head', ['title' => 'Edit Profile'])
<body>
    <div class="container">
        @include('new_version.section.user_panel_header', ['user' => $user])
        
        @include('new_version.shared.loader')

        <section class="mainBody">
            <div class="leftPanel leadUnlock">
                @include('new_version.shared.profile-panel-header')
                <h2 class="editProfileHeading">Change your password below</h2>
                <div class="profileFormArea">
                    <form action="{{route('changePasswordPost')}}" method="POST" class="change-password form-group" id="changePasswordForm">
                        {{-- Error or Success Message --}}
                        @include('new_version.shared.messages')
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
                            <button type="submit" id="submitChangePasswordForm" class="orangeBtn">CHANGE</button>
                        </div>
                        {{csrf_field()}}
                    </form>
                </div>
            </div>

            {{-- Right panel of dashboard comes here --}}
            @include('new_version.shared.right-panel')

            @include('new_version.shared.dashboard-footer', ['class' => 'footer mobileOnly'])
        </section>

        @include('new_version.shared.dashboard-footer', ['class' => 'footer'])
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

            $('#c_pass_err').text('');
            $('#c_pass_err').parent().removeClass('error');
            $('#o_pass_err').text('');
            $('#o_pass_err').parent().removeClass('error');
            $('#pass_err').text('');
            $('#pass_err').parent().removeClass('error');

            var flag = true;
            var pass = $('#pass').val();
            var c_pass = $('#c_pass').val();
            var o_pass = $('#o_pass').val();
            
            if(!checkPass(pass)) {
                var msg = 'Password should be minimum of 6 characters.';
                $('#pass_err').text(msg);
                $('#pass_err').parent().addClass('error');
                return {msg : msg, flag: false}
            } else if(pass !== c_pass) {
                var msg = 'Password and confirm password should match.'
                $('#c_pass_err').text(msg);
                $('#c_pass_err').parent().addClass('error');
                return {msg : msg, flag: false}
            } else if(o_pass.trim().length == 0) {
                var msg = 'You have to enter your old password.'
                $('#o_pass_err').text(msg);
                $('#o_pass_err').parent().addClass('error');
                return {msg : msg, flag: false}
            }
            return {msg : 'success', flag: true};
        }

        $(document).ready(function() {

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
        });
    </script>
</body>
</html>
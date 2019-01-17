<!DOCTYPE html>
<html lang="en">
    @include('section.user_panel_head', ['title' => 'Edit Profile'])
<body>
    <div class="container">
        @include('section.user_panel_header', ['user' => $user])
        
        @include('new_version.shared.loader')

        <section class="mainBody">
            <div class="leftPanel leadUnlock">
                
                <h2 class="editProfileHeading">Edit your profile information</h2>
                <div class="profileTip">
                    <figure>
                        <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_camera_black.png" alt="">
                    </figure>
                    <p>
                        TIP: Click on the "Camera" icon on the profile pic<br>to change it
                    </p>
                </div>
                
                
                <div class="profileFormArea">
                    <div class="mesg">
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
                    </div>

                    <div class="formHeading">
                        Your personal information
                    </div>
                    
                    
                    <form action="{{route('updateUserInfo')}}" method="POST" id="updateUserInfo">
                        <div class="formRow">
                            @php
                                $nameArr = explode(' ', $user->name, 2);
                                $fname = isset($nameArr[0]) ? $nameArr[0] : 'USER';
                                $lname = isset($nameArr[1]) ? $nameArr[1] : '';
                            @endphp
                            <div class="fieldWrap small">
                                <input type="text" name="fname" id="fname" placeholder="First Name" value="{{$fname}}">
                                <div id="fname_err" class="errorMsg"></div>
                            </div>
                            <div class="fieldWrap small">
                                <input type="text" name="lname" id="lname" placeholder="Last Name" value="{{$lname}}">
                                <div id="lname_err" class="errorMsg"></div>
                            </div>
                        </div>

                        <div class="formRow">
                            <input type="text" name="email" id="email" placeholder="Email" value="{{$user->email}}" readonly>
                        </div>
                        <div class="formRow">
                            <button type="submit" id="updateUserInfoBtn" class="orangeBtn">UPDATE</button>
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
        
        var checkUserInfoForm = function() {
            var flag = true;
            var fname = $('#fname').val();
            var lname = $('#lname').val();
            var name = fname + ' ' + lname;
            if(fname.trim().length == 0 || name.trim().length == 0) {
                $('#fname').parent().addClass('error');
                var msg = 'Please enter a valid first name.'
                $('#fname_err').text(msg);
                return {msg : msg, flag: false}
            } else {
                $('#fname').parent().removeClass('error');
                var msg = ''
                $('#fname_err').text(msg);
                return {msg : msg, flag: true}
            }
            // return {msg : 'success', flag: true};
        }

        $(document).ready(function() {
            setTimeout(() => {
                $('#loader-icon').hide();
            }, 400);
            $('#updateUserInfoBtn').click(function(e) {
                e.preventDefault();
                var ob = checkUserInfoForm();
                if(ob.flag) {
                    $('#updateUserInfo').submit();
                    $('#loader-icon').show();
                }
            });
        });

        $('input').blur(function() {
            var id = $(this).attr('id');
            var val = $(this).val();
            checkUserInfoForm();
            // if(id == 'fname' && val.trim().length == 0) {
            //     checkUserInfoForm();
            // }
        });
    </script>
</body>
</html>
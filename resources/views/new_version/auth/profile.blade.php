<!DOCTYPE html>
<html lang="en">
    @include('new_version.section.user_panel_head', ['title' => 'Edit Profile'])
<body>
    @include('new_version.shared.loader')
    <div class="container">

        @include('new_version.section.user_panel_header', ['user' => $user])
        <section class="mainBody">

            {{-- Right panel of dashboard comes here --}}
            @include('new_version.shared.right-panel')

            <div class="leftPanel leadUnlock">
                
                <h2 class="editProfileHeading">Edit your profile information</h2>
                {{-- <div class="profileTip">
                    <figure>
                        <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_camera_black.png" alt="">
                    </figure>
                    <p>
                        TIP: Click on the "Camera" icon on the profile pic<br>to change it
                    </p>
                </div> --}}
                <div class="displayPic">
                    <div class="displayPicContainer">{!! $user->profile_image !!}</div>
                    <div class="changePic">
                        <form method="POST" action="{{route('uploadImage')}}" id="uploadImagePost" enctype="multipart/form-data">
                            {{csrf_field()}}
                            <input type="file" name="originalImage" class="changeProPic" accept=".jpeg,.png,.jpg">
                            <input type="hidden" value="" name="icon" id="iconId">
                            <input type="hidden" value="" name="image" id="imageId">
                        </form>
                    </div>
                </div>
                
                <div class="profileFormArea">
                    {{-- Error or Success Message --}}
                    @include('new_version.shared.messages')

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

            <footer class="footer mobileOnly">
                &copy; 2017 Powered by Tier5 <span><a href="">Privacy Policy</a> / <a href="">Terms of Use</a></span>
            </footer>
        </section>

        <footer class="footer">
            &copy; 2017 Powered by Tier5 <a href="">Privacy Policy</a> / <a href="">Terms of Use</a>
        </footer>

        
    </div>



    <div class="uploadPicModal" style="display: none;">
    <div class="modalContainer">
    <span class="close"></span>
    <h2>Edit Image</h2>
    <div class="imageContainer">
        <div class="imageHolder">
        <img alt="">
        </div>
        <div class="imageFrame"></div>
        <div class="editInfo" style="display: none;"></div>
    </div>
    <div class="editCtrl">
        <span>Zoom In</span>
        <div class="range"><input type="range" min="185" max="740" value="185" class="scale"></div>
        <span>Zoom Out</span>
        <button type="button" class="resetZoom">Reset</button>
    </div>
    <div class="editCtrl rotateCtrl">
        <span>0 Deg</span>
        <div class="range"><input type="range" min="0" max="180" value="0" class="rotate"></div>
        <span>180 Deg</span>
        <button type="button" class="resetRotate">Reset</button>
    </div>
    
        <div class="previewArea">
            <div class="preview">
                <h3>Preview</h3>
                <div class="output">{!! $user->profile_image !!}</div>
            </div>
            <div class="preview2">
                <h3>Icon</h3>
                <div class="output2">{!! $user->profile_image_icon !!}</div>
            </div> 
        </div>

        <div class="modalFooter">
            <button class="crop orangeBtn">Crop</button>
            <button class="greenBtn" id="uploadProfilePic">Upload</button>
        </div>
        </div>
    </div>







    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/custom2.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/common.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/right-panel.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>

    <script type="text/javascript">
        var img = null;
        var images = {
            icon : null,
            image : null,
            iconEnc: null,
            imageEnc: null
        };
        var nameValidation = function(name) {
            var regex = /^[a-zA-Z ]{2,30}$/;
            return regex.test(name);
        }

        var checkUserInfoForm = function() {
            var flag = true;
            var fname = $('#fname').val();
            var lname = $('#lname').val();
            var name = fname + ' ' + lname;
            if(fname.trim().length == 0 || name.trim().length == 0 || !nameValidation(fname) ) {
                $('#fname').parent().addClass('error');
                var msg = 'Please enter a valid first name.';
                $('#fname_err').text(msg);
                return {msg : msg, flag: false}
            } else {
                $('#fname').parent().removeClass('error');
                var msg = '';
                $('#fname_err').text(msg);
                return {msg : msg, flag: true}
            }
            // return {msg : 'success', flag: true};
        }

        var cropify = function() {
            cropedCss = $(".imageHolder").attr("style");
            images.image = '<div style="width:185px;height:124px;overflow:hidden;"><div style="position:relative; left:-59px; top:-64px;"><div style="'+ cropedCss +'"><img style="width:100%;" src="'+ img +'"></div></div></div>';
            images.icon = '<div style="width:185px;height:124px;overflow:hidden;transform:scale(0.34);position:relative;left:-185%;top:-103%;"><div style="position:relative; left:-59px; top:-64px;"><div style="'+ cropedCss +'"><img style="width:100%;" src="'+ img +'"></div></div></div>';
            
            images.imageEnc = '<div style="width:185px;height:124px;overflow:hidden;"><div style="position:relative; left:-59px; top:-64px;"><div style="'+ cropedCss +'"><img style="width:100%;" src="'+ '--icon--img--to--upload--' +'"></div></div></div>';
            images.iconEnc = '<div style="width:185px;height:124px;overflow:hidden;transform:scale(0.34);position:relative;left:-185%;top:-103%;"><div style="position:relative; left:-59px; top:-64px;"><div style="'+ cropedCss +'"><img style="width:100%;" src="'+ '--icon--img--to--upload--' +'"></div></div></div>';

            $(".output").html(images.image);
            $(".output2").html(images.icon);
            
            console.log('icon : ', images.icon);
            console.log('image : ', images.image);
        }
        
        $(document).ready(function() {
        $('#updateUserInfoBtn').click(function(e) {
            e.preventDefault();
            var ob = checkUserInfoForm();
            if(ob.flag) {
                $('#updateUserInfo').submit();
                $('#loader-icon').show();
            }
        });

        // image crop code --------------------v
        
        $(".changeProPic").change(function(e){
        var preview = $('.imageHolder img');
        var file = this.files[0];
        var reader = new FileReader();
            if(file) {
                console.log('file present : ', reader.readAsDataURL(file));
                // img = reader.result;
                // $(".imageHolder img").attr("src", reader.result);
            } else {
                //console.log('file absent : ', reader.readAsDataURL(file));
                preview.attr('src', "");
            }

            reader.onloadend = function() {
                $(".imageHolder img").attr("src", reader.result);
                img = reader.result;
                cropify();
                //console.log('on load loaded : ', reader.result);
            }
            $(".uploadPicModal").fadeIn();
            //console.log('file : ', reader.result);
        });

        //$(".imageHolder img").attr("src",img);

        $(".imageHolder").draggable();

            var scale = 185;
            var rotate = 0;
            var cropedCss;
            $(".scale").mousemove(function(){
                scale = $(this).val();
                $(".imageHolder").css("width", scale + "px");
            });
            $(".scale").change(function(){
                $(".editInfo").text((scale/185).toFixed(1) + "x");
                $(".editInfo").fadeIn(300);
                setTimeout(function(){
                    $(".editInfo").text("");
                    $(".editInfo").fadeOut(300);
                }, 1000);
            });

            $(".rotate").mousemove(function(){
                rotate = $(this).val();
                $(".imageHolder").css("transform", "rotate(" + rotate + "deg)");

            });
            $(".rotate").change(function(){
                $(".editInfo").text(rotate + "deg");
                $(".editInfo").fadeIn(300);
                setTimeout(function(){
                    $(".editInfo").text("");
                    $(".editInfo").fadeOut(300);
                }, 1000);
            });

            $(".resetZoom").click(function(){
                scale = 185;
                $(".imageHolder").css("width", scale + "px");
                $(".scale").val(0);
            });
            $(".resetRotate").click(function(){
                rotate = 0;
                $(".imageHolder").css("transform", "rotate(" + rotate + "deg)");
                $(".rotate").val(0);
            });

            $('#uploadProfilePic').click(function(e) {
                e.preventDefault();
                cropify();
                $('#iconId').val(images.iconEnc);
                $('#imageId').val(images.imageEnc);
                $('#uploadImagePost').submit();
            });

            $(".crop").click(function() {
                cropify();
            });

            $(".modalContainer .close").click(function(){
                $(".uploadPicModal").fadeOut();
                $(".changeProPic").val("");
                img = null;
                $(".output").html("");
                scale = 185;
                rotate = 0;
                $(".scale").val(0);
                $(".rotate").val(0);
                $(".imageHolder").css("transform", "rotate(" + rotate + "deg)");
                $(".imageHolder").css("width", scale + "px");
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
<header class="dashHeader">
    <div class="headerLeft">
        <a href=""><img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/logo.png" alt=""></a>
    </div>
    <div class="headerRight">
        <div class="heaverNav">
            <ul>
                <li>
                    <a href="{{route('search')}}">
                        <span class="desktopOnly">SEARCH DOMAIN</span>
                        <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/search.png" alt="SEARCH DOMAIN" class="mobileOnly">
                    </a>
                </li>
                @if($user->user_type < config('settings.PLAN.L1'))
                <li>
                    <a href="{{route('myUnlockedLeads')}}">
                        <span class="desktopOnly">UNLOCKED LEADS</span>
                        <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_unlocked_leads_mobile.png" alt="UNLOCKED LEADS" class="mobileOnly">
                    </a>
                </li>
                @endif

                @if($user->user_type == config('settings.ADMIN-NUM'))
                    <li>
                        <a href="{{route('manage')}}">
                            <span class="desktopOnly">MANAGE</span>
                            {{-- <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_unlocked_leads_mobile.png" alt="UNLOCKED LEADS" class="mobileOnly"> --}}
                        </a>
                    </li>
                    <li>
                        <a href="{{route('UserList')}}">
                            <span class="desktopOnly">USER LIST</span>
                            {{-- <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_unlocked_leads_mobile.png" alt="UNLOCKED LEADS" class="mobileOnly"> --}}
                        </a>
                    </li>
                    <li>
                        <a href="{{route('importExport')}}">
                            <span class="desktopOnly">IMPORT CSV</span>
                            {{-- <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_unlocked_leads_mobile.png" alt="UNLOCKED LEADS" class="mobileOnly"> --}}
                        </a>
                    </li>
                @endif

            </ul>
        </div>
        <div class="userNameArea">
            <div class="profileTag" id="profileTag">
                <div class="userName">
                    <span class="desktopOnly">{{isset($user) && strlen(trim($user->name)) > 0 ? strtoupper($user->name) : 'USER'}}</span>
                </div>
                <div class="userImg">
                    <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/Profile_circle.png" alt="">
                </div>
            </div>
            <div id="profileMenu" class="profileMenu" style="display: none">
                <div class="closeMenu profileMenuCloseBtn" id="profileMenuCloseBtn"></div>
                <div class="profilePic">
                    {{-- <a href="javascript:void(0)" class="changePic"><img id="upload_profile_pic" src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_camera_green.png" alt="change picture"></a> --}}
                    <input type="file" id="avatar_file" accept="image/jpeg, image/png" style="display: none;" />
                    <img id="profile_pic_container" style="max-width: 185; max-height: 123;" src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/profilePic.png" alt="">
                </div>
                <div class="profileMenuBody">
                    <div class="profileName">
                        <p>{{isset($user) && strlen(trim($user->name)) > 0 ? strtoupper($user->name) : 'USER'}}</p>
                        <span>{{isset($user) && strlen(trim($user->email)) > 0 ? ($user->email) : 'USER-EMAIL'}}</span>
                    </div>
                    <ul>
                        <li><a href="{{route('profile')}}">Profile</a></li>
                        <li><a href="{{route('changePassword')}}">Change Password</a></li>
                        <li><a href="{{route('logout')}}">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</header>

<script type="text/javascript">

image_config = {
    'w' : 185,
    'h' : 123,
    circular : {
        'w' : 40
    }
}


var processImage = function(picContainerId, input) {

    if (input.files && input.files[0]) {
        var file = input.files[0];

        console.log('input file : width - ', file.width, ' height : ', file.height);

        // Ensure it's an image
        if(file.type.match(/image.*/)) {
            console.log('An image has been loaded');

            // Load the image
            var reader = new FileReader();
            reader.onload = function (readerEvent) {
                var image = new Image();
                image.onload = function (imageEvent) {

                    // Resize the image
                    var canvas = document.createElement('canvas'),
                        max_size = 544,// TODO : pull max size from a site config
                        width = image.width,
                        height = image.height;
                    if (width > height) {
                        if (width > max_size) {
                            height *= max_size / width;
                            width = max_size;
                        }
                    } else {
                        if (height > max_size) {
                            width *= max_size / height;
                            height = max_size;
                        }
                    }
                    console.log('height : ', height, 'width : ', width);
                    canvas.width = width;
                    canvas.height = height;
                    canvas.getContext('2d').drawImage(image, 0, 0, width, height);
                    var dataUrl = canvas.toDataURL('image/jpeg');
                    var resizedImage = dataURLToBlob(dataUrl);

                    
                    // $.event.trigger({
                    //     type: "imageResized",
                    //     blob: resizedImage,
                    //     url: dataUrl
                    // });
                }
                image.src = readerEvent.target.result;

                // recreating the image
                $('#'+picContainerId).attr('src', image.src);
            }
            reader.readAsDataURL(file);
        }
    }

    // Read in file
    // var file = event.target.files[0];

    
};

/* Utility function to convert a canvas to a BLOB */
var dataURLToBlob = function(dataURL) {
    var BASE64_MARKER = ';base64,';
    if (dataURL.indexOf(BASE64_MARKER) == -1) {
        var parts = dataURL.split(',');
        var contentType = parts[0].split(':')[1];
        var raw = parts[1];

        return new Blob([raw], {type: contentType});
    }

    var parts = dataURL.split(BASE64_MARKER);
    var contentType = parts[0].split(':')[1];
    var raw = window.atob(parts[1]);
    var rawLength = raw.length;

    var uInt8Array = new Uint8Array(rawLength);

    for (var i = 0; i < rawLength; ++i) {
        uInt8Array[i] = raw.charCodeAt(i);
    }

    return new Blob([uInt8Array], {type: contentType});
}
/* End Utility function to convert a canvas to a BLOB      */


// function processImage(picContainerId, input) {
//         var reader = new FileReader();
//         reader.onload = function(e) {
//             $('#'+picContainerId).attr('src', e.target.result);
//         }
//         reader.readAsDataURL(input.files[0]);
// }

$(document).ready(function() {


    // Scrren width mobile resolution 
    // $(".datatable table tbody tr td:first-child").click(function(){
    //     console.log('td clicked');
    //     if($(this).parent("tr").hasClass("show")){
    //         $(this).parent("tr").removeClass("show");
    //     } else {
    //         $(".datatable table tbody tr").removeClass("show");
    //         $(this).parent("tr").addClass("show");
    //     }
    // });

    $(document).on('click', '.datatable table tbody tr td:first-child', function() {
        console.log('td clicked');
        if($(this).parent("tr").hasClass("show")){
            $(this).parent("tr").removeClass("show");
        } else {
            $(".datatable table tbody tr").removeClass("show");
            $(this).parent("tr").addClass("show");
        }
    })
    

    $('#upload_profile_pic').click(function() {
        console.log('click deetcted');
        $("input[id='avatar_file']").click();
    });
    $('#avatar_file').change(function() {
        console.log('change detected', this);
        processImage('profile_pic_container', this);
    });

    /* Handle image resized events */
    $(document).on("imageResized", function (event) {
        var data = new FormData($("form[id*='uploadImageForm']")[0]);
        if (event.blob && event.url) {
            data.append('image_data', event.blob);

            // $.ajax({
            //     url: event.url,
            //     data: data,
            //     cache: false,
            //     contentType: false,
            //     processData: false,
            //     type: 'POST',
            //     success: function(data){
            //     //handle errors...
            //     }
            // });
        }
    });
});
</script>
<header class="dashHeader">
    <div class="headerLeft">
        <a href=""><img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/logo.png" alt=""></a>
    </div>
    <div class="headerRight">
        <div class="heaverNav">
            <ul>
                <li>
                    <a href="">
                        <span class="desktopOnly">SEARCH DOMAIN</span>
                        <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/search.png" alt="SEARCH DOMAIN" class="mobileOnly">
                    </a>
                </li>
                <li>
                    <a href="">
                        <span class="desktopOnly">UNLOCKED LEADS</span>
                        <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_unlocked_leads_mobile.png" alt="UNLOCKED LEADS" class="mobileOnly">
                    </a>
                </li>
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
                <div class="closeMenu"></div>
                <div class="profilePic">
                    <a href="" class="changePic"><img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_camera_green.png" alt="change picture"></a>
                    <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/profilePic.png" alt="">
                </div>
                <div class="profileMenuBody">
                    <div class="profileName">
                        <p>{{isset($user) && strlen(trim($user->name)) > 0 ? strtoupper($user->name) : 'USER'}}</p>
                        <span>john.doe@tier5.us</span>
                    </div>
                    <ul>
                        <li><a href="{{route('profile')}}">Profile</a></li>
                        <li><a href="{{route('logout')}}">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</header>
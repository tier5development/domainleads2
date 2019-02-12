
<ul class={{config('settings.ADMIN-NUM') == $user->user_type ? 'admin-user' : 'normal-user'}}>
    <li>
        <a href="{{route('search')}}">
            <span class="desktopOnly">SEARCH DOMAIN</span>
            <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/search.png" alt="SEARCH DOMAIN" class="mobileOnly">
        </a>
    </li>
    @if($user->user_type <= config('settings.PLAN.L1'))
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

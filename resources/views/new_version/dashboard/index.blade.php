<!DOCTYPE html>
<html lang="en">
    @include('new_version.section.user_panel_head', ['title' => 'Domainleads Search'])
<body>
    @include('new_version.shared.loader')
    <div class="container">

        @include('new_version.section.user_panel_header', ['user' => $user])
        <section class="mainBody">
            {{-- Include common dashboard right panel --}}
            @include('new_version.shared.right-panel')

            <div class="leftPanel">
                <div class="leftPanelHeader">
                    <div class="clientImg">
                        @if(strlen($user->profile_image_icon) > 0)
                            {!! $user->profile_image_icon !!}
                        @else
                            <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/dl_default_user_pic_40x40.png" alt="">
                        @endif
                    </div>
                    <div class="clientInfo">
                        <h1>Hey {{ucwords(strtolower(explode(' ', $user->name)[0]))}},</h1>
                        <p>Welcome to Domain Leads. Start your domain search right here.</p>
                    </div>
                </div>
                {{-- Search form lies here --}}
                @include('new_version.search.search-form')
            </div>

            {{-- Include footer --}}
            @include('new_version.shared.dashboard-footer-mobile')
        </section>
        
        {{-- Include footer --}}
        @include('new_version.shared.dashboard-footer')
    </div>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/custom2.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/right-panel.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/common.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>
</body>
</html>
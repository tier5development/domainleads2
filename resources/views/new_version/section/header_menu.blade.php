<header class="header">
    <div class="container customHeadWidth">
      <div class="row">
        <div class="col-xs-3">
          <div class="logo">
                <a href="{{route('home')}}">
                <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/logo.png" alt="logo">
            </a>
          </div>
        </div>
        <a href="javascript:void(0);" class="menu-button"><i class="fa fa-bars"></i></a>

        <div class="col-xs-9 bottomRight">
            @if(isset($show_no_icons) && $show_no_icons == true)
                <!-- Show no icons -->
            @else
                <nav class="menu clearfix">
                    <a href="javascript:void(0);" class="menuClose"><i class="fas fa-arrow-right"></i></a>
                    <ul>
                        <li><a href="#leadconversion" class="lead-conversion">lead conversion</a></li>
                        <li><a href="#pricing" class="pricing">pricing</a></li>
                        <li><a href="{{route('supportPage')}}" class="pricing">support</a></li>
                        @if(Auth::check() && isset($user))
                            {{-- <a href="{{route('search')}}" class="button gradiant-orange">dashboard</a> --}}
                            @include('new_version.shared.user-settings-dropdown', ['user' => $user])
                        @else
                            <a href="{{route('loginPage')}}" class="button gradiant-orange">login</a>
                        @endif
                        
                    </ul>
                </nav>
            @endif
        </div>
      </div>
    </div>

    <script type="text/javascript">
        var locationTLD = "{{config('settings.LOCATION_TLD')}}"
        $(".pricing").click(function() {
            var urltld = window.location.href.split(locationTLD)
            if(urltld[1] == '') {
                // do nothing
            } else {
                window.location.replace("{{config('settings.LANDING-DOMAIN')}}"+"#pricing")
            }
        });
        $(".lead-conversion").click(function() {
            var urltld = window.location.href.split(locationTLD)
            if(urltld[1] == '') {
                // do nothing
            } else {
                window.location.replace("{{config('settings.LANDING-DOMAIN')}}"+"#leadconversion")
            }
        });
    </script>
  </header>
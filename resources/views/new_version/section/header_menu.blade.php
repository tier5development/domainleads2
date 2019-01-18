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
                        <li><a href="#">lead conversion</a></li>
                        <li><a href="#">pricing</a></li>
                        <a href="{{route('loginPage')}}" class="button gradiant-orange">login</a>
                    </ul>
                </nav>
            @endif
        </div>
      </div>
    </div>
    
  </header>
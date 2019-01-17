
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">Domain Leads</a>
    </div>
    <ul class="nav navbar-nav">
      <li @if (Request::path() == '/') class="active" @endif><a href="{{ URL::to('/') }}">Home</a></li>
      @if(\Auth::user()->user_type == config('settings.ADMIN-NUM'))
      <li @if (Request::path() == 'UserList') class="active" @endif><a href="{{ route('UserList') }}"> User`s List</a></li>
      <li @if (Request::path() == 'importExport') class="active" @endif><a href="{{ URL::to('importExport') }}"> Import CSV</a></li>
      @endif
      <li @if (Request::path() == 'search') class="active" @endif>
      <a href="{{ URL::to('search') }}"> Search Domain</a></li>
      
      @if(\Auth::user()->user_type < 3)
        <li>
          <a href="{{route('myUnlockedLeads')}}">Unlocked Leads</a>
        </li>
      @endif
      
      
        {{--
      @if(\Auth::user()->membership_status == 0)
        <li class="pull-right"><a href="#">free membership</a></li>
      @elseif(\Auth::user()->membership_status == 1)
        <li class="pull-right"><a href="#">basic membership</a></li>
      @elseif(\Auth::user()->membership_status == 2)
        <li class="pull-right"><a href="#">premium membership</a></li>
      @else
        <li class="pull-right"><a href="#">advanced membership</a></li>
      @endif
       <!--  <li class="pull-right"><a href="#">Notifications</a></li> -->
          --}}
      @if(\Auth::user()->user_type == config('settings.ADMIN-NUM'))
        <li ><a href="{{url('/')}}/manage">Manage</a></li>
      @endif

      @if(\Auth::user())
        <li ><a href="{{route('changePassword')}}">Profile</a></li>
      @endif

        {{-- <li class="pull-right"><a href="https://www.textinbulk.com/">Text In Bulk</a></li> --}}
      <li class="pull-right"> <a href="{{ URL::to('logout') }}">Logout</a></li>
    </ul>
  </div>
</nav>

<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">Domain Leads</a>
    </div>
    <ul class="nav navbar-nav">
      <li @if (Request::path() == '/') class="active" @endif><a href="{{ URL::to('/') }}">Home</a></li>
      <li @if (Request::path() == 'importExport') class="active" @endif><a href="{{ URL::to('importExport') }}"> Import CSV</a></li>
      <li @if (Request::path() == 'postSearchData') class="active" @endif><a href="{{ URL::to('postSearchData') }}"> Search Domain</a></li>
      <li> <a href="{{ URL::to('logout') }}">Logout</a></li>
      

      @if(\Auth::user()->membership_status == 0)
        <li class="pull-right"><a href="#">free membership</a></li>
      @elseif(\Auth::user()->membership_status == 1)
        <li class="pull-right"><a href="#">basic membership</a></li>
      @elseif(\Auth::user()->membership_status == 2)
        <li class="pull-right"><a href="#">premium membership</a></li>
      @else
        <li class="pull-right"><a href="#">advanced membership</a></li>
      @endif
    </ul>
  </div>
</nav>
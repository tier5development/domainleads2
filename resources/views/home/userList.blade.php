<html lang="en">
@include('layouts.header')
<head>
<title>User List</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" >
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<script type="text/javascript" src="{{url('/')}}/theme/js/bootstrap.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>
<body>
  <style>
    .overlay{background: rgba(0,0,0,0.7); width: 100%; height: 100%; position: fixed; top: 0;
         z-index: 1111;
         }
         .loader-main{width: 100px; height: 100px; position: absolute; margin-left: -50px; margin-top: -50px; top: 50%; left: 50%;}
         .loader-main img{max-width: 100%;}
         .applyBtn{
           float: right;
           margin-top: 2px;
         }
         .refreshBtn{
           margin-top: 2px;
         }
         .perPage label{
           float: left;
           margin-top: 6px;
         }
         .perPage select{
           width: calc(100% - 80px);
           float: right;
         }
  </style>

  <div id="ajax-loader" style="display: none;">
    <div class="overlay">
        <div class="loader-main">
        <img src="{{url('/')}}/images/loader.gif">
        </div>
    </div>
  </div>

<div class="container">
    <div>
				@if(Session::has('error'))
					<div class="alert alert-danger fade in alert-dismissible" style="margin-top:18px;">
						<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
						<strong>Error!</strong> {{Session::get('error')}}
					</div>
				@endif
				@php Session::forget('error') @endphp
		</div>
  {{-- <div class="col-md-2">
    
  </div> --}}
  
  <form action="{{route('UserList')}}" method="GET">
  <div class="col-md-2">
      <select name="usertype" id="" class="form-control">
        @foreach($userTypes as $key => $type)
          <option {{\Request::get('usertype') == $type ? 'selected' : ''}} value="{{$key == 0 ? '' : $type}}">{{$type}}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3 perPage">
        <label>Per-Page : </label>
        <select name="perpage" id="" class="form-control">
          @foreach($perpageset as $key => $pagetotal)
            <option {{\Request::get('perpage') == $pagetotal ? 'selected' : ''}} value="{{$key == 0 ? '' : $pagetotal}}">{{$pagetotal}}</option>
          @endforeach
        </select>
      </div>
  <div class="search form-group col-md-4 pull-right">    
      <div class="row">
        <div class="col-md-8">
          <input value="{{Request::get('search')}}" name="search" class="form-control" placeholder="search">
        </div>
        {{csrf_field()}}
        <div class="applyBtn">
            <button type="submit" class="btn btn-sm btn-info float-right">Apply</button>
        </div>
        <a class="btn btn-sm btn-success refreshBtn" href="{{route('UserList')}}">Refresh</a>
      </div>
  </div>
</form><br>

<div class="col-md-12">
<b class="pull-left"><h4>Users : {{$users->count()}}</h4></b><br>
<table class="table table-hover table-bordered">
  <thead class="thead-inverse">
    <tr>
      <th>Name</th>
      <th>Email</th>
      <th>Suspend Status</th>
      <th>Delete</th>
      <th>Created</th>
    </tr>
  </thead>
  <tbody>
  @foreach($users as $key => $eachUser)
      <tr id="row_{{$eachUser->id}}" style="background-color:mintcream" >
      {{-- <th scope="row">{!! $key + 1 !!}</th> --}}
      <td>{!! $eachUser->name!!}</td>
      <td id="email_{{$eachUser->id}}">{!! $eachUser->email!!}</td>
      <td>
          @if(strpos($eachUser->email, '_suspended'))
            <button onclick="suspend_api('{{$eachUser->id}}', this)" class="btn btn-sm btn-warning">Unsuspend</button>
          @else
            <button onclick="suspend_api('{{$eachUser->id}}', this)" class="btn btn-sm btn-primary">Suspend</button>
          @endif
      </td>
      <td>
        <button onclick="deleteUser('{{$eachUser->id}}', this)" class="btn btn-sm btn-danger">Delete</button>
      </td>
      <td>{!! date('F jS, Y', strtotime($eachUser->created_at))!!}</td>
    </tr>
  @endforeach
  </tbody>
</table>


<b class="pull-left" style="margin-top: 10px;"><h4>Total Users : {{$users->total()}}</h4></b>
<div class="pull-right">
    {{$users->appends([
      'search' => \Request::has('search') ? \Request::get('search') : null,
      'usertype' => \Request::has('usertype') ? \Request::get('usertype') : null,
      'perpage' => \Request::has('perpage') ? \Request::get('perpage') : null
    ])->links()}}
</div>

</div>

</div>
</body>
<br><br>
<script type="text/javascript">
  $(document).ready(function() {
    console.log('ready');
  });

  function deleteUser(id , t) {
    $.ajax({
      url : "{{route('deleteUserPost')}}",
      type : "POST",
      data : {_token : "{{csrf_token()}}", id:id},
      beforeSend: function() {

      }, success : function(r) {
        console.log(r);
        if(r.status) {
          swal({
            title: "Successful",
            text: r.message,
            icon: "success",
            button: "OK",
          }).then(function() {
            $('#row_'+id).remove();

          });
        } else {
          swal({
            title: "Oops! Something went wrong..",
            text: r.message,
            icon: "warning",
            button: "OK",
          })
        }
      }, error : function(e) {
        console.info('delete', e);
      }
    });
  }

  function suspend_api(id, t) {
    action = $(t).text();
    console.log(id, action);
    $.ajax({
      url   : "{{route('suspendOrUnsuspendUser')}}",
      type  : "POST", 
      data  : {_token: "{{csrf_token()}}", id: id},
      beforeSend : function() {
        //$('#ajax-loader').show();
      }, success : function(r) {
        //$('#ajax-loader').hide();
        if(r.status) {
          swal({
            title: "Successful",
            text: r.message,
            icon: "success",
            button: "OK",
          }).then(function() {
            action == 'Unsuspend' 
                      ? $(t).removeClass('btn-warning').addClass('btn-primary').text('Suspend')  
                      : $(t).removeClass('btn-primary').addClass('btn-warning').text('Unsuspend');
            if(r.email !== undefined && r.email !== null)  {
              $('#email_'+id).text(r.email);
            }
          });
        } else {
          swal({
            title: "Oops! Something went wrong..",
            text: r.message,
            icon: "warning",
            button: "OK",
          });
          console.log(r);
        }
      }, error : function(e) {
        //$('#ajax-loader').hide();
        console.info(e);

        swal({
            title: "Oops! Something went wrong..",
            text: e,
            icon: "warning",
            button: "OK",
          });
      }
    });
  }
</script>

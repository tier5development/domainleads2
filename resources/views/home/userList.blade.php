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
  </style>

  <div id="ajax-loader" style="display: none;">
    <div class="overlay">
        <div class="loader-main">
        <img src="{{url('/')}}/images/loader.gif">
        </div>
    </div>
  </div>

<div class="container">
<div class="col-md-12">
<table class="table table-hover table-bordered">
  <thead class="thead-inverse">
    <tr>
      <th>#</th>
      <th>Name</th>
      <th>Email</th>
      <th>Suspend Status</th>
      <th>Delete</th>
      <th>Created</th>
    </tr>
  </thead>
  <tbody>
  @foreach($User as $key => $eachUser)
    <tr style="background-color:{{$eachUser->deleted_at != null ? '#f47a42' :'mintcream'}}"  >
      <th scope="row">{!! $eachUser->id!!}</th>
      <td>{!! $eachUser->name!!}</td>
      <td id="email_{{$eachUser->id}}">{!! $eachUser->email!!}</td>
      <td>
        @if($eachUser->deleted_at == null)
          @if(strpos($eachUser->email, '_suspended'))
            <button onclick="suspend_api('{{$eachUser->id}}', this)" class="btn btn-sm btn-warning">Unsuspend</button>
          @else
            <button onclick="suspend_api('{{$eachUser->id}}', this)" class="btn btn-sm btn-primary">Suspend</button>
          @endif
        @endif
      </td>
      <td>
          @if($eachUser->deleted_at != null)
            <button class="btn btn-sm btn-success">Restore User</button>
          @else
            <button class="btn btn-sm btn-danger">Delete</button>
          @endif
      </td>
      <td>{!! $eachUser->created_at!!}</td>
      
    </tr>
  @endforeach
  </tbody>
</table>
</div>
</div>
</body>
<script type="text/javascript">
  $(document).ready(function() {
    console.log('ready');
  });

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

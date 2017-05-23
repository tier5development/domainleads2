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
</head>
<body>
  


<div class="container">
<div class="col-md-12">
<table class="table table-hover table-bordered">
  <thead class="thead-inverse">
    <tr>
      <th>#</th>
      <th>Name</th>
      <th>Username</th>
      <th>Created</th>
    </tr>
  </thead>
  <tbody>
  @foreach($User as $eachUsers)
    <tr>
      <th scope="row">{!! $eachUsers->id!!}</th>
      <td>{!! $eachUsers->name!!}</td>
      <td>{!! $eachUsers->email!!}</td>
      <td>{!! $eachUsers->created_at!!}</td>
      
    </tr>
  @endforeach
  </tbody>
</table>
</div>
</div>
</body>

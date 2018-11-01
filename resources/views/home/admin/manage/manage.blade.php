<html lang="en">
@include('layouts.header')
<head>
<title>Manage CSV</title>
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

         .createUserModal{
           position: fixed;
           width: 100%;
           height: 100%;
           background: rgba(0,0,0,0.7);
           z-index: 999;
           top: 0;
           left: 0;
         }
         .modalContainer{
           max-width: 400px;
           margin: 0 auto;
           box-sizing: border-box;
           padding: 15px;
           background: #fff;
           border-radius: 5px;
           position: relative;
           top: 50%;
           transform: translateY(-50%);
           box-shadow: 0 0 15px rgba(0,0,0,1);
         }
         .clear{
           clear: both;
         }
         .modalContainer h2{
           background: #f2f2f2;
           border-bottom: 1px solid #ccc;
           box-sizing: border-box;
           padding: 15px;
           font-size: 18px;
           margin: -15px -15px 20px -15px;
           border-radius: 5px 5px 0 0;
           text-align: center;
         }
         .modalContainer .close{
           position: absolute;
           top: -10px;
           right: -10px;
           width: 25px;
           height: 25px;
           border-radius: 15px;
           background: #ff3b3b;
           z-index: 999;
           opacity: 1;
           color: #fff;
           text-shadow: none;
           font-size: 14px;
           text-align: center;
           padding-top: 4px;
           cursor: pointer;
         }
         td{
           font-size: 15px;
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
          @php Session::forget('error') @endphp
				@elseif(Session::has('success'))
          <div class="alert alert-success fade in alert-dismissible" style="margin-top:18px;">
						<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
						<strong>Success!</strong> {{Session::get('success')}}
          </div>
          @php Session::forget('success') @endphp
        @endif
		</div>
  {{-- <div class="col-md-2">
    
  </div> --}}
  
  <form action="{{route('manage')}}" method="GET">
    <div class="col-md-2">
      <select name="filetypeselected" id="" class="form-control">
        @foreach($importTypes as $key => $type)
          <option {{\Request::get('filetypeselected') == $type ? 'selected' : ''}} value="{{$key == 0 ? '' : $type}}">{{$key == 0 ? 'Select' : $type}}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3 perPage">
      <label>Per-Page : </label>
      <select name="perpage" id="" class="form-control">
        @foreach($perpageset as $key => $pagetotal)
          <option {{\Request::get('perpage') == $pagetotal ? 'selected' : ''}} value="{{$key == 0 ? '' : $pagetotal}}">{{$key == 0 ? 'Select' : $pagetotal}}</option>
        @endforeach
      </select>
    </div>
    <div class="search form-group col-md-4 pull-right">    
        <div class="row">
          <div class="col-md-8">
            <input value="{{Request::get('search')}}" name="search" class="form-control" placeholder="search file names">
          </div>
          {{csrf_field()}}
          <div class="applyBtn">
              <button type="submit" class="btn btn-sm btn-info float-right">Apply</button>
          </div>
          <a class="btn btn-sm btn-success refreshBtn" href="{{route('manage')}}">Refresh</a>
        </div>
    </div>
  </form>
  <br>

<div class="col-md-12">
  <div class="tabHeadRow">
      <b class="pull-left"><h4>Data : {{$data->count()}}</h4></b>
      {{-- <button class="btn btn-sm btn-info pull-right createUserButton">Create User</button> --}}
  </div>

  <table class="table table-hover table-bordered">
    <thead class="thead-inverse">
      <tr>
        <th>File name</th>
        <th>Insert Time</th>
        <th>Total Leads</th>
        <th>Total Domains</th>
        <th>Insertion Status</th>
        <th>Insert Date</th>
      </tr>
    </thead>
    <tbody>
    @foreach($data as $key => $eachData)
        <tr style="background-color:white" >
        {{-- <th scope="row">{!! $key + 1 !!}</th> --}}
        <td>{!! $eachData->file_name!!}</td>
        <td>{!! gmdate("H:i:s", $eachData->query_time)!!}</td>
        <td>{!! $eachData->leads_inserted !!}</td>
        <td>{!! $eachData->domains_inserted !!}</td>
        <td>{{$eachData->status == 0 ? 'Failed' : $eachData->status == 1 ? 'Ongoing' : 'Complete'}}</td>
        <td>{!! date('F jS, Y', strtotime($eachData->created_at))!!}</td>
      </tr>
    @endforeach
    </tbody>
  </table>


  <b class="pull-left" style="margin-top: 10px;"><h4>Total Data : {{$data->total()}}</h4></b>
  <div class="pull-right">
      {{$data->appends([
        'search' => \Request::has('search') ? \Request::get('search') : null,
        'perpage' => \Request::has('perpage') ? \Request::get('perpage') : null,
        'filetype' => \Request::has('filetype') ? \Request::get('filetype') : null
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
</script>

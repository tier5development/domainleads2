<html lang="en">
@include('layouts.header')
<head>
    @include('layouts.universal-styles',['title' => $title])
    <link rel="stylesheet" href="{{url('/')}}/public/css/style.css">
</head>
<body>
    <div class="tableContainer"> 



    <p class="tableHeading">All leads unlocked </p>

    <u class="totalPage">Total count : {{$leads->total()}}</u>
    <form class="" action="{{route('myUnlockedLeadsPost')}}" method="POST">
        <br>
            <p class="perPage">
                Per Page 
                <select name="perpage">
                    <option {{$perpage == 10 ? 'selected' : null}} value="10">10</option>
                    <option {{$perpage == 20 ? 'selected' : null}} value="20">20</option>
                    <option {{$perpage == 50 ? 'selected' : null}} value="50">50</option>
                    <option {{$perpage == 100 ? 'selected' : null}} value="100">100</option>
                </select>
            </p>
            <button class="btn btn-sm btn-primary searchBtn" id="search">Apply</button>
            <input type="date" name="date" value="{{Request::has('date') ? Request::get('date') : null}}" class="dateInp">
            {{csrf_field()}}
    </form><br><br>

    @if(count($leads) > 0 )
    <form method="POST" action="{{route('downloadUnlockedLeads')}}">
        <input type="hidden" name="date" value="{{Request::has('date') ? Request::get('date') : null}}">
        <button type="submit" class="btn btn-info btn-sm pull-right">Download CSV</button>
        {{csrf_field()}}
    </form><br>
    @endif
    
    <div style="display: none" id="spinner" class="spinner"></div>

    <h1 style="display: none" id="processing">Some text if needed to show in runtime</h1>
    
        <div id="content_div">
            <table class="uni_table" align="center">
                <tr>
                    <th>Sl no</th>
                    <th>Lead</th>
                    <th>Country</th>
                    <th>Registrant Name</th>
                    <th>Domain Name</th>
                    <th>Phone</th>
                    <th>Date of unlock</th>
                    
                </tr>
                @foreach($leads as $i => $eachLead)
                <tr>
                    @php $page = Request::has('page') ? Request::get('page') : 1  @endphp
                    <td>{{($i+1) + (($page-1)*$perpage)}}</td>
                    <td>
                        @if($eachLead->lead != null)
                            <span>{{$eachLead->registrant_email}}</span>
                            <br>
                            <small> Total Domains : <a href="{{url('/')}}/lead/{{encrypt($eachLead->registrant_email)}}">{{$eachLead->lead->each_domain->count()}}</a></small>
                        @else
                            <span style="color : red">{{$eachLead->registrant_email}}</span>
                        @endif
                    </td>
                    <td>
                        <small>{{$eachLead->registrant_country}}</small>
                    </td>
                    <td>
                        <small>{{$eachLead->registrant_fname}}&nbsp;{{$eachLead->registrant_lname}}</small>
                    </td>
                    <td>
                        <small>{{$eachLead->domain_name}}</small>
                    </td>
                    <td>
                            
                        @if($eachLead->lead)
                        @php 
                            $phone = $eachLead->lead->registrant_phone; 
                            $phoneType = $eachLead->lead->valid_phone ? $eachLead->lead->valid_phone->number_type : null;
                        @endphp
                            <small class="telNumber">{{$phone}}</small>
                            <div class="teleIco">
                                @if($phoneType && $phoneType == "Cell Number" || $phoneType == "Landline" )
                                    @if($phoneType == "Cell Number")
                                        <img id="phone_{{$i}}" style="width:20px; height:40px" src="{{url('/')}}/images/phone.png">
                                    @elseif($phoneType == "Landline")
                                        <img id="phone_{{$i}}" style="width:30px; height:40px" src="{{url('/')}}/images/landline.png">
                                    @endif
                                @endif
                            </div>
                        @else
                        @endif
                        
                    </td>
                    <td>
                        <small>{{$eachLead->created_at}}</small>
                    </td>
                    
                </tr>
                @endforeach
            </table>
            @if($leads->count() > 0)
            <div class="paginate">
                {{$leads->appends(['date' => Request::has('date') ? Request::get('date') : null,
                'perpage' => Request::has('perpage') ? Request::get('perpage') : 20])->links()}}
            </div>
            @endif
        </div>
    </div>
</body>
</html>
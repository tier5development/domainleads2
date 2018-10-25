
<table class="table table-hover table-bordered domainDAta">
    <tr>
        <th>Unlock Lead</th>
        <th>Domain Name</th>
        <th>Country</th>
        <th>Registrant Name</th>
        <th>Registrant Email</th>
        <th>Registrant Phone</th>
        <th>Domains Create Date</th>
        <th>Registrant Company</th>
    </tr>

    
    @foreach($record as $key=>$each)
    <tr>
        <th>
            {{-- @if(isset($users_array[$each['registrant_email']]))
                <input type="checkbox" id="ch_{{$key}}" onclick="unlock('{{$each['registrant_email']}}' , '{{$key}}')" name="ch_{{$key}}" checked="true" disabled="true">
                <input type="hidden" id="leads_id_{{$key}}"  class="leads_id" value="{{$each['id']}}">
            @else
                <input type="checkbox" id="ch_{{$key}}" onclick="unlock('{{$each['registrant_email']}}' , '{{$key}}')" name="ch_{{$key}}">	
                <input type="hidden" id="leads_id_{{$key}}"  class="leads_id" value="">
            @endif --}}

            @if(isset($users_array[$each['registrant_email']]))
                <button id="ch_{{$key}}" onclick="unlock('{{$each['registrant_email']}}' , '{{$key}}')" name="ch_{{$key}}" disabled="true" class="btn btn-sm btn-success">Unlocked</button>
                <input type="hidden" id="leads_id_{{$key}}"  class="leads_id" value="{{$each['id']}}">
            @else
                <button id="ch_{{$key}}" onclick="unlock('{{$each['registrant_email']}}' , '{{$key}}')" name="ch_{{$key}}" class="btn btn-sm btn-primary">Unlock</button>	
                <input type="hidden" id="leads_id_{{$key}}"  class="leads_id" value="">
            @endif
        </th>

        <th>
            @if(isset($users_array[$each['registrant_email']]))
                <small>
                 <b data-domainname="{{$each['domain_name']}}" id="domain_name_{{$key}}">{{$each['domain_name']}}</b></small>
            @else
                <small data-domainname="{{$each['domain_name']}}" id="domain_name_{{$key}}">***</small>
            @endif
            <br>
            <small> Unlocked Num : <span id="unlocked_num_{{$key}}">{{$each['unlocked_num']}}</span></small>
            <br>
            <small> Total Domains : <a href="javascript:void(0)" onclick="clickLink(this, {{$key}})" id="linkClick_{{$key}}" data-ref = "{{route('viewDomainsOfUnlockedLeed', ['email' => encrypt($each['registrant_email']), 'request' => Session::has('oldReq') ? Session::get('oldReq') : null])}}">{{$each['domains_count']}}</a></small> 
        </th>

        <th>
            @if(isset($users_array[$each['registrant_email']]))
                <small id="country_{{$key}}">{{$each['registrant_country']}}</small>
            @else
                <small id="country_{{$key}}">***</small>
            @endif
        </th>

        <th>
            @if(isset($users_array[$each['registrant_email']]))
                <small id="registrant_name_{{$key}}">{{$each['registrant_name']}}</small>
            @else
                <small id="registrant_name_{{$key}}">***</small>
            @endif
        </th>

        <th>
            @if(isset($users_array[$each['registrant_email']]))
                <small id="registrant_email_{{$key}}">{{$each['registrant_email']}}</small>
            @else
                <small id="registrant_email_{{$key}}">***</small>
            @endif
        </th>

        <th>
            @if(isset($users_array[$each['registrant_email']]))	
		
                <small id="registrant_phone_{{$key}}">{{$each['registrant_phone']}}</small>

                @if(isset($each['number_type']) && ($each['number_type'] == "Cell Number" || $each["number_type"] == "Landline" )) 
                    @if($each['number_type'] == "Cell Number")
                    <img id="phone_{{$key}}" style="width:20px; height:40px" src="{{url('/')}}/images/phone.png">

                    @elseif($each['number_type'] == "Landline")
                    <img id="phone_{{$key}}" style="width:30px; height:40px" src="{{url('/')}}/images/landline.png">

                    @endif
                @endif
            @else
		
                <small id="registrant_phone_{{$key}}">***</small>
                @if(isset($each['number_type']) && ($each["number_type"] == 'Cell Number' || $each["number_type"] == "Landline" ))
                    @if($each['number_type'] == "Cell Number")
                    <img  id="phone_{{$key}}" style="width:20px; height:40px; display:none" src="{{url('/')}}/images/phone.png">
                    
                    @elseif($each['number_type'] == "Landline")
                    <img id="phone_{{$key}}" style="width:30px; height:40px; display:none" src="{{url('/')}}/images/landline.png">
                    @endif
                @endif
            @endif
            <br>
        </th>

        <th>
            @if(isset($users_array[$each['registrant_email']]))
                <small id="domains_create_date_{{$key}}">{{date('F dS, Y', strtotime($each["domains_create_date"]))}}</small>
            @else
                <small id="domains_create_date_{{$key}}">***</small>
            @endif
        </th>

        <th>
            @if(isset($users_array[$each['registrant_email']]))
                <small id="registrant_company_{{$key}}">{{$each['registrant_company']}}</small>
            @else
                <small id="registrant_company_{{$key}}">***</small>
            @endif
        </th>
    </tr>
    @endforeach
</table>

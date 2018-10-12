<table class="table table-hover table-bordered domainDAta">
    <tr>
        <th>Check box</th>
        <th>Create Website</th>
        <th>Domain Name</th>
        <th>Registrant Name</th>
        <th>Registrant Email</th>
        <th>Registrant Phone</th>
        <th>Domains Create Date</th>
        <th>Registrant Company</th>
    </tr>

    
    @foreach($record as $key=>$each)
    <tr>

        <th>
            @if(isset($users_array[$each['registrant_email']]))
                <input type="checkbox" id="ch_{{$key}}" onclick="unlock('{{$each['registrant_email']}}' , '{{$key}}')" name="ch_{{$key}}" checked="true" disabled="true">
                <input type="hidden" id="leads_id_{{$key}}"  class="leads_id" value="{{$each['id']}}">
            @else
                <input type="checkbox" id="ch_{{$key}}" onclick="unlock('{{$each['registrant_email']}}' , '{{$key}}')" name="ch_{{$key}}">	
                <input type="hidden" id="leads_id_{{$key}}"  class="leads_id" value="">
            @endif
        </th>

        <th>
            {{-- @if(isset($chkWebsite_array[$each['registrant_email']]))
                <button class="btn btn-primary" id="chkDomainForWebsiteID_{{$key}}" onclick="chkDomainForWebsite('{{$domain_list[$each['registrant_email']]['domain_name']}}','{{$key}}','{{$each['registrant_email']}}')" disabled="true">Created website</button>
            @else
                <button class="btn btn-primary" id="chkDomainForWebsiteID_{{$key}}" onclick="chkDomainForWebsite('{{$domain_list[$each['registrant_email']]['domain_name']}}','{{$key}}','{{$each['registrant_email']}}')" >Create website</button>
            @endif --}}
            
            @if(isset($users_array[$each['registrant_email']]))
                <input type="checkbox" name="downloadcsv" value="1" class="eachrow_download" id="eachrow_download_{{$key}}" emailID="{{$each['registrant_email']}}"  @if(in_array($each['registrant_email'], $emailID_list)) {{'checked'}}  @endif >
            @else
                <small id="showCSV_{{$key}}" style="display: none"><input type="checkbox" name="downloadcsv" value="1" class="eachrow_download" id="eachrow_download_{{$key}}" emailID="{{$each['registrant_email']}}" <?php if(in_array($each['registrant_email'], $emailID_list)){ echo "checked";} ?>>
                </small>
                <small id="hideCSV_{{$key}}">***</small>
            @endif
        </th>

        <th>
            @if(isset($users_array[$each['registrant_email']]))
                <small>
                 <b id="domain_name_{{$key}}">{{$each['domain_name']}}</b></small>
            @else
                <small id="domain_name_{{$key}}">***</small>
            @endif
            <br>
            <small> Unlocked Num : <span id="unlocked_num_{{$key}}">{{$each['unlocked_num']}}</span></small>
            <br>
            <small > Total Domains : <a href="{{url('/')}}/lead/{{encrypt($each['registrant_email'])}}">{{$each['domains_count']}}</a></small> 
            <!-- leadArr[$each->registrant_email] -->
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

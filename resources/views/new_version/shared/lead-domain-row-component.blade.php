    {{-- 
        * Input : each -> Each record array returned from domain search
        (called from search-results-table file and also ajax call when a lead is unlocked)

        * Table headers structure prototype
        <tr>
            <th>DOMAIN<br>NAME</th>
            <th>REGISTRANT<br>NAME, EMAIL</th>
            <th>REGISTRANT<br>PHONE</th>
            <th>CREATED<br>DATE</th>
            <th>EXPIRY<br>DATE</th>
            <th>REGISTRANT<br>COMPANY</th>
            <th></th>
        </tr>
    --}}
    @php
        $lead = $each->leads; 
        $ph = isset($lead) ? $lead->valid_phone : null;
        $domainsInfo = $each->domains_info; 
    @endphp 
    
    <td>
        @if(!isset($users_array[$each->domain_name]) && $restricted == true)
            <p data-domainname="{{$each->domain_name}}"
                data-restrict="1" 
                id="domain_name_{{$key}}">{{customMaskDomain($each->domain_name)}} 
                <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_view_disable.png" alt="">
            </p>
        @else
            <p class="wordBreak" data-domainname="{{$each->domain_name}}"
                data-restrict="0" 
                id="domain_name_{{$key}}">{{$each->domain_name}}</p>
        @endif
        <div class="leadStatus">
            <p class="locked">
                <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_lock_opened.png" alt="">
                <span>{{$each->unlocked_num}}</span>
            </p>
        </div>
    </td>
        
    <td>
        @if(!isset($users_array[$each->domain_name]) && $restricted == true)
            <div class="encapsulate">
                <span></span>
                <span></span>
                <span></span>
            </div>
        @else
            @if($lead)
                <p>{{$lead->registrant_name}}</p>
                <p class="email wordBreak">
                    <a href="javascript:void(0)">{{$email}}</a>
                </p>
                <p class="country">
                    <!-- TODO -->
                    <img 
                    @if(isset($country_abr) && strlen($country_abr) > 0)
                        src="{{config('settings.APPLICATION-DOMAIN')}}/public/svg/{{$country_abr}}.svg" 
                    @endif
                    alt="" style="width: 18px; height: 18px;">
                    <span>{{$lead->registrant_country}}</span>
                </p>
            @else
                
            @endif
        @endif
    </td>
        
    <td>
        @if(!isset($users_array[$each->domain_name]) && $restricted == true)
            <div class="encapsulate">
                <span></span>
            </div>
        @else
            <p class="phone">
                @if($lead)
                    @if($ph)
                        @if($ph->number_type && strtolower($ph->number_type) == 'cell number')
                            <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_mobile.png" alt="">
                        @elseif($ph->number_type && strtolower($ph->number_type) == 'landline')
                            <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_land_phone.png" alt="">
                        @endif
                    @endif
                    <span>{{str_replace('.', '-', $lead->registrant_phone)}}</span>
                @else

                @endif
            </p>
        @endif
    </td>

    <td>
        <p>
            <span>
                @if($domainsInfo)
                    {{DateTime::createFromFormat('Y-m-d', $domainsInfo->domains_create_date)->format('m-d-Y')}}
                @endif
            </span>
        </p>
    </td>
            
    <td>
        <p>
            <span>
                @if($domainsInfo)
                    {{DateTime::createFromFormat('Y-m-d', $domainsInfo->expiry_date)->format('m-d-Y')}}
                @endif
            </span>
        </p>
    </td>
            
    <td>
        @if(!isset($users_array[$each->domain_name]) && $restricted == true)
            <div class="encapsulate">
                <span></span>
            </div>
        @else
            <p>
                <span class="wordBreak">
                    @if($lead)
                        {{$lead->registrant_company}}
                    @endif
                </span>
            </p>
        @endif
    </td>
            
    <td>
        @if(!isset($users_array[$each->domain_name]) && $restricted == true)
            <button type="button" class="greenBtn unlockBtn" 
                onclick="unlockFromLeads('{{$email}}', '{{$key}}')">
                <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_unclok_whilte.png" alt=""> Unlock
            </button>
        @else
            <p><span></span></p>
        @endif
    </td>
    
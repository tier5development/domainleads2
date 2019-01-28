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
    
    
        <td>
            
            @if(!isset($users_array[$each['registrant_email']]) && $restricted == true)
                <p data-domainname="{{$each['domain_name']}}"
                    data-restrict="1" 
                    id="domain_name_{{$key}}">{{$each['domain_name_masked']}} 
                    <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_view_disable.png" alt="">
                </p>
            @else
                <p class="wordBreak" data-domainname="{{$each['domain_name']}}"
                    data-restrict="0" 
                    id="domain_name_{{$key}}">{{$each['domain_name']}}</p>
            @endif
            <div class="leadStatus">
                <p class="locked">
                    <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_lock_opened.png" alt="">
                    <span>{{$each['unlocked_num']}}</span>
                </p>
                <p class="domains">
                    <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_more_domains.png" alt="">
                    <a 
                     
                    id="linkClick_{{$key}}" 
                    href = "{{route('viewDomainsOfUnlockedLeed', 
                        ['email' => encrypt($each['registrant_email'])])}}"
                    >{{$each['domains_count']}}</a>
                </p>
            </div>
        </td>
        
        <td>
            @if(!isset($users_array[$each['registrant_email']]) && $restricted == true)
                <div class="encapsulate">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            @else
                <p>{{$each['registrant_name']}}</p>
                <p class="email wordBreak">
                    <a href="#">{{$each['registrant_email']}}</a>
                </p>
                <p class="country">
                    <!-- TODO -->
                    <img 
                    @if(isset($country_abr) && strlen($country_abr) > 0)
                        src="{{config('settings.APPLICATION-DOMAIN')}}/public/svg/{{$country_abr}}.svg" 
                    @endif
                    alt="" style="width: 18px; height: 18px;">
                    <span>{{$each['registrant_country']}}</span>
                </p>
            @endif
        </td>
        
        <td>
            @if(!isset($users_array[$each['registrant_email']]) && $restricted == true)
                <div class="encapsulate">
                    <span></span>
                </div>
            @else
                <p class="phone">
                    @if(isset($each['number_type']) && strtolower($each['number_type']) == 'cell number')
                        <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_mobile.png" alt="">
                    @elseif(isset($each['number_type']) && strtolower($each['number_type']) == 'landline')
                        <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_land_phone.png" alt="">
                    @endif
                    <span>{{str_replace('.', '-', $each['registrant_phone'])}}</span>
                </p>
            @endif
        </td>

        <td>
            <p>
                @php
                    $resultDate = DateTime::createFromFormat('d/m/Y', $each['domains_create_date'])->format('m-d-Y');
                @endphp
                <span>{{$resultDate}}</span>
            </p>
        </td>
            
        <td>
            <p>
                @php
                    $resultDate = DateTime::createFromFormat('d/m/Y', $each['expiry_date'])->format('m-d-Y');
                @endphp
                <span>{{$resultDate}}</span>
            </p>
        </td>
            
        <td>
            @if(!isset($users_array[$each['registrant_email']]) && $restricted == true)
                <div class="encapsulate">
                    <span></span>
                </div>
            @else
                <p>
                    <span class="wordBreak">{{$each['registrant_company']}}</span>
                </p>
            @endif
        </td>
            
        <td class="unlock-date-hide">
            @if(!isset($users_array[$each['registrant_email']]) && $restricted == true)
                <button type="button" class="greenBtn unlockBtn" 
                    onclick="unlock('{{$each['registrant_email']}}', '{{$key}}', '{{base64_encode($each['registrant_phone'])}}', '{{base64_encode($each['number_type'])}}')">
                    <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_unclok_whilte.png" alt=""> Unlock
                </button>
            @else
                <p><span></span></p>
            @endif
        </td>
    
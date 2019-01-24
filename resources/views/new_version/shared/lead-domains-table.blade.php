
{{--
    * Input $record
--}}

@php
    $country_codes = country_codes();
@endphp

<div class="datatable" id="search-result-container">

    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th>DOMAIN<br>NAME</th>
                <th>REGISTRANT<br>NAME, EMAIL</th>
                <th>REGISTRANT<br>PHONE</th>
                <th>CREATED<br>DATE<br>(M-D-Y)</th>
                <th>EXPIRY<br>DATE<br>(M-D-Y)</th>
                <th>REGISTRANT<br>COMPANY</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($alldomain as $key=>$each)
            @php
                $country = $each->leads ? $each->leads->registrant_country : null;
                $country_abr = isset($country_codes[ucwords(strtolower($country))]) 
                    ? strtoupper($country_codes[ucwords(strtolower($country))]) : null;
            @endphp
                <tr id="tr_{{$key}}">
                    @include('new_version.shared.lead-domain-row-component', ['each' => $each, 'key' => $key, 'restricted' => $restricted, 'email' => $email, 'country_abr'=> $country_abr])
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
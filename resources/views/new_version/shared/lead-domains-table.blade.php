
{{--
    * Input $record
--}}

<div class="datatable" id="search-result-container">
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th>DOMAIN<br>NAME</th>
                <th>REGISTRANT<br>NAME, EMAIL</th>
                <th>REGISTRANT<br>PHONE</th>
                <th>CREATED<br>DATE</th>
                <th>EXPIRY<br>DATE</th>
                <th>REGISTRANT<br>COMPANY</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($alldomain as $key=>$each)
                <tr id="tr_{{$key}}">
                    @include('new_version.shared.lead-domain-row-component', ['each' => $each, 'key' => $key, 'restricted' => $restricted, 'email' => $email])
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
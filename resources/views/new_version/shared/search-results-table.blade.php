
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
            @foreach ($record as $key=>$each)
                <tr id="tr_{{$key}}">
                    @include('new_version.shared.search-row-component', ['each' => $each, 'key' => $key])
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
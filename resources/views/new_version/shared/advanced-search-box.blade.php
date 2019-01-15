<div class="filterPopup" style="display: none;">
    <form method="POST" action="{{Route('search')}}" class="col-md-6 search_form" id="postSearchDataForm">
        <div class="closeFilterPopup"></div>
        <div class="filterPopupInner">
            <div class="popupLeft">
                <div class="filterFormRow">
                    <label for="">Domain Count</label>
                    <div class="fieldArea">
                        <div class="smallSelectBox">
                            <select class="select">
                                <option value="">Greater then</option>
                            </select>
                        </div>
                        <input type="text">
                    </div>
                </div>
                
                <div class="filterFormRow">
                    <label for="">Domain Unlocked</label>
                    <div class="fieldArea">
                        <div class="smallSelectBox">
                            <select class="select">
                                <option value="">Greater then</option>
                            </select>
                        </div>
                        <input type="text">
                    </div>
                </div>
            </div>
            <div class="popupRight">
                <div class="filterFormRow">
                    <label for="">Sort by</label>
                    <div class="fieldArea">
                        <div class="largeSelectBox">
                            <select class="select">
                                <option value="">Domain Count more to less</option>
                            </select>
                        </div>
                        
                    </div>
                </div>
                <div class="filterFormRow">
                    <label for="">Phone number type</label>
                    <div class="fieldArea">
                        <div class="radio">
                            <input type="radio" name="PhoneNumberType">
                            <p><span></span></p>
                        </div>
                        <span class="label">Mobile</span>
                        <div class="radio">
                            <input type="radio" name="PhoneNumberType">
                            <p><span></span></p>
                        </div>
                        <span class="label">Landline</span>
                    </div>
                </div>
                <div class="filterFormRow">
                    <button type="button" class="orangeBtn">Apply Filter</button>
                </div>
            </div>
        </div>

        {{csrf_field()}}
        <input type="hidden" id="pagination" name="pagination" value="{{Session::has('pagination') ? Session::get('pagination') : 10}}" readonly>
        <input type="hidden" id="registered_date" name="domains_create_date" 
        value="{{Request::get('domains_create_date') != null ? date('Y-m-d', strtotime(Request::get('domains_create_date'))) : ''}}">
        <input type="hidden" id="registered_date2" name="domains_create_date2" 
        value="{{Request::get('domains_create_date2') != null ? date('Y-m-d', strtotime(Request::get('domains_create_date2'))) : ''}}">
        <input type="hidden" id="domains_expired_date" name="domains_expired_date" 
        value="{{Request::get('domains_expired_date') != null ? date('Y-m-d', strtotime(Request::get('domains_expired_date'))) : ''}}">
        <input type="hidden" id="domains_expired_date2" name="domains_expired_date2" 
        value="{{Request::get('domains_expired_date2') != null ? date('Y-m-d', strtotime(Request::get('domains_expired_date2'))) : ''}}">
        <input type="hidden" id="mode" name="mode" value="{{Request::get('mode') != null ? Request::get('mode') : 'newly_registered'}}">
        <input type="hidden" id="domain_name" name="domain_name" value="{{Request::get('domain_name') != null ? Request::get('domain_name') : ''}}">
        <input type="hidden" id="domain_ext" name="domain_ext" value="{{Session::has('oldReq') && isset(Session::get('oldReq')['domain_ext']) ? Session::get('oldReq')['domain_ext'] : '' }}">
    </form>

    {{-- 
        * hidden fields goes here from mandatory search fields
        --}}
        {{-- var reg_date = $('#registered_date').val();
            var reg_date2 = $('#registered_date2').val();
            var expiry_date = $('#domains_expired_date').val();
            var expiry_date2 = $('#domains_expired_date2').val();
            var mode = $('#postSearchDataForm input[type=radio]:checked').val();
            var domain_name = $('#domain_name').val();
            var domain_ext = $('#domain_ext').val(); --}}
</div>
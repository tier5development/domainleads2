<div class="filterPopup" style="display: none;">
    <form method="POST" action="{{Route('search')}}" class="col-md-6 search_form" id="postAdvancedSearchDataForm">
        <div class="closeFilterPopup"></div>
        <div class="filterPopupInner">
            <div class="popupLeft">
                <div class="filterFormRow">
                    <label for="">Domain Count</label>
                    <div class="fieldArea">
                        <div class="smallSelectBox">
                            <select data-stopsubmit='1' class="selectpage" name="gt_ls_domaincount_no" id="gt_ls_domaincount_no">
                                <option value="0" {{Input::get('gt_ls_domaincount_no') == 0 ? 'selected' : '' }}>Select</option>
                                <option value="1" {{Input::get('gt_ls_domaincount_no') == 1 ? 'selected' : '' }}>Greater than</option>
                                <option value="2" {{Input::get('gt_ls_domaincount_no') == 2 ? 'selected' : '' }}>Lesser Than</option>
                                <option value="3" {{Input::get('gt_ls_domaincount_no') == 3 ? 'selected' : '' }}>Equals</option>
                            </select>
                        </div>
                        <input class="form-control" type="text" name="domaincount_no" id="domaincount_no" value="{{ Input::get('domaincount_no') }}">
                    </div>
                </div>
                
                <div class="filterFormRow">
                    <label for="">Domain Unlocked</label>
                    <div class="fieldArea">
                        <div class="smallSelectBox">
                            <select data-stopsubmit='1' class="selectpage" name="gt_ls_leadsunlocked_no" id="gt_ls_leadsunlocked_no" >
                                <option value="0" {{Input::get('gt_ls_leadsunlocked_no') == 0 ? 'selected' : '' }}>Select</option>
                                <option value="1" {{Input::get('gt_ls_leadsunlocked_no') == 1 ? 'selected' : '' }}>Greater than</option>
                                <option value="2" {{Input::get('gt_ls_leadsunlocked_no') == 2 ? 'selected' : '' }}>Lesser Than</option>
                                <option value="3" {{Input::get('gt_ls_leadsunlocked_no') == 3 ? 'selected' : '' }}>Equals</option>
                            </select>
                        </div>
                        <input class="form-control" type="text" name="leadsunlocked_no" id="leadsunlocked_no" value="{{ Input::get('leadsunlocked_no') }}">
                    </div>
                </div>
            </div>
            <div class="popupRight">
                <div class="filterFormRow">
                    <label for="">Sort by</label>
                    <div class="fieldArea">
                        <div class="largeSelectBox">
                            <select data-stopsubmit='1' class="selectpage" id="sort" name="sort">
                                <option value="unlocked_asnd" {{Input::get('sort') == 'unlocked_asnd' ? 'selected' : '' }}>Unlock Count less to more</option>
                                <option value="unlocked_dcnd" {{Input::get('sort') == 'unlocked_dcnd' ? 'selected' : '' }}>Unlock Count more to less</option>
                                <option value="domain_count_asnd" {{Input::get('sort') == 'domain_count_asnd' ? 'selected' : '' }}>Domain Count less to more</option>
                                <option value="domain_count_dcnd" {{Input::get('sort') == 'domain_count_dcnd' ? 'selected' : '' }}>Domain Count more to less</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="filterFormRow">
                    <label for="">Phone number type</label>
                    <div class="fieldArea">

                        <div class="radio">
                            <input type="checkbox" name="cell_number" value="cell number" @if(Input::get('cell_number')) checked @endif >
                            <p><span></span></p>
                        </div>
                        <span class="label">Mobile</span>
                            
                        <div class="radio">
                            <input type="checkbox" name="landline_number" value="landline number" @if(Input::get('landline_number')) checked @endif>
                            <p><span></span></p>
                        </div>
                        <span class="label">Landline</span>
                    </div>
                </div>
                <div class="filterFormRow">
                    <button id="advanced-search-btn" type="submit" class="orangeBtn">Apply Filter</button>
                </div>
            </div>
        </div>

        {{csrf_field()}}
        
        <input type="hidden" id="pagination" name="pagination" value=@if(isset($pagination)) {{$pagination}}@else {{10}}@endif readonly>

        <input type="hidden" id="registrant_country" name="registrant_country" 
        value="{{Request::has('registrant_country') ? Request::get('registrant_country') : ''}}" readonly>

        <input type="hidden" id="registrant_state" name="registrant_state" 
        value="{{Request::has('registrant_state') ? Request::get('registrant_state') : ''}}" readonly>

        <input type="hidden" id="registrant_zip" name="registrant_zip" 
        value="{{Request::has('registrant_zip') ? Request::get('registrant_zip') : ''}}" readonly>

        <input type="hidden" id="registered_date" name="domains_create_date" 
        value="{{Request::get('domains_create_date') != null ? date('Y-m-d', strtotime(Request::get('domains_create_date'))) : ''}}" readonly>

        <input type="hidden" id="registered_date2" name="domains_create_date2" 
        value="{{Request::get('domains_create_date2') != null ? date('Y-m-d', strtotime(Request::get('domains_create_date2'))) : ''}}" readonly>

        <input type="hidden" id="domains_expired_date" name="domains_expired_date" 
        value="{{Request::get('domains_expired_date') != null ? date('Y-m-d', strtotime(Request::get('domains_expired_date'))) : ''}}" readonly>

        <input type="hidden" id="domains_expired_date2" name="domains_expired_date2" 
        value="{{Request::get('domains_expired_date2') != null ? date('Y-m-d', strtotime(Request::get('domains_expired_date2'))) : ''}}" readonly>

        <input type="hidden" id="mode" name="mode" value="{{Request::get('mode') != null ? Request::get('mode') : 'newly_registered'}}" readonly>
        
        <input type="hidden" id="domain_name" name="domain_name" value="{{Request::get('domain_name') != null ? Request::get('domain_name') : ''}}" readonly>
        
        <input type="hidden" id="domain_ext" name="domain_ext" value="{{Session::has('oldReq') && isset(Session::get('oldReq')['domain_ext']) ? Session::get('oldReq')['domain_ext'] : '' }}" readonly>
    </form>

</div>
<script type="text/javascript">
    $(document).ready(function() {
        $(window).bind("pageshow", function(event) {
            $("#loader-icon").hide();
        });

        $('#advanced-search-btn').on('click', function(e) {
            $("#loader-icon").show();
            e.preventDefault();

            // Call the ajax function to cache search results in mysql
            $.ajax({
                url  : "{{config('settings.DL-API')}}/api/search_api"+"?"+$('#postAdvancedSearchDataForm').serialize(),
                type : "get",
                beforeSend : function() {
                    console.log("going to send advanced form");
                }, success : function(data) {
                    console.log("data return : ", data)
                }, error : function(er) {
                    console.log("err return : ", er)
                }, complete : function() {
                    console.log("completed ... ")
                    $('#loader-icon').hide();
                    $('#postAdvancedSearchDataForm').submit();
                }
            })

        });

        
    });
</script>
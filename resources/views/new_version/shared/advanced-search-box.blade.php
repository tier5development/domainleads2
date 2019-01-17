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
                            {{-- <label>cell number</label>
                            <input type="checkbox" name="cell_number" value="cell number" @if(Input::get('cell_number')) checked @endif >
                            <label>landline number</label>
                            <input type="checkbox" name="landline_number" value="landline number" @if(Input::get('landline_number')) checked @endif> --}}
                        
                        {{-- <div class="radio">
                            <input type="radio" name="PhoneNumberType">
                            <p><span></span></p>
                        </div>
                        <span class="label">Mobile</span>
                        
                        <div class="radio">
                            <input type="radio" name="PhoneNumberType">
                            <p><span></span></p>
                        </div>
                        <span class="label">Landline</span> --}}

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
                    <button type="submit" class="orangeBtn">Apply Filter</button>
                </div>
            </div>
        </div>

        {{csrf_field()}}
        <input type="hidden" id="pagination" name="pagination" value=@if(isset($pagination)) {{$pagination}}@else {{10}}@endif readonly>
        
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
<script type="text/javascript">
    $(document).ready(function() {
        // $('.select').each(function(){
        //     var thisVar = $(this), numberOfOptions = $(this).children('option').length;

        //     thisVar.addClass('select-hidden'); 
        //     thisVar.wrap('<div class="select"></div>');
        //     thisVar.after('<div class="select-styled"></div>');

        //     var styledSelect = thisVar.next('div.select-styled');
        //     styledSelect.text(thisVar.children('option').eq(0).text());

        //     var $list = $('<ul />', {
        //         'class': 'select-options'
        //     }).insertAfter(styledSelect);

        //     for (var i = 0; i < numberOfOptions; i++) {
        //         $('<li />', {
        //             text: thisVar.children('option').eq(i).text(),
        //             rel: thisVar.children('option').eq(i).val()
        //         }).appendTo($list);
        //     }

        //     var listItems = $list.children('li');

        //     styledSelect.click(function(e) {
        //         e.stopPropagation();
        //         $('div.select-styled.active').not(this).each(function(){
        //             $(this).removeClass('active').next('ul.select-options').fadeOut(200);
        //         });
        //         $(this).toggleClass('active').next('ul.select-options').fadeToggle(200);
        //     });

        //     listItems.click(function(e) {
        //         e.stopPropagation();
        //         styledSelect.text($(this).text()).removeClass('active');
        //         thisVar.val($(this).attr('rel'));
        //         $list.fadeOut(200);
        //         //console.log(thisVar.val());
        //     });

        //     $(document).click(function() {
        //         styledSelect.removeClass('active');
        //         $list.fadeOut(200);
        //     });

        // });
    });
</script>
<div class="searchDomainForm">
        <form method="POST" action="{{Route('search')}}" class="col-md-6 search_form" id="postSearchDataForm">
            <div class="formRow">
                <div class="rowHeading">I am looking for</div>
                <div class="formRowInner">
                    <div class="radioCol">
                        <div class="radio">
                            <input checked name="mode" value="newly_registered" {{Input::get('mode') == 'newly_registered' ? 'checked' : '' }} type="radio">
                            <p><span></span></p>
                        </div>
                        <label for="">Newly registered domains</label>
                    </div>
                    <div class="radioCol">
                        <div class="radio">
                            <input type="radio" name="mode" value="getting_expired" {{Input::get('mode') == 'getting_expired' ? 'checked' : ''}}>
                            <p><span></span></p>
                        </div>
                        <label for="">To be expired domains</label>
                    </div>
                </div>
            </div>

            <div class="formRow">
                <div class="rowHeading">Registered date range</div>
                <div class="formRowInner dateRange">
                    <div class="dateArea">
                        <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_calendar.png" alt="">
                        <div class="date">
                            <input type="text" class="day" placeholder="dd">
                            <input type="text" class="month" placeholder="mm">
                            <input type="text" class="year" placeholder="yyyy">
                        </div>
                    </div>
                    <div class="dateArea endDate">
                        <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_calendar.png" alt="">
                        <div class="date">
                            <input type="text" class="day" placeholder="dd">
                            <input type="text" class="month" placeholder="mm">
                            <input type="text" class="year" placeholder="yyyy">
                        </div>
                    </div>
                </div>
            </div>
            <div class="formRow">
                <div class="rowHeading">Domain name</div>
                <div class="formRowInner domainName">
                    <input placeholder="ex: money" type="text" value="{{Request::get('domain_name')}}" name="domain_name" id="domain_name">
                    <div class="selectBox">
                        <select class="select">
                            <option value=".com">.com</option>
                            <option value=".org">.org</option>
                            <option value=".us">.us</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="formRow address">
                <div class="formCol">
                    <label for="">Registered Country</label>
                    <input type="text" value="{{Request::get('registrant_country')}}" name="registrant_country" id="registrant_country" >
                </div>
                <div class="formCol">
                    <label for="">State</label>
                    <input type="text" value="{{ Request::get('registrant_state') }}" name="registrant_state" id="registrant_state">
                </div>
                <div class="formCol">
                    <label for="">ZIP Code</label>
                    <input type="text" name="registrant_zip" value="{{Request::get('registrant_zip')}}" id="registrant_zip">
                </div>
            </div>
            <div class="formRow submit">
                <button type="button" class="orangeBtn">Search Domains</button>
            </div>
            
            <div class="formRow">
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
            
            <div class="formRow">
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
        </form>
    </div>


    {{-- <div class="filterPopup" >
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
        </div> --}}
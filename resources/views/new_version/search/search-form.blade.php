
<div class="searchDomainForm">
    <form method="POST" action="{{Route('search')}}" class="col-md-6 search_form" id="postSearchDataForm">
        <div class="formRow">
            <div class="rowHeading">I am looking for</div>
            <div class="formRowInner">
                <div class="radioCol">
                    <div class="radio">
                        <input type="radio" id="mode_switch" checked name="mode" value="newly_registered" {{Input::get('mode') == 'newly_registered' ? 'checked' : '' }}>
                        <p><span></span></p>
                    </div>
                    <label for="">Newly registered domains</label>
                </div>
                <div class="radioCol">
                    <div class="radio">
                        <input type="radio" id="mode_switch" name="mode" value="getting_expired" {{Input::get('mode') == 'getting_expired' ? 'checked' : ''}}>
                        <p><span></span></p>
                    </div>
                    <label for="">To be expired domains</label>
                </div>
            </div>
        </div>
        <div class="formRow">
            <div id="created_date_div">
                <div class="rowHeading">Registered date range</div>
                <div class="formRowInner dateRange">
                    <div class="dateArea">
                        <div class="date">
                            {{-- <input type="text" 
                            id="datepicker" 
                            class="dateHidden"> --}}
    
                            <input type="text" 
                                value="{{Request::get('domains_create_date') != null ? date('Y-m-d',strtotime(Request::get('domains_create_date'))) : '' }}" 
                                name="domains_create_date" 
                                id="registered_date" 
                                class="dateHidden"
                                placeholder="Start Date"
                                style="disply: none;">
    
                            <input type="text" class="day" placeholder="dd" readonly>
                            <input type="text" class="month" placeholder="mm" readonly>
                            <input type="text" class="year" placeholder="yyyy" readonly>
                        </div>
                    </div>
                    <div class="dateArea endDate">
                        <div class="date">
                            {{-- <input type="text" id="datepicker2" class="dateHidden"> --}}
                            
                            <input type="text" 
                                value="{{Request::get('domains_create_date2') != null ? date('Y-m-d',strtotime(Request::get('domains_create_date2'))) : ''}}" 
                                name="domains_create_date2" 
                                id="registered_date2" 
                                class="dateHidden" 
                                placeholder="End Date"
                                style="display: none">
    
                            <input type="text" class="day" placeholder="dd" readonly>
                            <input type="text" class="month" placeholder="mm" readonly>
                            <input type="text" class="year" placeholder="yyyy" readonly>
                        </div>
                    </div>
                </div>
            </div>
            

            <div id="expired_date_div" style="display: none">
                    <div class="rowHeading">Expired date range</div>
                    <div class="formRowInner dateRange">
                        <div class="dateArea">
                            <div class="date">
                                {{-- <input type="text" 
                                id="datepicker" 
                                class="dateHidden"> --}}
        
                                <input type="text" 
                                    value="{{Request::get('domains_expired_date') != null ? date('Y-m-d',strtotime(Request::get('domains_expired_date'))) : '' }}" 
                                    name="domains_expired_date" 
                                    id="expired_date" 
                                    class="dateHidden"
                                    placeholder="Start Date"
                                    style="disply: none;">
        
                                <input type="text" class="day" placeholder="dd" readonly>
                                <input type="text" class="month" placeholder="mm" readonly>
                                <input type="text" class="year" placeholder="yyyy" readonly>
                            </div>
                        </div>
                        <div class="dateArea endDate">
                            <div class="date">
                                {{-- <input type="text" id="datepicker2" class="dateHidden"> --}}
                                
                                <input type="text" 
                                    value="{{Request::get('domains_expired_date2') != null ? date('Y-m-d',strtotime(Request::get('domains_expired_date2'))) : ''}}" 
                                    name="domains_expired_date2" 
                                    id="expired_date2" 
                                    class="dateHidden" 
                                    placeholder="End Date"
                                    style="display: none">
        
                                <input type="text" class="day" placeholder="dd" readonly>
                                <input type="text" class="month" placeholder="mm" readonly>
                                <input type="text" class="year" placeholder="yyyy" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            
            


        </div>
        <div class="formRow">
            <div class="rowHeading">Domain name</div>
            <div class="formRowInner domainName">
                {{-- <input type="text" placeholder="ex: money"> --}}
                <input type="text" 
                    placeholder="ex: money" 
                    value="{{ Request::get('domain_name') }}" 
                    name="domain_name" 
                    id="domain_name">

                <input type="hidden" name="domain_ext" id="domain_ext">
                <div class="selectBox">
                    <select type="checkbox" class="selectOption">
                        @foreach ($allExtensions as $item)
                            <option value="{{$item}}">{{$item}}</option>
                        @endforeach
                        {{-- <option value="com">com</option>
                        <option value="org">org</option>
                        <option value="us">us</option>
                        <option value="com">io</option>
                        <option value="org">net</option>
                        <option value="us">gov</option>
                        <option value="com">edu</option>
                        <option value="org">in</option>
                        <option value="us">onion</option> --}}
                    </select>
                </div>
            </div>
        </div>
        <div class="formRow address">
            <div class="formCol">
                <label for="">Registered Country</label>
                <input type="text" 
                    value   =   "{{ Request::get('registrant_country') }}" 
                    name    =   "registrant_country" 
                    id      =   "registrant_country"
                    placeholder = "United States">
                {{-- <input type="text"> --}}
            </div>
            <div class="formCol">
                <label for="">State</label>
                {{-- <input type="text"> --}}
                <input 
                    type="text" 
                    value="{{ Request::get('registrant_state') }}" 
                    name="registrant_state" 
                    id="registrant_state"
                    placeholder = "California">
            </div>
            <div class="formCol">
                <label for="">ZIP Code</label>
                {{-- <input type="text"> --}}
                <input 
                    type="text" 
                    name="registrant_zip" 
                    value="{{Request::get('registrant_zip')}}"
                    placeholder = "54321">
            </div>
        </div>
        <div class="formRow submit">
            <button type="submit" type="button" id="searchDomains" class="orangeBtn">Search Domains</button>
        </div>
    </form>
</div>
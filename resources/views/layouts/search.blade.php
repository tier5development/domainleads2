


<div class="row">
            <div class="col-md-12">
               <form method="POST" action="{{Route('search')}}" class="col-md-6 search_form" id="postSearchDataForm">
                  <div class="form-group">
                    {{Input::get('mode')}}
                     <label>Mode : </label>
                     <span> Newly Registered :</span>
                     <input type="radio" checked name="mode" value="newly_registered" {{Input::get('mode') == 'newly_registered' ? 'checked' : '' }}>
                     <span> To Be Expired :</span>
                     <input type="radio" name="mode" value="getting_expired" {{Input::get('mode') == 'getting_expired' ? 'checked' : ''}}>
                  </div>
                  
                  <div class="form-group">
                     <label>Domain Name : </label>
                     <input type="text" value="{{ Request::get('domain_name') }}" name="domain_name" id="domain_name" class="form-control">
                  </div>
                  <div class="form-group">
                     <label>Registrant Country : </label>
                     <input type="text" value="{{ Request::get('registrant_country') }}" name="registrant_country" id="registrant_country" class="form-control">
                  </div>
                  <div class="form-group">
                     <label>Registrant State : </label>
                     <input type="text" value="{{ Request::get('registrant_state') }}" name="registrant_state" id="registrant_state" class="form-control">
                  </div>
                  <div>
                    <label>zip : </label>
                    <input type="text" name="registrant_zip" value="{{Request::get('registrant_zip')}}">
                  </div>
                  <br>
                  
                  <div class="form-group" id="created_date_div" style="display: none">
                    <label>Select Date or Date-Range (Domains Create Date)</label><br>
                    <div class="row">
                    <div class="col-sm-8 col-md-8 col-lg-8">
                      <div class="row">
                        <div class="col-sm-5">
                          <input style="width: 200px" 
                          type="date" 
                          value="{{ Request::get('domains_create_date') != null ? date('Y-m-d',strtotime(Request::get('domains_create_date'))) : '' }}" 
                          name="domains_create_date" 
                          id="registered_date" 
                          class="form-control" 
                          placeholder="Start Date">
                        </div>
                        <div class="col-md-2">

                        </div>
                        <div class="col-sm-5">
                          <input style="width: 200px" 
                          type="date" 
                          value="{{ Request::get('domains_create_date2') != null ? date('Y-m-d',strtotime(Request::get('domains_create_date2'))) : '' }}" 
                          name="domains_create_date2" 
                          id="registered_date2" 
                          class="form-control" 
                          placeholder="End Date">
                        </div>
                      </div>
                    </div>
                    </div>
                  </div>
                  

                  <div class="form-group" id="expired_date_div" style="display: none">
                    <label>Select Date or Date-Range (Domains Expired Date)</label><br>
                    <div class="row">
                      <div class="col-sm-8 col-md-8 col-lg-8">
                        <div class="row">
                          <div class="col-sm-5">
                            <input style="width: 200px" type="date" value="{{ Request::get('domains_expired_date') != null ? date('Y-m-d',strtotime(Request::get('domains_expired_date'))) : '' }}" name="domains_expired_date" id="domains_expired_date" class="form-control" placeholder="Start Date">
                          </div>
                          <div class="col-md-2">

                          </div>
                          <div class="col-sm-5">
                            <input style="width: 200px" type="date" value="{{ Request::get('domains_expired_date2') != null ? date('Y-m-d',strtotime(Request::get('domains_expired_date2'))) : '' }}" name="domains_expired_date2" id="domains_expired_date2" class="form-control" placeholder="End Date">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="from-group">
                     <label>Select Domains Extensions</label>
                     <dl class="dropdown">
                        <dt>
                           <a href="#">
                              <span class="hida">Select</span>    
                              <p class="multiSel"></p>
                           </a>
                        </dt>
                        <dd>
                           <div class="mutliSelect">
                              <ul>
                                 <li>
                                    <input 
                                    type="checkbox" 
                                    name="domain_ext[0]" 
                                    value="com" 
                                    @if(Session::has('oldReq') && isset(Session::get('oldReq')['domain_ext']) && in_array('com', Session::get('oldReq')['domain_ext'])) checked @endif/>com
                                 </li>
                                 <li>
                                    <input 
                                    type="checkbox" 
                                    name="domain_ext[1]" 
                                    value="io" 
                                    @if(Session::has('oldReq') && isset(Session::get('oldReq')['domain_ext']) && in_array('io', Session::get('oldReq')['domain_ext'])) checked @endif/>io
                                 </li>
                                 <li>
                                    <input 
                                    type="checkbox" 
                                    name="domain_ext[2]" 
                                    value="net" 
                                    @if(Session::has('oldReq') && isset(Session::get('oldReq')['domain_ext']) && in_array('net', Session::get('oldReq')['domain_ext'])) checked @endif/>net
                                 </li>
                                 <li>
                                    <input 
                                    type="checkbox" 
                                    name="domain_ext[3]" 
                                    value="org" 
                                    @if(Session::has('oldReq') && isset(Session::get('oldReq')['domain_ext']) && in_array('org', Session::get('oldReq')['domain_ext'])) checked @endif/>org
                                 </li>
                                 <li>
                                    <input 
                                    type="checkbox" 
                                    name="domain_ext[4]" 
                                    value="gov" @if(Session::has('oldReq') && isset(Session::get('oldReq')['domain_ext']) && in_array('gov', Session::get('oldReq')['domain_ext'])) checked @endif/>gov
                                 </li>
                                 <li>
                                    <input 
                                    type="checkbox" 
                                    name="domain_ext[5]" 
                                    value="edu" @if(Session::has('oldReq') && isset(Session::get('oldReq')['domain_ext']) && in_array('edu', Session::get('oldReq')['domain_ext'])) checked @endif/>edu
                                 </li>
                                 <li>
                                    <input 
                                    type="checkbox" 
                                    name="domain_ext[6]" value="in" @if(Session::has('oldReq') && isset(Session::get('oldReq')['domain_ext']) && in_array('in', Session::get('oldReq')['domain_ext'])) checked @endif/>in
                                 </li>
                              </ul>
                           </div>
                        </dd>
                        <!-- <button>Filter</button> -->
                     </dl>
                  </div>

                  <div class="form-group">
                     <label>cell number</label>
                     <input type="checkbox" name="cell_number" value="cell number" @if(Input::get('cell_number')) checked @endif >
                     <label>landline number</label>
                     <input type="checkbox" name="landline_number" value="landline number" @if(Input::get('landline_number')) checked @endif>
                     <label>Data Per-Page</label>
                     <select id="pagination" name="pagination">
                     <option value="10" @if(Input::get('pagination')=='10') selected @endif>10</option>
                     <option value="20" @if(Input::get('pagination')=='20') selected @endif>20</option>
                     <option value="50" @if(Input::get('pagination')=='50') selected @endif>50</option>
                     <option value="100" @if(Input::get('pagination')=='100') selected @endif>100</option>
                     <option value="200" @if(Input::get('pagination')=='200') selected @endif>200</option>
                     
                     </select>
                  </div>
                  <div class="row">


                  <div class="col-md-6 col-sm-6">
                     <label>Sort filter</label>
                     <select id="sort" name="sort">
                     <option value="unlocked_asnd" @if(Input::get('sort')=='unlocked_asnd') selected @endif>unlocked_asnd</option>
                     <option value="unlocked_dcnd" @if(Input::get('sort')=='unlocked_dcnd') selected @endif>unlocked_dcnd</option>
                     <option value="domain_count_asnd" @if(Input::get('sort')=='domain_count_asnd') selected @endif>domain_count_asnd</option>
                     <option value="domain_count_dcnd" @if(Input::get('sort')=='domain_count_dcnd') selected @endif>domain_count_dcnd</option>
                     </select>
                  </div>
                  <div class="col-md-6 col-sm-6">
                  
                  <input class="btn btn-info pull-right" type="submit" name="Submit" value="Submit">
                  </div>
                  </div>

                  <div style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px;">
                  
                  <div class="row form-group">
                     <!-- done -->
                     <div class="col-md-4 col-sm-3">
                        <label>DomainCount :</label> <br>
                     </div>
                     <div class="col-md-4 col-sm-3">   	
                        <select name="gt_ls_domaincount_no" id="gt_ls_domaincount_no">
                           <option value="0" <?php if(Input::get('gt_ls_domaincount_no')==0) { echo "selected";} ?>>Select</option>
                           <option value="1" <?php if(Input::get('gt_ls_domaincount_no')==1) { echo "selected";} ?>>Greater than</option>
                           <option value="2" <?php if(Input::get('gt_ls_domaincount_no')==2) { echo "selected";} ?>>Lesser Than</option>
                           <option value="3" <?php if(Input::get('gt_ls_domaincount_no')==3) { echo "selected";} ?>>Equals</option>
                        </select>
                     </div>
                     <div class="col-md-4 col-sm-3">   
                        <input class="form-control" type="text" name="domaincount_no" id="domaincount_no" value="{{ Input::get('domaincount_no') }}">
                     </div>
                  </div>

                  <div class="row">
                  	<div class="col-md-4 col-sm-3">  
                     <label>LeadsUnlocked :</label> 
                    </div>
                    <div class="col-md-4 col-sm-3">   
                     <select name="gt_ls_leadsunlocked_no" id="gt_ls_leadsunlocked_no" >
                        <option value="0" <?php if(Input::get('gt_ls_leadsunlocked_no')==0) { echo "selected";} ?>>Select</option>
                        <option value="1" <?php if(Input::get('gt_ls_leadsunlocked_no')==1) { echo "selected";} ?>>Greater than</option>
                        <option value="2" <?php if(Input::get('gt_ls_leadsunlocked_no')==2) { echo "selected";} ?>>Lesser Than</option>
                        <option value="3" <?php if(Input::get('gt_ls_leadsunlocked_no')==3) { echo "selected";} ?>>Equals</option>
                     </select>
                     </div>
                     <div class="col-md-4 col-sm-3">  
                     <input class="form-control" type="text" name="leadsunlocked_no" id="leadsunlocked_no" value="{{ Input::get('leadsunlocked_no') }}"> 
                     </div>
                  </div>  
                   
                 <br>
                     <div class="btn btn-primary pull-right" id="refine_searchID">Refine Search</div>
                     <div class="clearfix"></div>
                  </div>
               </form>
            </div>
         </div>
         <script type="text/javascript">
          $(document).ready(function() {
            var mode = $('#postSearchDataForm input[type=radio]:checked').val();
            console.log('mode', mode);
            if(mode == 'newly_registered') {
              $('#created_date_div').show();
              $('#expired_date_div').hide();
            } else if(mode == 'getting_expired') {
              $('#expired_date_div').show();
              $('#created_date_div').hide();
            }

            $('#postSearchDataForm input[type=radio]').on('change', function() {
              var mode = $(this).val();
              if(mode == 'newly_registered') {
                $('#created_date_div').show();
                $('#expired_date_div').hide();
                $('#domains_expired_date').val('');
                $('#domains_expired_date2').val('');
              } else if(mode == 'getting_expired') {
                $('#expired_date_div').show();
                $('#created_date_div').hide();
                $('#registered_date').val('');
                $('#registered_date2').val('');
              }
            });
          });
         </script>
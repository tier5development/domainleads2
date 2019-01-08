@if(Session::has('error'))
    <div style="paddin" class="alert alert-danger" align="center"><strong>Error!</strong> {{ Session::get('error') }} <a onclick=this.hide class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
@endif
{{ Session::forget('error') }}

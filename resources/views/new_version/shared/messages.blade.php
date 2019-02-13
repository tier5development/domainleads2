@if(Session::has('fail'))
    <div class="alertBox error">
        <p>{{Session::get('fail')}}</p>
        <span class="close"></span>
    </div>
    @php Session::forget('fail') @endphp

    @elseif(Session::has('error'))
    <div class="alertBox error">
        <p>{{Session::get('error')}}</p>
        <span class="close"></span>
    </div>
    @php Session::forget('error') @endphp
    @elseif(Session::has('success'))
    <div class="alertBox success">
        <p>{{Session::get('success')}}</p>
        <span class="close"></span>
    </div>
    @php Session::forget('success') @endphp
@endif

{{-- Needed for ajax --}}
<div id="ajax-msg-box" class="alertBox" style="display: none;">
    <p id="ajax-body" class="message-body-ajax"></p>
    <span class="close"></span>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.close').click(function() {
            $(this).parent().removeClass('error').removeClass('success').hide();
            $(".message-body-ajax").text('');
        });
    });
</script>
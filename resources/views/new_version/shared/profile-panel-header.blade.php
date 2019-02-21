<div class="tab">
    <button onclick=window.location.replace("{{route('profile')}}") class="singleItem prof-info">profile information</button>
    <button onclick=window.location.replace("{{route('changePassword')}}") class="singleItem prof-change-password">change password</button>
    @if($user->user_type != config('settings.ADMIN-NUM'))
        <button onclick=window.location.replace("{{route('paymentInformation')}}") class="singleItem prof-payment-info">payment information</button>
    @endif
    <button onclick=window.location.replace("{{route('showMembershipPage')}}") class="singleItem prof-membership">membership</button>
    @if($user->user_type == config('settings.ADMIN-NUM'))
        <button onclick=window.location.replace("{{route('updatePaymentKeys')}}") class="singleItem update-keys">stripe keys</button>
    @endif
</div>

<script type="text/javascript">
    var showPageMarker = function() {
        var pageurl = window.location.href;
        var pageLastUrl = pageurl.split('/').slice(-1)[0];
        switch(pageLastUrl) {
            case 'profile' : 
            $('.tab>button').removeClass('active');
            $('.prof-info').addClass('active');
            break;
            
            case 'change-password' : 
            $('.tab>button').removeClass('active');
            $('.prof-change-password').addClass('active');
            break;

            case 'payment-info' : 
            $('.tab>button').removeClass('active');
            $('.prof-payment-info').addClass('active');
            break;

            case 'membership' : 
            $('.tab>button').removeClass('active');
            $('.prof-membership').addClass('active');
            break;

            case 'update-payment-keys' : 
            $('.tab>button').removeClass('active');
            $('.update-keys').addClass('active');
            break;

            default: 
            break;
        }
    }
    $(document).ready(function() {
        showPageMarker();
    });
</script>
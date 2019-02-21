<footer class="mainFooter">
    <div class="container">
        <div class="">
            <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/logo_footer.png">
            <span>&copy; 2017 Powered by Tier5</span>
            <a href="#">privacy policy</a>
            <a href="#">terms of use</a>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js"></script>
<script type="text/javascript">
    var getUrlParams = function (sParam) {
        var sPageURL = window.location.search.substring(1);
        var sURLVariables = sPageURL.split('&');
        for (var i = 0; i < sURLVariables.length; i++) {
            var sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] == sParam) {
                return sParameterName[1];
            }
        }
        return null;
    }

    var savePlan = function(plan) {
        Cookies.set('plan', plan);
    }

    $(document).ready(function() {    
        var old_affiliate_id = Cookies.get('affiliate_id');
        var affiliate_id = getUrlParams('affiliate_id');
        console.log('ready : ', affiliate_id, Cookies.get('affiliate_id'));
        if(old_affiliate_id == 'undefined') {
            if(affiliate_id != null) {
                Cookies.set('affiliate_id', affiliate_id, { expires: 7 });
                console.log('af id : ', Cookies.get('affiliate_id'));
            } else {
                // no affiliate id found, nothing to do
            }
        }
    });
</script>
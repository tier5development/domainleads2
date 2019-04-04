<div class="rightPanel">
    <div class="leftLine">
        <span></span>
        <p></p>
        <span></span>
    </div>
    <h2>YOUR DOMAIN UNLOCKING HISTORY</h2>
    <div class="todayContent">
        <h3>Today</h3>
        <div class="chart">
            <canvas id="crart" width="132" height="132"></canvas>
        </div>
        <p>
            You have unlocked <span class="green"><span id="currentUnlockedCount"></span> /</span> <span class="yellow"><span id="perDayLimitCount"></span></span> domains today.
        </p>
        <p>
            Upgrade your membership<br> to unlock more of your daily limit.
        </p>
        <p>
            @if($user->user_type < config('settings.PLAN.HIGHEST-UPGRADABLE'))
                <button type="button" class="orangeBtn" onclick=window.location.replace("{{route('showMembershipPage')}}")>Upgrade Now</button>
            @endif
        </p>
    </div>
    <div class="tilldateContent">
        <h3>Till Date</h3>
        <p>You have unlocked <span class="green"> <a class="all-domains-unlocked" href="{{route('myUnlockedLeads')}}"><span id="tillDateCount"></span></a> </span> domails till date.</p>
    </div>
</div>
 <script type="text/javascript">
 $(document).ready(function() {
    $('.container').addClass('level3User');
 });
 var getUsageGlobal = function(refresh = false) {
    
    // console.log('called getUsageGlobal');
    $.ajax({
            url : "{{route('totalLeadsUnlockedToday')}}",
            type: "POST",
            data: {_token: "{{csrf_token()}}"},
            beforeSend: function() {
                console.log('called 1 getUsageGlobal');
            },
            success: function(r) {
                console.log('got response getUsageGlobal');
                $('#loader-icon').hide(); 
                // console.log('response obt : ', r);
                if(r.status) {
                    if(r.limit == -1) {
                        $('.container').addClass('level3User');
                    } else {
                        // $('.rightPanel').show();
                        $('.container').removeClass('level3User');
                        if(refresh) {
                            canvasObj.setCanvas();
                            canvasObj.setCurve(r.leadsUnlocked, r.limit);
                            canvasObj.refresh(10);
                        } else {
                            canvasObj.setCanvas();
                            canvasObj.setCurve(r.leadsUnlocked, r.limit);
                            canvasObj.drawProgressBar(10);
                            $('#currentUnlockedCount').text(r.leadsUnlocked);
                            $('#perDayLimitCount').text(r.limit);
                            $('#tillDateCount').text(r.allLeadsUnlocked);
                        }
                    }
                }
            }, error: function(e) {
                // console.log('got error getUsageGlobal');
                if(e.status === 401) {
                    window.location.replace("{{route('home')}}");
                }
                console.error('error occoured :: -->', e.status);
            }
    });
 }
 var refreshCanvas = function() {
    getUsageGlobal(true);
 }
 $(document).ready(function() {
    getUsageGlobal();
 });
 $(document).ready(function() {
    $('.rightPanTgl').click(function() {
     $('.rightPanel').toggle(); 
    })
});
 </script>
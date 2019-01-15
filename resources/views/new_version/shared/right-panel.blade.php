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
            You have unlocked <span class="green"><span id="currentUnlockedCount"></span> /</span> <span class="yellow"><span id="perDayLimitCount"></span></span> domains today
        </p>
        <p>
            Upgrade your membership<br> to unlock more of your daily limit.
        </p>
        <p>
            <button type="button" class="orangeBtn">Upgrade Now</button>
        </p>
    </div>
    <div class="tilldateContent">
        <h3>Till Date</h3>
        <p>You have unlocked <span class="green"><span id="tillDateCount"></span></span> domails till date</p>
    </div>
</div>
 <script type="text/javascript">
 $(document).ready(function() {
    // var leadsUnlockedGlobal = null;
    // var limitGlobal = null;
    // var allLeadsUnlockedGlobal = null;
    // var 
    $.ajax({
            url : "{{route('totalLeadsUnlockedToday')}}",
            type: "POST",
            data: {_token: "{{csrf_token()}}"},
            success: function(r) {
                console.log('response obt : ', r);
                if(r.status) {
                    canvasObj.setCanvas();
                    canvasObj.setCurve(r.leadsUnlocked, r.limit);
                    canvasObj.drawProgressBar(10);
                    $('#currentUnlockedCount').text(r.leadsUnlocked);
                    $('#perDayLimitCount').text(r.limit);
                    $('#tillDateCount').text(r.allLeadsUnlocked);
                }
            }, error: function(e) {
                console.error(e);
            }
    });
 });
        
 </script>
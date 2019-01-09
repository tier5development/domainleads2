<!DOCTYPE html>
<html lang="en">
    @include('section.user_panel_head',['title' => 'Edit Profile'])
<body>
    <div class="container">
        @include('section.user_panel_header',['user' => $user])
        
        <section class="mainBody">
            <div class="leftPanel leadUnlock">
                
                <h2 class="editProfileHeading">Edit your profile information</h2>
                <div class="profileTip">
                    <figure>
                        <img src="images/icon_camera_black.png" alt="">
                    </figure>
                    <p>
                        TIP: Click on the "Camera" icon on the profile pic<br>to change it
                    </p>
                </div>
                <div class="profileFormArea">
                    <div class="formHeading">
                        Your personal information
                    </div>
                    <div class="formRow">
                        <input type="text" class="small" placeholder="First Name" value="John">
                        <input type="text" class="small" placeholder="Last Name" value="Vaughn">
                    </div>
                    <div class="formRow">
                        <input type="text" placeholder="Email" value="john.doe@tier5.us">
                    </div>
                    <div class="formHeading">
                        Change password
                    </div>
                    <div class="formRow">
                        <input type="password" placeholder="Current Password">
                    </div>
                    <div class="formRow">
                        <input type="password" class="small" placeholder="New Password">
                        <input type="password" class="small" placeholder="Confirm New Password">
                    </div>
                    <div class="formRow">
                        <button type="button" class="orangeBtn">UPDATE</button>
                    </div>
                </div>
            </div>

            <div class="rightPanel desktopOnly">
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
                        You have unlocked <span class="green">40 /</span> <span class="yellow">50</span> domains today
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
                    <p>You have unlocked <span class="green">359</span> domails till date</p>
                </div>
            </div>
            <footer class="footer mobileOnly">
                &copy; 2017 Powered by Tier5 <span><a href="">Privacy Policy</a> / <a href="">Terms of Use</a></span>
            </footer>
        </section>

        <footer class="footer">
            &copy; 2017 Powered by Tier5 <a href="">Privacy Policy</a> / <a href="">Terms of Use</a>
        </footer>
    </div>


    <div class="alert">
        <div class="alertLeft">
            <img src="images/Logo_symbol_green.png" alt="">
        </div>
        <div class="alertRight">
            <p>You have unlocked 4 leads today.
                <br>
                You can unlocked upto <span>50</span> leads per day.
            </p>
        </div>
    </div>


    <script src="js/custom2.js"></script>
<script>
// Activity circle chart 1 --------------------------------------

// var chartConfig = {ier5
//     al: 0,
//     start: 4.72,
//     cw: null,
//     diff:null,
//     radius : 80,
//     chartVal : 84,
//     canvas : document.getElementById('crart1'),
//     context : document.getElementById('crart1').getContext('2d')
// }

var canvas = document.getElementById('crart');

var context = canvas.getContext('2d');
var al=0;
var av = 0;
var start=4.72;
var cw=context.canvas.width/2;
var ch=context.canvas.height/2;
var diff;

var targetVal = 50;
var currentVal = 40;

var radius = 60;
var chartVal = (currentVal / targetVal) * 100;

var gradient = context.createLinearGradient(0, 0, 0, 140);
    gradient.addColorStop(0, '#48e4b3');
    gradient.addColorStop(0.5, '#3cbec1');
    gradient.addColorStop(1, '#48e4b3');

function progressBar(){
    diff=(al/100)*Math.PI*2;
    context.clearRect(0,0,400,400);
    context.beginPath();
    context.arc(cw,ch,radius,0,2*Math.PI,false);
    context.fillStyle='#FFF';
    context.fill();
    context.strokeStyle='#f6f6f6';
    context.stroke();
    context.fillStyle='#000';
    context.strokeStyle= gradient;
    context.textAlign='center';
    context.lineWidth=10;
    context.font = '21px "Avenir LT Std 95 Black"';
    context.fillStyle = '#333';
    context.beginPath();
    context.arc(cw,ch,radius,start,diff+start,false);
    context.stroke();
    context.lineCap = 'round';
    context.fillText(av+"/50" ,65, 75 );
    if(al>=chartVal){
        clearTimeout(bar);
    }
        al++;
        av++;
    if(av>=currentVal){
        av = currentVal;
    }
        
}

var bar = setInterval(progressBar, 10);

</script>

</body>
</html>
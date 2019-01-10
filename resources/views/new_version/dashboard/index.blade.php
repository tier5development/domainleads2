<!DOCTYPE html>
<html lang="en">
    @include('section.user_panel_head', ['title' => 'Domainleads | Dashboard | Search'])
<body>
    <div class="container">
        @include('section.user_panel_header', ['user' => $user])
        <section class="mainBody">
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

            <div class="leftPanel">
                <div class="leftPanelHeader">
                    <div class="clientImg">
                        <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/Profile_circle_small.png" alt="">
                    </div>
                    <div class="clientInfo">
                        <h1>Hey John,</h1>
                        <p>Welcome to Domain Leads. Start your domain search right here.</p>
                    </div>
                </div>
                @include('new_version.shared.search')
            </div>
            <footer class="footer mobileOnly">
                &copy; 2017 Powered by Tier5 <span><a href="">Privacy Policy</a> / <a href="">Terms of Use</a></span>
            </footer>
        </section>
        <footer class="footer">
            &copy; 2017 Powered by Tier5 <a href="">Privacy Policy</a> / <a href="">Terms of Use</a>
        </footer>
    </div>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/custom2.js"></script>
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


<script>
$(document).ready(function(){

    var PATH = "{{config('settings.APPLICATION-DOMAIN')}}/public/";
    console.log(PATH+"images/icon_calendar.png");
    $('.selectOption').each(function(){
        var thisVar = $(this), numberOfOptions = $(this).children('option').length;

        thisVar.addClass('select-hidden'); 
        thisVar.wrap('<div class="select"></div>');
        thisVar.after('<div class="select-styled"></div>');

        var styledSelect = thisVar.next('div.select-styled');
        //styledSelect.text(thisVar.children('option').eq(0).text());

        var $list = $('<ul />', {
            'class': 'select-options'
        }).insertAfter(styledSelect);

        for (var i = 0; i < numberOfOptions; i++) {
            $('<li />', {
                text: thisVar.children('option').eq(i).text(),
                rel: thisVar.children('option').eq(i).val()
            }).appendTo($list);
        }

        var $listItems = $list.children('li');

        styledSelect.click(function(e) {
            console.log('here1');
            e.stopPropagation();
            $('div.select-styled.active').not(this).each(function(){
                $(this).removeClass('active').next('ul.select-options').fadeOut(200);
            });
            $(this).toggleClass('active').next('ul.select-options').fadeToggle(200);
        });

        $listItems.click(function(e) {
            // e.stopPropagation();
            console.log('here2');
            //styledSelect.text($(this).text()).removeClass('active');
            styledSelect.append("<p>" + $(this).text() + "<span class='cl'>x</span></p>");
            $(this).hide();
            thisVar.val($(this).attr('rel'));
            $list.fadeOut(200);
            //console.log(thisVar.val());
        });
        $(document).click(function(e) {
            console.log('here3', e);
            styledSelect.removeClass('active');
            $list.fadeOut(200);
        });
    });

    var cross = $(".selectBox .select-styled p span");

    // $(".select-styled p span").click(function(event){
    //     console.log(event);
    //     event.stopPropagation();
    //      $(this).closest("p").css("background","#000");
    // });
    $(".cl").click(function(event){
        event.stopPropagation();
        console.log(event);
        event.stopPropagation();
        $(this).closest("p").css("background","#000");
    });



    $( "#registered_date").datepicker({
        dateFormat: "yy-mm-dd",
        showOn: "button",
        buttonImage: "/public/images/icon_calendar.png",
        buttonImageOnly: true,
    });

    $( "#registered_date2").datepicker({
        dateFormat: "yy-mm-dd",
        showOn: "button",
        buttonImage: "/public/images/icon_calendar.png",
        buttonImageOnly: true,
    });

    $(".dateHidden").change(function(){
        var dateSelect = $(this).val().split("-");
        $(this).nextAll(".year").val(dateSelect[0]);
        $(this).nextAll(".month").val(dateSelect[1]);
        $(this).nextAll(".day").val(dateSelect[2]);
        // $(this).nextAll(".day").val(dateSelect[0]);
        // $(this).nextAll(".month").val(dateSelect[1]);
        // $(this).nextAll(".year").val(dateSelect[2]);
    });
});
</script>

</body>
</html>
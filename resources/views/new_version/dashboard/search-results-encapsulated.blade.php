<!DOCTYPE html>
<html lang="en">
{{-- <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="css/dashboard.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head> --}}

@include('section.user_panel_head', ['title' => 'Domainleads | Search Results'])

<body>
    <div class="container">
        {{-- <header class="dashHeader">
            <div class="headerLeft">
                <a href=""><img src="images/logo.png" alt=""></a>
            </div>
            <div class="headerRight">
                <div class="heaverNav">
                    <ul>
                        <li>
                            <a href="">
                                <span class="desktopOnly">SEARCH DOMAIN</span>
                                <img src="images/search.png" alt="SEARCH DOMAIN" class="mobileOnly">
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <span class="desktopOnly">UNLOCKED LEADS</span>
                                <img src="images/icon_unlocked_leads_mobile.png" alt="UNLOCKED LEADS" class="mobileOnly">
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="userNameArea">
                    <div class="userName">
                        <span class="desktopOnly">JOHN VAUGHN</span>
                    </div>
                    <div class="userImg">
                        <img src="images/Profile_circle.png" alt="">
                    </div>
                    <div class="profileMenu" style="display: none;">
                        <div class="closeMenu"></div>
                        <div class="profilePic">
                            <a href="" class="changePic"><img src="images/icon_camera_green.png" alt="change picture"></a>
                            <img src="images/profilePic.png" alt="">
                        </div>
                        <div class="profileMenuBody">
                            <div class="profileName">
                                <p>JOHN VAUGHN</p>
                                <span>john.doe@tier5.us</span>
                            </div>
                            <ul>
                                <li><a href="">Profile</a></li>
                                <li><a href="">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </header> --}}
        @include('section.user_panel_header', ['user' => $user])
        
        <section class="mainBody">
            <div class="leftPanel leadUnlock">
                <div class="filterPopup" style="display: none;">
                    <div class="closeFilterPopup"></div>
                    <div class="filterPopupInner">
                        <div class="popupLeft">
                            <div class="filterFormRow">
                                <label for="">Domain Count</label>
                                <div class="fieldArea">
                                    <div class="smallSelectBox">
                                        <select class="select">
                                            <option value="">Greater then</option>
                                        </select>
                                    </div>
                                    <input type="text">
                                </div>
                            </div>
                            <div class="filterFormRow">
                                <label for="">Domain Unlocked</label>
                                <div class="fieldArea">
                                    <div class="smallSelectBox">
                                        <select class="select">
                                            <option value="">Greater then</option>
                                        </select>
                                    </div>
                                    <input type="text">
                                </div>
                            </div>
                        </div>
                        <div class="popupRight">
                            <div class="filterFormRow">
                                <label for="">Sort by</label>
                                <div class="fieldArea">
                                    <div class="largeSelectBox">
                                        <select class="select">
                                            <option value="">Domain Count more to less</option>
                                        </select>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="filterFormRow">
                                <label for="">Phone number type</label>
                                <div class="fieldArea">
                                    <div class="radio">
                                        <input type="radio" name="PhoneNumberType">
                                        <p><span></span></p>
                                    </div>
                                    <span class="label">Mobile</span>
                                    <div class="radio">
                                        <input type="radio" name="PhoneNumberType">
                                        <p><span></span></p>
                                    </div>
                                    <span class="label">Landline</span>
                                </div>
                            </div>
                            <div class="filterFormRow">
                                <button type="button" class="orangeBtn">Apply Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dataTableArea">
                    <div class="dataTableHeader">
                        <div class="unlockInfo">
                            <strong>25924</strong> domains found against your search
                        </div>
                        <div class="dataTableHeaderRight">
                            <button class="refineSearch">
                                <div class="icon"><img src="images/Icon_refine_search.png" alt=""></div>
                                <p>Refine your search</p>
                            </button>
                            <!-- <div class="filterResult">
                                <div class="icon"><img src="images/icon_filter.png" alt=""></div>
                                <input type="text" placeholder="Filter result">
                            </div> -->
                            <div class="pageViewControl">
                                <label for="">SHOW:</label>
                                <div class="selectBox">
                                    <select class="select">
                                        <option value="20">20 per page</option>
                                        <option value="20">50 per page</option>
                                        <option value="20">100 per page</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="datatable">
                        <table cellpadding="0" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>DOMAIN<br>NAME</th>
                                    <th>REGISTRANT<br>NAME, EMAIL</th>
                                    <th>REGISTRANT<br>PHONE</th>
                                    <th>CREATED<br>DATE</th>
                                    <th>EXPIRY<br>DATE</th>
                                    <th>REGISTRANT<br>COMPANY</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <p>19thteams.com</p>
                                        <p class="domains"><img src="images/icon_more_domains.png" alt=""><a href="">5</a></p>
                                    </td>
                                    <td>
                                        <p>Jared Sposito</p>
                                        <p class="email"><a href="">jared@greenswithenvy.net</a></p>
                                        <p class="country"><img src="images/flag_usa.png" alt=""><span>United States</span></p>
                                    </td>
                                    <td>
                                        <p class="phone"><img src="images/icon_mobile.png" alt=""><span>+1.3033207812</span></p>
                                    </td>
                                    <td>
                                        <p><span>06/12/2018</span></p>
                                    </td>
                                    <td>
                                        <p><span>06/12/2020</span></p>
                                    </td>
                                    <td>
                                        <p><span>Greens with Envy</span></p>
                                    </td>
                                    <td>
                                        <p><span></span></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p>19thteams.com</p>
                                        <p class="domains"><img src="images/icon_more_domains.png" alt=""><a href="">5</a></p>
                                    </td>
                                    <td>
                                        <p>Jared Sposito</p>
                                        <p class="email"><a href="">jared@greenswithenvy.net</a></p>
                                        <p class="country"><img src="images/flag_usa.png" alt=""><span>United States</span></p>
                                    </td>
                                    <td>
                                        <p class="phone"><img src="images/icon_land_phone.png" alt=""><span>+1.3033207812</span></p>
                                    </td>
                                    <td>
                                        <p><span>06/12/2018</span></p>
                                    </td>
                                    <td>
                                        <p><span>06/12/2020</span></p>
                                    </td>
                                    <td>
                                        <p><span>Greens with Envy</span></p>
                                    </td>
                                    <td>
                                        <p><span></span></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p>********.co* <img src="images/icon_view_disable.png" alt=""></p>
                                        <div class="leadStatus">
                                            <p class="locked"><img src="images/icon_lock_opened.png" alt=""><span>2</span></p>
                                            <p class="domains"><img src="images/icon_more_domains.png" alt=""><a href="">5</a></p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="encapsulate">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="encapsulate">
                                            <span></span>
                                        </div>
                                    </td>
                                    <td>
                                        <p><span>06/12/2018</span></p>
                                    </td>
                                    <td>
                                        <p><span>06/12/2020</span></p>
                                    </td>
                                    <td>
                                        <div class="encapsulate">
                                            <span></span>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="greenBtn unlockBtn"><img src="images/icon_unclok_whilte.png" alt=""> Unlock</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p>********.co* <img src="images/icon_view_disable.png" alt=""></p>
                                        <div class="leadStatus">
                                            <p class="locked"><img src="images/icon_lock_opened.png" alt=""><span>2</span></p>
                                            <p class="domains"><img src="images/icon_more_domains.png" alt=""><a href="">5</a></p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="encapsulate">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="encapsulate">
                                            <span></span>
                                        </div>
                                    </td>
                                    <td>
                                        <p><span>06/12/2018</span></p>
                                    </td>
                                    <td>
                                        <p><span>06/12/2020</span></p>
                                    </td>
                                    <td>
                                        <div class="encapsulate">
                                            <span></span>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="greenBtn unlockBtn"><img src="images/icon_unclok_whilte.png" alt=""> Unlock</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p>********.co* <img src="images/icon_view_disable.png" alt=""></p>
                                        <div class="leadStatus">
                                            <p class="locked"><img src="images/icon_lock_opened.png" alt=""><span>2</span></p>
                                            <p class="domains"><img src="images/icon_more_domains.png" alt=""><a href="">5</a></p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="encapsulate">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="encapsulate">
                                            <span></span>
                                        </div>
                                    </td>
                                    <td>
                                        <p><span>06/12/2018</span></p>
                                    </td>
                                    <td>
                                        <p><span>06/12/2020</span></p>
                                    </td>
                                    <td>
                                        <div class="encapsulate">
                                            <span></span>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="greenBtn unlockBtn"><img src="images/icon_unclok_whilte.png" alt=""> Unlock</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

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
            <footer class="footer mobileOnly">
                &copy; 2017 Powered by Tier5 <span><a href="">Privacy Policy</a> / <a href="">Terms of Use</a></span>
            </footer>
        </section>

        <footer class="footer">
            &copy; 2017 Powered by Tier5 <a href="">Privacy Policy</a> / <a href="">Terms of Use</a>
        </footer>
    </div>


    <div class="alert" style="display: none;">
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

<script>
$(document).ready(function(){
    $(".refineSearch").click(function(){
        $(".filterPopup").fadeIn();
    });
    $(".closeFilterPopup").click(function(){
        $(".filterPopup").fadeOut();
    });

});
</script>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
    <!-- head -->
    @include('new_version.section.head')
  
    @if(config('settings.ISLIVE') == true)
      <script>
          !function(f,b,e,v,n,t,s)
          {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
          n.callMethod.apply(n,arguments):n.queue.push(arguments)};
          if(!f.fbq)f.fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
          n.queue=[];t=b.createElement(e);t.async=!0;
          t.src=v;s=b.getElementsByTagName(e)[0];
          s.parentNode.insertBefore(t,s)}(window, document,'script',
          'https://connect.facebook.net/en_US/fbevents.js');
          fbq('init', '1544020915892734');
          fbq('track', 'PageView');
      </script>
      <noscript>
          <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1544020915892734&ev=PageView&noscript=1" />
      </noscript>
      <!-- End Facebook Pixel Code -->
    @endif

    <body>

    <!-- banner -->
    <section class="banner">
    
        <!-- header -->
        @include("new_version.section.header_menu")
        
        <!-- inner content -->
        <div class="container" id="bannercaptionWrap">
            <div class="bannerCaption">
                <h2>Let's face the reality</h2>
                <h3>without the leads there is no bussiness </h3>
                <p>The major hitch every vendor and affiliate faces is getting fresh leads which you can connect with.We have ensured that detailed information about your leads is available to you,like which is the best number to contact your leads. </p>
                <a href="{{route('loginPage')}}" class="button gradiant-orange">unlock your leads</a>
            </div>
            
            <div class="bannerContainer">
                <div class="bannerImgLeft">
                    <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/banner_graphic_2.png" alt="banner_graphic_2">
                </div>
                <div class="bannerImgMid">
                    <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/banner_graphic1.png" alt="banner_graphic_1">
                </div>
                <div class="bannerImgRight">
                    <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/banner_graphic_3.png" alt="banner_graphic_3">
                </div>
            </div>
        </div>
    </section>
  <!-- major benefits -->
  <section class="majorBenefits genSection">
    <div class="container">
      <div class="row">
        <div class="col-sm-6">
        <div class="benefits">
          <ul class="cardArea">
            <li class="card1 active">
              <div class="cardContainer">
                <div class="cardFront">
                  <div class="cardIcon"></div>
                  <p>Powerful Filters</p>
                </div>
                <div class="cardBack">
                  <div class="cardIcon"></div>
                  <p>Powerful Filters</p>
                </div>
              </div>
            </li>
            <li class="card2">
              <div class="cardContainer">
                <div class="cardFront">
                  <div class="cardIcon"></div>
                  <p>Verified Phone Numbers</p>
                </div>
                <div class="cardBack">
                  <div class="cardIcon"></div>
                  <p>Verified Phone Numbers</p>
                </div>
              </div>
            </li>
            <li class="card3">
              <div class="cardContainer">
                <div class="cardFront">
                  <div class="cardIcon"></div>
                  <p>User-friendly CRM</p>
                </div>
                <div class="cardBack">
                  <div class="cardIcon"></div>
                  <p>User-friendly CRM</p>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
      <div class="col-sm-5">
        <div class="sideText">
          <h4>major benefits</h4>
          <div class="slideContainer">
            <div class="slide slide1 active">
              <h2 class="heading">Powerful Filters</h2>
              <p class="headingInfo">Easily filter by TDL, Location, Registered Date, Valid contact info and much more. Search specific domains quickly.</p>
              <a href="{{route('signupPage')}}">get started <i class="fas fa-long-arrow-alt-right"></i></a>
            </div>
            <div class="slide slide2">
              <h2 class="heading">Verified Phone Numbers</h2>
              <p class="headingInfo">All our leads are Verified phone Numbers and you can see what they are using, a cell phone or a land-line number.</p>
              <a href="{{route('signupPage')}}">get started <i class="fas fa-long-arrow-alt-right"></i></a>
            </div>
            <div class="slide slide3">
              <h2 class="heading">User-friendly CRM</h2>
              <p class="headingInfo">Not only do we offer fresh and high-quality leads, we also ensure that it’s user-friendly CRM. You can easily unlock your leads and explore.</p>
              <a href="{{route('signupPage')}}">get started <i class="fas fa-long-arrow-alt-right"></i></a>
            </div>
          </div>
          
        </div>
      </div>
      </div>
    </div>
  </section>
  <!-- take a look -->
  <section class="takeLook genSection">
    <div class="container">
      <div class="row">
        <div class="col-sm-4">
          <div class="sideText">
            <h4>take a look</h4>
            <h2 class="heading">See how easily it works</h2>
            <p class="headingInfo">Watch our demo video to see how powerful and easy our platform is.</p>
          </div>
        </div>
        <div class="col-sm-8 pull-right">
            <a href="https://youtu.be/2e78DEDIw0E" data-lity> 
              <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/take_a_look_img.png" alt="how it works video">
            </a>
        </div>
      </div>
      
    </div>
  </section>
  <!-- lead conversion -->
  <section id="leadconversion" class="leadConversion genSection">
    <div class="container">
      <div class="row">
        <div class="col-sm-6">
          <div class="lead_conversion_imgWrap">
            <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/lead_conversion_img.png">
          </div>
        </div>
        <div class="col-sm-6">
          <div class="sideText">
            <h2 class="heading">lead conversion</h2>
            <p class="headingInfo">Our leads have opted in and typically convert for <br>the following services</p>
            <span id="leadsUnlocked">{{$socketMeta->leads_unlocked}}</span>
            <p>Leads unlocked in our platform</p><br>

            <span id="totalDomains">{{$socketMeta->total_domains}}</span>
            <p>Domains in our platform</p><br>
            
            <span id="totalUsers">{{$socketMeta->total_users}}</span>
            <p>Users in our platform</p><br>

            <span id="leadsAddedLastDay">{{$socketMeta->leads_added_last_day}}</span>
            <p>Leads added yesterday</p>

            <a href="{{route('loginPage')}}" class="button gradiant-orange">unlock your leads</a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- users say -->
  <section class="userSay comSection">
    <div class="container">
      <div class="sideText">
        <h2 class="heading">What our <span>users</span> say?</h2>
        <p class="headingInfo">Design Firms, Development Firms, Digital Marketing Agencies, are using our<br> system to get new clients and sell their services. See how they like it.</p>
        <div class="userImages">

          @foreach ($reviews as $key => $review)
            <div class="tooltip userImg{{$key+1}} round {{$key+1 > 7 ? 'showLeft' : ($key+1 >= 9 && $key+1 <= 11 ? 'showBottom' : '')}}">
              <div class="userImgContainer">
                <img src="{{$review->img}}" alt="user1">
              </div>
              <div class="arrow-left">
                <span class="tooltiptext">
                  <p>{{$review->review}}</p>
                  <a href="{{$review->fb_link}}"><h3>{{$review->name}}</h3></a>
                  <h6>{{$review->title}}</h6>
                  <br>
                  {{-- <a href="{{$review->fb_link}}"><img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_fb.png" alt="" style="width: 30px;"></a> --}}
                </span>
              </div>
            </div>    
          @endforeach

          
        </div>

        <div class="reviewSlider">
          <ul class="review">
            <li class="">
              <div class="reviewTxt">
                <p>
                  I always go for the old leads, skybound only does SEO and digital marketing.with domain leads I can quickly filter by leads that already have a website, I love this feature because that is my target audience for our service. DomainLeads gives me the tools I need to terget leads and make sales.
                </p>
                <span class="userName">brandon shavers</span>
                <span class="userInfo">founder - skybound digital</span>
              </div>
            </li>
            <li class="">
                <div class="reviewTxt">
                  <p>
                    I always go for the old leads, skybound only does SEO and digital marketing.with domain leads I can quickly filter by leads that already have a website, I love this feature because that is my target audience for our service. DomainLeads gives me the tools I need to terget leads and make sales.
                  </p>
                  <span class="userName">brandon shavers</span>
                  <span class="userInfo">founder - skybound digital</span>
                </div>
              </li>
          </ul>
          <ul class="profileImg">
            <li class="">
              <div class="profileImgBox">
                <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/Profile_circle@2x.png" alt="brandon shavers">
              </div>
            </li>
            <li>
              <div class="profileImgBox">
                <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/atomix_user31.png" alt="brandon shavers">
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <section id="pricing" class="plans spaceSection">
    <div class="container">
      <h2 class="heading">Pick a plan & start unlocking your leads</h2>
      <p class="headingInfo">Your business boosts right from here</p>
      <div class="eachPlanContainer">
        @foreach (config('settings.PLAN.NAMEMAP') as $key => $item)
        @php if($item[0] == config('settings.PLAN.NON-DISPLAYABLE')) continue; @endphp
          <div class="eachPlanOuter">
            <div class="eachPlan">
              {{-- <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/basic_plan.png"> --}}
              <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/{{config('settings.PLAN.PUBLISHABLE.'.$item[0])[2]}}">
              <h4>{{$item[2]}}</h4>
              <ul>
                <li>{{config('settings.PLAN.PUBLISHABLE.'.$item[0])[0] < 0 ? 'UNLIMITED' : config('settings.PLAN.PUBLISHABLE.'.$item[0])[0]}} leads a day</li>
                <li>location filters</li>
                <li>keywords filters</li>
                <li>TLD filters</li>
                <li>lead exports</li>
              </ul>
              <h3>${{config('settings.PLAN.PUBLISHABLE.'.$item[0])[1]}}</h3>
              <span>Billed monthly, no set up fee.</span>

              @if($user != null)
                @if($user->user_type == config('settings.PLAN.L').$item[0])
                    <a href="{{route('showMembershipPage')}}") data-plan='{{$item[0]}}' class="button planBtn greyButton">current plan</a>
                @elseif($user->user_type > config('settings.PLAN.L').$item[0])
                    @if($user->isDowngradable())
                        <a href="{{route('showMembershipPage')}}" data-plan='{{$item[0]}}' class="button planBtn gradiant-green">downgrade</a>
                    @endif
                @elseif($user->user_type < config('settings.PLAN.L').$item[0])
                    <a href="{{route('showMembershipPage')}}") data-plan='{{$item[0]}}' class="button planBtn gradiant-orange">get started</a>
                @endif
              @else
                <a onclick="savePlan('{{$item[0]}}')" href="{{route('signupPage')}}" class="button gradiant-orange">get started</a>
              @endif
              {{-- <a onclick="savePlan('{{$item[0]}}')" href="{{route('signupPage')}}" class="button gradiant-orange">get started</a> --}}
            </div>
          </div>
        @endforeach
        

      </div>
    </div>
  </section>
  
  @include('section.footer_menu')
  <!-- sticky text -->
  <!-- <div class="stickyBox" id="stickyBoxWrap" style="display: none;">
    <div id="popup">
      <img src="images/sticky_Logo.png">
      <div class="stickyText">
        <p>You have unlocked 4 leads today.</p>
        <p>You can unlocked upto <span>50</span> leads per day.</p>
      </div>
    </div>
  </div>  -->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/jQuery-min-3.3.1.min.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/bootstrap.min.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/lity-2.3.1.min.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/custom.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.2.0/socket.io.js"></script>

    {{-- <script src="//js.pusher.com/3.1/pusher.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/4.4.0/pusher.js"></script>
    <script>
      
      var pusher = new Pusher("{{config('broadcasting.connections.pusher.key')}}", {
        authTransport: 'ajax',
        encrypted: {{config('broadcasting.connections.pusher.options.encrypted')}},
        cluster: "{{config('broadcasting.connections.pusher.options.cluster')}}"
      });
      var channel = pusher.subscribe('usage-info');
      channel.bind('App\\Events\\UsageInfo', function(data) {
        document.getElementById('leadsUnlocked').textContent = data.leadsUnlocked;
        document.getElementById('totalDomains').textContent = data.totalDomains;
        document.getElementById('totalUsers').textContent = data.totalUsers;
        document.getElementById('leadsAddedLastDay').textContent = data.leadsAddedLastDay;
      });

      $(document).ready(function(){
        var hoverFlag = false;
        var counter = 1;
        var pauseTime = 3000;

        $(".cardArea li").each(function(index){
          $(this).hover(function(){
            counter = index + 1;
            $(".cardArea li").removeClass("active");
            $(this).addClass("active");
            $(".slideContainer .slide").removeClass("active");
            $(".slideContainer .slide"+counter).addClass("active");
          });
        });
        
        setInterval(function(){
          if(hoverFlag == false){
            $(".cardArea li").removeClass("active");
            $("li.card"+counter).addClass("active");

            $(".slideContainer .slide").removeClass("active");
            $(".slideContainer .slide"+counter).addClass("active");

            // console.log(counter);
            if(counter == 3){
              counter = 1;
            }else{
              counter = counter+1;
            }
          }
        },pauseTime);
        
        
        var lastScrollTop = 0;
        var moveImg = $(window).scrollTop() / 30;
        $(window).scroll(function(event){
            var st = $(this).scrollTop();
            if (st > lastScrollTop){
              moveImg = moveImg+0.25;
            } else {
              moveImg = moveImg-0.25;
            }
            if(st == 0) {
              moveImg = 0;
            }
            $(".bannerImgLeft").css("margin-top", moveImg+"px");
            $(".bannerImgRight").css("margin-bottom", moveImg+"px");
            //console.log(moveImg);
            lastScrollTop = st;
          });
          
          var countReview = 0;
          var counterSlid = 0;
          $(".reviewSlider .review li").each(function(index){
            $(this).addClass("slid"+index);
            countReview = index;
          });
          $(".reviewSlider .profileImg li").each(function(index){
            $(this).addClass("thumb"+index);
          });
          console.log(countReview);

          setInterval(function(){
            $(".reviewSlider .review li").removeClass("active");
            $(".reviewSlider .profileImg li").removeClass("active");
            $(".thumb"+counterSlid).addClass("active");
            $(".thumb"+counterSlid).addClass("change");
            setTimeout(function(){
              $(".slid"+counterSlid).addClass("active");
              $(".reviewSlider .profileImg li").removeClass("change");
            },600);

            counterSlid = counterSlid + 1;
            if(counterSlid > countReview){
              counterSlid = 0;
            }
          },3000);

        });

    </script>

  </body>
</html>

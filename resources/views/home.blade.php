<!DOCTYPE html>
<!--
 * A Design by GraphBerry
 * Author: GraphBerry
 * Author URL: http://graphberry.com
 * License: http://graphberry.com/pages/license
-->
<html lang="en">
    
    <head>
        <meta charset=utf-8>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Domian Leads</title>
        <!-- Load Roboto font -->
        <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,700&amp;subset=latin,latin-ext' rel='stylesheet' type='text/css'>
        <!-- Load css styles -->

        <link rel="stylesheet" type="text/css" href="{{url('/')}}/theme/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="{{url('/')}}/theme/css/bootstrap-responsive.css">
        <link rel="stylesheet" type="text/css" href="{{url('/')}}/theme/css/style.css">
        <link rel="stylesheet" type="text/css" href="{{url('/')}}/theme/css/pluton.css">
        <link rel="stylesheet" type="text/css" href="{{url('/')}}/theme/css/jquery.cslider.css">
        <link rel="stylesheet" type="text/css" href="{{url('/')}}/theme/css/jquery.bxslider.css">
        <link rel="stylesheet" type="text/css" href="{{url('/')}}/theme/css/animate.css">
        
        <!--[if IE 7]>
            <link rel="stylesheet" type="text/css" href="css/pluton-ie7.css" />
        <![endif]-->
        
    
      
        <!-- Fav and touch icons -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="images/ico/apple-touch-icon-144.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="images/ico/apple-touch-icon-114.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="images/apple-touch-icon-72.png">
        <link rel="apple-touch-icon-precomposed" href="images/ico/apple-touch-icon-57.png">
        <link rel="shortcut icon" href="images/ico/favicon.ico">
    </head>
    
    <body>
   

        <div class="navbar">
            <div class="navbar-inner">
                <div class="container">
                    <a href="#" class="brand">
                    
                        <!--<img src="images/logo.png" width="120" height="40" alt="Logo" />-->
                        <img src="{{url('/')}}/theme/images/Domain-leads-_logo.png">
                        <!-- This is website logo -->
                    </a>
                    <!-- Navigation button, visible on small resolution -->
                    <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <i class="icon-menu"></i>
                    </button>
                    <!-- Main navigation -->
                    <div class="nav-collapse collapse pull-right">
                        <ul class="nav" id="top-navigation">
                            <li class="active"><a href="{{url('/')}}">Home</a></li>
                            <li><a href="#about">About</a></li>
                            
                            <li><a href="#portfolio">How it works</a></li>
                            
                            <li><a href="#lead">Lead Conversion</a></li>
                            <li><a href="#testemonial">Testimonial</a></li>
                            <!-- <li><a href="#plans">Plans</a></li>
                            <li><a href="#price">Pricing</a></li> -->
                             @if (Auth::check())
                             <li> <a href="{{ URL::to('search') }}">Search Domain</a></li>
                             <li> <a href="{{ URL::to('logout') }}">Logout</a></li>
                              @else 


                            <li> <button class="" id="popupid" data-toggle="modal" data-target="#myModal">SignIn</button></li>
                            <li><button class="" id="popupid_for_reg" data-toggle="modal" data-target="#myModal_for_reg">Register</button></li>
                            @endif
                        </ul>
                       
                    </div>
                    <!-- End main navigation -->
                </div>
            </div>
        </div>
        <!-- Start home section -->
        <div id="home">
            <!-- Start cSlider -->
            <div id="da-slider" class="da-slider">
                <div class="triangle"></div>
                <!-- mask elemet use for masking background image -->
                <div class="mask"></div>
                <!-- All slides centred in container element -->
                <div class="container">
                    <!-- Start first slide -->
                    <div class="da-slide">
                        <h2 class="fittext2">BETA Domain Leads</h2>
                        <h4>Free Leads</h4>
                        <p>We provide Filtered out 3rd party private registration data which you can search by domain name, registrant country, registrant state, zip, domain creation date and domain extension.</p>
                        
                        <div class="da-img">
                            <img src="{{url('/')}}/theme/images/Slider01.png">
                        </div>
                    </div>
                    <!-- End first slide -->
                    <!-- Start second slide -->
                    <div class="da-slide">
                        <h2>Verified Cell Numbers</h2>
                        <h4>U.S ONLY</h4>
                        <p>We provide verified active mobile numbers of leads. People who have recently booked a domain name and looking for web services.</p>
                        
                        <div class="da-img">
                           <img src="{{url('/')}}/theme/images/Slider02.png">
                        </div>
                    </div>
                    <!-- End second slide -->
                    <!-- Start third slide -->
                    <div class="da-slide">
                        <h2>Easy Dashboard</h2>
                        <h4>Unlock Leads Easily</h4>
                        <p>We offer a simple, clean and user-friendly dashboard while communicating crucial lead data and key information to our users.</p>
                        
                        <div class="da-img">
                           <!-- <img src="images/Slider03.png" width="320" alt="image03"> -->
                           <img src="{{url('/')}}/theme/images/Slider03.png">
                            
                        </div>
                    </div>
                    <!-- Start third slide -->
                    <!-- Start cSlide navigation arrows -->
                    <div class="da-arrows">
                        <span class="da-arrows-prev"></span>
                        <span class="da-arrows-next"></span>
                    </div>
                    <!-- End cSlide navigation arrows -->
                </div>
            </div>
        </div>
        <!-- End home section -->
        <!-- Service section start -->
        <div class="section primary-section" id="about">
            <div class="container">
                <!-- Start title section -->
                <div class="title">
                    <h1>Why use Domain Leads?</h1>
                    <!-- Section's title goes here -->
                    <p>Let’s face the reality, without the leads there is no business. The major hitch every vendor and affiliate faces is getting fresh leads which you can connect with.
We have ensured that detailed information about your leads is available to you, like which is the best number to contact your leads. We give out details like how many domains they have and what is their creation date. You can unlock and export these leads as per your needs. </p>
                    <div class="container centered" style="padding-top: 1px !important; padding-bottom: 10px !important;">
                <a href="" id="popupid_for_reg" data-toggle="modal" data-target="#myModal_for_reg" class="button button-sp">Register</a>

                </div>
                </div>
                
                <div class="row-fluid">
                    <div class="span4">
                        <div class="centered service">
                            <div class="circle-border zoom-in">
                          
                                
                                <img src="{{url('/')}}/theme/images/Service1.png">
                            </div>
                            <h3>Verified U.S Cell Numbers </h3>
                            <p>All our leads are Verified U.S Cell Numbers and you can follow what they are using, a cell phone or a land-line number.</p>
                        </div>
                    </div>
                    <div class="span4">
                        <div class="centered service">
                            <div class="circle-border zoom-in">
                            
                                <img src="{{url('/')}}/theme/images/Service2.png">
                            </div>
                            <h3>Powerful Filters</h3>
                            <p>Easily filter by TDL, Location, Registered Date, Valid contact info, and much more. Search specific domains quickly.</p>
                        </div>
                    </div>
                    <div class="span4">
                        <div class="centered service">
                            <div class="circle-border zoom-in">
                               
                                <img src="{{url('/')}}/theme/images/Service3.png">
                            </div>
                            <h3>User-friendly CRM</h3>
                            <p>Not only do we offer fresh and high-quality leads, we also ensure that it’s user-friendly CRM. You can easily unlock your leads and explore.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Service section end -->
        <!-- Portfolio section start -->
        <div class="section secondary-section " id="portfolio">
            <div class="triangle"></div>
            <div class="container">
                <div class=" title">
                    <h1>Want to see how it works?</h1>
                    <p>Watch our demo video to see how powerful our platform is.</p>
                </div>
                


                <!--
    
                    repetative divs deleted

                -->


                <div class="videosection">
                    <img src="theme/images/vdo-img.jpg" class="img-responsive">
                    <div class="video-container">
                        
                        <iframe class="video" width="560" height="313" src="https://www.youtube.com/embed/2kMi6MfmGM8?enablejsapi=1&amp;version=3&amp;playerapiid=ytplayer&amp;rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" marginwidth="0" marginheight="0" hspace="0" vspace="0" scrolling="no" allowfullscreen="" allowscriptaccess="always"></iframe>
                    </div>
                </div>
            </div>
        </div>
        <!-- Portfolio section end -->
        <!-- About us section start -->
        <div class="section primary-section" id="lead">
            <div class="triangle"></div>
            <div class="container">
                <div class="title">
                    <h1>Lead Conversion</h1>
                    <p>You won't find anyone else with these powerful features.</p>
                </div>
                <!-- <div class="row-fluid team">
                    <div class="span4" id="first-person">
                        <div class="thumbnail">
                           
                            
                            <img src="{{url('/')}}/theme/images/valid.png">
                            <h3>Validated Phone Numbers</h3>
                            
                            <div class="mask">
                                <h2>So Powerful</h2>
                                <p>Before you ever unlock a lead we let you know if they have a valid phone number or not. We also let you know if it's a landline or Cell Phone. When you unlock the lead you get even more information like carrier and location of the phone number.</p>
                            </div>
                        </div>
                    </div>
                    <div class="span4" id="second-person">
                        <div class="thumbnail">
                           
                            
                            <img src="{{url('/')}}/theme/images/filters.jpg">
                            <h3>Advanced Filters</h3>
                            
                            <div class="mask">
                                <h2>Find the leads you're looking for.</h2>
                                <p>With our advanced filters you can quickly find only the leads that matter to you. Filter by TDL, Location, Registration Date, State, City, Phone Type on file. You can even target specific keywords included in the domain.</p>
                            </div>
                        </div>
                    </div>
                    <div class="span4" id="third-person">
                        <div class="thumbnail">
                            
                            
                            <img src="{{url('/')}}/theme/images/crm.png">
                            <h3>Full CRM</h3>
                           
                            <div class="mask">
                                <h2>Sales Automation</h2>
                                <p>We give you a full CRM to manage your leads. Our CRM offers: Bulk Email/SMS/Call blasts. SMS auto-responder, Voice IVR, in-browser dialer, lead dispositions, sales tracking, and much more..</p>
                            </div>
                        </div>
                    </div>
                </div> -->
                <!-- <div class="about-text centered">
                    <h3>About Us</h3>
                    <p>Tier5 is an American IT Company specializing in subscription based software, lead generation, and business solution. Headquartered in Indiana, we combine diverse skill sets through our network of trained developers. Our subscription based portals are developed under strict, process oriented methodology and supervision. This approach allows us to constantly grow our user base, while delivering quality functionalities to our subscribers. </p>
                </div> -->
                <h3>Typical Lead Conversions</h3>
                <div class="row-fluid">
                    <div class="span6">
                        <ul class="skills">
                            <li>
                                <span class="bar" data-width="80%"></span>
                                <h3>Graphic Design</h3>
                            </li>
                            <li>
                                <span class="bar" data-width="60%"></span>
                                <h3>Wordpress Website</h3>
                            </li>
                            <li>
                                <span class="bar" data-width="90%"></span>
                                <h3>SEO</h3>
                            </li>
                            <li>
                                <span class="bar" data-width="45%"></span>
                                <h3>Website Development</h3>
                            </li>
                        </ul>
                    </div>
                    <div class="span6">
                        <div class="highlighted-box center" style="padding: 10px 30px !important;">
                            <h1 style="margin: -4px;"">Get real results!</h1>
                            <p>If you need real conversions, you should be using Text In Bulk. We give you the newest way to generate leads with a proven high sales conversion rate. We also provide all the tools necessary to put your sales on auto-pilot. Kick back and watch our system do all the work while you rack up the sales. Visit <a href="https://www.textinbulk.com/">www.textinbulk.com</a> or Click on Try Text In Bulk. Generate Inbound Calls, Send Bulk text, Cue your Outbound Calls and Much More.</p>
                            <a href="https://www.textinbulk.com/"><button class="button button-sp"                         style="margin-bottom:10px; margin-top: -11px;">Try Text In Bulk</button></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- About us section end -->
        <div class="section secondary-section">
            <div class="triangle"></div>
            <div class="container centered">
                <p class="large-text">Our leads are ready to buy your services, what are you waiting for?</p>
                <a href="" class="button" id="popupid_for_reg" data-toggle="modal" data-target="#myModal_for_reg" class="button button-sp">Register</a>
            </div>
        </div>
        <!-- Client section start -->
        <!-- Client section start -->
        <div id="clients">
            <div class="section primary-section" id="testemonial">
                <div class="triangle"></div>
                <div class="container">
                    <div class="title">
                        <h1>What our users Say?</h1>
                        <p>Design Firms, Development Firms, Digital Marketing Agencies, they are all using our system to get new clients and sell their services. See how they like it.</p>
                    </div>
                    <div class="row">
                        <div class="span4">
                            <div class="testimonial">
                                <p>"We really love domainleads becuase we can quickly find only local leads looking for our service and contact them in bulk. DomainLeads saves us time and money in marketing and is always bringing in new clients."</p>
                                <div class="whopic">
                                    <div class="arrow"></div>
                                   
                                    
                                    <!-- <img src="{{url('/')}}/theme/images/jon.jpg"> -->
                                    <strong>Jon Vaughn
                                        <small>CEO Tier5 LLC</small>
                                    </strong>
                                </div>
                            </div>
                        </div>
                        <div class="span4">
                            <div class="testimonial">
                                <p>"Every website needs a logo, with domainleads I can search for specific keywords in domains and quickly send out MMS messages with samples of similar logos I've created. I'm constantly getting sales usings domainleads."</p>
                                <div class="whopic">
                                    <div class="arrow"></div>
                                    <strong>Stephine Ayrs
                                        <small>Founder - Item Strategies LLC</small>
                                    </strong>
                                </div>
                            </div>
                        </div>
                        <div class="span4">
                            <div class="testimonial">
                                <p>"I always go for the old leads, Skybound only does SEO and digital marketing. With domain leads I can quickly filter by leads that already have a website, I love this feature becuase that is my target audience for our service. DomainLeads gives me the tools I need to target leads and make sales."</p>
                                <div class="whopic">
                                    <div class="arrow"></div>
                                    <strong>Brandon Shavers
                                        <small>Founder - Skybound Digital</small>
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="testimonial-text">
                        "With others you just buy bulk leads, with us you target, contact, and convert leads to sales."
                    </p>
                </div>
            </div>
        </div>
      
        <!-- Price section start -->
      
        <!-- Price section end -->
        <!-- Newsletter section start -->
        <div class="section third-section">
            <div class="container newsletter">
                <div class="sub-section">
                    <div class="title clearfix">
                        <div class="pull-left">
                            <h3>Newsletter</h3>
                        </div>
                    </div>
                </div>
                <div id="success-subscribe" class="alert alert-success invisible">
                    <strong>You're on your way!</strong>Check your email to finish up and get your free weekly leads and help from our experts.</div>
                <div class="row-fluid">
                    <div class="span5">
                        <p>Sign up to Get leads for prospects who showed interest to buy web services</p>
                    </div>
                    <div class="span7">
                        <form class="inline-form">
                            <input type="email" name="email" id="nlmail" class="span8" placeholder="Enter your email" required />
                            <button id="subscribe" class="button button-sp">Subscribe</button>
                        </form>
                        <div id="err-subscribe" class="error centered">Please provide valid email address.</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Newsletter section end -->
        <!-- Contact section start -->
        <div id="contact" class="contact">
            <div class="section secondary-section">
                <div class="container">
                    <div class="title">
                        <h1>Contact Us</h1>
                        <p>Have any doubts, we're here to clear the air.</p>
                    </div>
                </div>
                <div class="container">
                        <div class="row-fluid">
                            <div class="span12 contact-form centered" style="margin-top: -66px;" >
                                <h3>Engage with us!</h3>
                                <div id="successSend" class="alert alert-success invisible">
                                    <strong>We listen!</strong>Your message has been sent.</div>
                                <div id="errorSend" class="alert alert-error invisible">There was an error.</div>
                                <form id="contact-form" action="php/mail.php">
                                    <div class="control-group">
                                        <div class="controls">
                                            <input class="span12" type="text" id="name" name="name" placeholder="* Your name..." />
                                            <div class="error left-align" id="err-name">Please enter name.</div>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="controls">
                                            <input class="span12" type="email" name="email" id="email" placeholder="* Your email..." />
                                            <div class="error left-align" id="err-email">Please enter valid email adress.</div>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="controls">
                                            <textarea class="span12" name="comment" id="comment" placeholder="* Comments..."></textarea>
                                            <div class="error left-align" id="err-comment">Please enter your comment.</div>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="controls">
                                            <button id="send-mail" class="button">Send message</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <!-- <div class="map-wrapper">
                    <div class="map-canvas" id="map-canvas">Loading map...</div>
                    <div class="container">
                        <div class="row-fluid">
                            <div class="span5 contact-form centered">
                                <h3>Engage with us!</h3>
                                <div id="successSend" class="alert alert-success invisible">
                                    <strong>We listen!</strong>Your message has been sent.</div>
                                <div id="errorSend" class="alert alert-error invisible">There was an error.</div>
                                <form id="contact-form" action="php/mail.php">
                                    <div class="control-group">
                                        <div class="controls">
                                            <input class="span12" type="text" id="name" name="name" placeholder="* Your name..." />
                                            <div class="error left-align" id="err-name">Please enter name.</div>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="controls">
                                            <input class="span12" type="email" name="email" id="email" placeholder="* Your email..." />
                                            <div class="error left-align" id="err-email">Please enter valid email adress.</div>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="controls">
                                            <textarea class="span12" name="comment" id="comment" placeholder="* Comments..."></textarea>
                                            <div class="error left-align" id="err-comment">Please enter your comment.</div>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="controls">
                                            <button id="send-mail" class="message-btn">Send message</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="container">
                    <div class="span9 center contact-info">
                        
                        <p class="info-mail"><a href="mailto:hello@tier5.us" style="color:white;" onMouseOver="this.style.color='black'"
   onMouseOut="this.style.color='white'" >hello@tier5.us</a></p>
                        <!-- <p>+11 234 567 890</p>
                        <p>+11 286 543 850</p>
                        <div class="title">
                            <h3>We Are Social</h3>
                        </div> -->
                    </div>
                    <div class="row-fluid centered">
                        <ul class="social">
                            <li>
                                <a href="https://www.facebook.com/domainleads.io/">
                                    <span class="icon-facebook-circled"></span>
                                </a>
                            </li>
                            <li>
                                <a href="skype:tier5development" >
                                    <span class="icon-skype-circled"></span>
                                </a>
                            </li>
                            <!-- <li>
                                <a href="">
                                    <span class="icon-linkedin-circled"></span>
                                </a>
                            </li> -->
                            <!-- <li>
                                <a href="">
                                    <span class="icon-pinterest-circled"></span>
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    <span class="icon-dribbble-circled"></span>
                                </a>
                            </li> -->
                            <!-- <li>
                                <a href="">
                                    <span class="icon-gplus-circled"></span>
                                </a>
                            </li> -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact section edn -->
        <!-- Footer section start -->
        <div class="footer">
            <p>&copy; 2017 Powered by <a href="https://www.tier5.us">Tier5</a></p>
        </div>
        <!-- Footer section end -->
        <!-- ScrollUp button start -->
        <div class="scrollup">
            <a href="#">
                <i class="icon-up-open"></i>
            </a>
        </div>
        <!-- ScrollUp button end -->
        <!-- Include javascript -->

        <script type="text/javascript" src="{{url('/')}}/theme/js/jquery.js"></script>

        <script type="text/javascript" src="{{url('/')}}/theme/js/jquery.mixitup.js"></script>

        <script type="text/javascript" src="{{url('/')}}/theme/js/bootstrap.js"></script>

        <script type="text/javascript" src="{{url('/')}}/theme/js/modernizr.custom.js"></script>

        <script type="text/javascript" src="{{url('/')}}/theme/js/jquery.bxslider.js"></script>

        <script type="text/javascript" src="{{url('/')}}/theme/js/jquery.cslider.js"></script>

        <script type="text/javascript" src="{{url('/')}}/theme/js/jquery.placeholder.js"></script>

        <script type="text/javascript" src="{{url('/')}}/theme/js/app.js"></script>

        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB3PpQ1EOAybx-Bkx1X52noOoPR_Fh_d1w &callback=initializeMap"></script>
         
       
        <!-- Load google maps api and call initializeMap function defined in app.js -->
        
        <!-- css3-mediaqueries.js for IE8 or older -->
        <!--[if lt IE 9]>
            <script src="js/respond.min.js"></script>
        <![endif]-->
   

<!-- 







-->   

<div class="modal fade loginsignup" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    ×</button>
                <h4 class="modal-title" id="myModalLabel">
                    Login</h4>
                    <div id="errormsg"></div>
            </div>
            <div class="modal-body">


            <form>
                <div class="form-group">
                     <input type="text" name="email" id="email_id_login" class="form-control" placeholder="Please Enter Your Email Adress" >     
                </div>
                <div class="form-group">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Please Enter Your Password">
                </div>
                <div class="form-group">
                    <input type="button" name="submit" value="Submit" class="send-btn" id="submitbtn">
                </div>
            </form>   
            </div>
        </div>
    </div>
</div>







<div class="modal fade loginsignup" id="myModal_for_reg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    ×</button>
                <h4 class="modal-title" id="myLargeModalLabel">
                    Register(Free)</h4>
                    <div id="errormsg_reg"></div>
            </div>
            <div class="modal-body">
                                
                <div class="signup-form">
 
                   
                <form>
                    <div class="form-group">
                        <input type="text" name="first_name" class="formtext form-control" id="first_name" placeholder="First Name">
                    </div>

                    <div class="form-group">
                        <input type="text" name="last_name" class="formtext form-control" id="last_name" placeholder="Last Name">
                    </div>

                    <div class="form-group">
                        <input type="text" name="email" class="formtext form-control" id="email_reg" placeholder="Email Address">
                    </div>

                    <div class="form-group">
                        <input type="password" name="password" class="formtext form-control" id="password_reg" placeholder="Password">
                    </div>

                    <div class="form-group">
                        <input type="password" name="c_password" class="formtext form-control" id="c_password_reg" placeholder="Confirm Password">
                    </div>

                    <div class="form-group">
                        <input type="button" name="Submit" value="Submit" class="send-btn" id="submitbtn_reg">
                    </div>

                </form>

               
            </div>
        </div>
    </div>
</div>









<script type="text/javascript">
$( document ).ready(function() {
        // $("popupid").click(function(){
        //     $('#myModal').modal('show');
        // }); 
        // $("popupid_for_reg").click(function(){
        //     $('#myModal_for_reg').modal('show');
        // }); 

        $("#submitbtn").click(function(){
            var email=$("#email_id_login").val();
            var password=$("#password").val();
           $.ajax({
               type:'POST',
               url:'{{url('/')}}/login',
               data:{email:email , password:password , _token:'{{csrf_token()}}'},
               success:function(data){
               // console.log(data);
                if(data=='success'){
                  window.location.href = 'search';
                }
                if(data=='error1'){
                   $("#errormsg").html('User is not registered');
                }
                if(data=='error2'){
                   $("#errormsg").html('Invalid login credentials');
                } 
                 
               }
            });
        });  
        $("#submitbtn_reg").click(function(){


            var first_name=$("#first_name").val();
            var last_name=$("#last_name").val();
            var email_reg=$("#email_reg").val();
            var password_reg=$("#password_reg").val();
            var c_password_reg=$("#c_password_reg").val();


           $.ajax({
               type:'POST',
               url:"{{url('/')}}/signme",
               data:{first_name:first_name , last_name:last_name , email:email_reg , password:password_reg , c_password:c_password_reg, _token:'{{csrf_token()}}'},
               success:function(data)
               {
                    console.log(data);
                    if(data.msg =='success'){
                       $("#errormsg_reg").html('Successfully Signed');
                       window.location.href = 'search';
                    }
                    if(data.msg =='error1'){
                       $("#errormsg_reg").html('Data not correct');
                    }
                    if(data.msg =='error2'){
                       $("#errormsg_reg").html('Please signup again');
                    } 
                    if(data.msg =='error3'){
                       $("#errormsg_reg").html('Email exists');
                    } 
               }
            });
        });  

});
   
</script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".videosection img").click(function(e){
            $(this).hide();
            $(this).parent().find(".video")[0].src += "&autoplay=1";
            e.preventDefault();
        });
    });
</script>
</body>
</html>
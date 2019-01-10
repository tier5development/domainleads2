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

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
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
         
        {{-- <link rel="apple-touch-icon-precomposed" href="images/ico/apple-touch-icon-57.png"> --}}
        <link rel="shortcut icon" href="images/ico/favicon.ico">


        {{-- <link rel="stylesheet" type="text/css" href="{{url('/')}}/public/css/new/bootstrap.min.css"> 
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


        <link rel="stylesheet" type="text/css" href="{{url('/')}}/public/css/new/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="{{url('/')}}/public/css/new/ionicons.min.css">
        <link rel="stylesheet" type="text/css" href="{{url('/')}}/public/css/new/blue.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
        <link rel="stylesheet" href="{{url('/')}}/public/css/login.css">
        <script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-74654877-7', 'auto');
ga('send', 'pageview');

</script>
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
                    
                </div>
            </div>
        </div>
        <div class="triangle" style="border-left: 585px outset transparent; border-right: 585px outset transparent;"></div>


            <div class="errorMsg">
                @if(Session::has('error'))
                    <div class="alert alert-danger fade in alert-dismissible" style="margin-top:18px;">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                        <strong>Error!</strong> {{Session::get('error')}}
                    </div>
                @elseif(Session::has('success'))
                    <div class="alert alert-success fade in alert-dismissible" style="margin-top:18px;">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                        <strong>Success!</strong> {{Session::get('success')}}
                    </div>
                @endif
                @php Session::forget('error') @endphp
            </div>
            
            <div class="container lgform">
                <div class="frm-heading">
                    <strong><h4>DOMAINLEADS</h4></strong>
                </div>
                
                <div class="login-box-body">
                <p class="login-box-msg">Please provide your registered email.</p>
    
                <form action="{{route('forgotPasswordPost')}}" method="post">
                        {{csrf_field()}}
                    <div class="form-group has-feedback">
                        <input type="email" name="email" class="form-control" placeholder="Email">
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    </div>

                    <div class="">
                        <div class="remember"> 
                            <a class="pull-left" href="{{route('loginPage')}}">Login Instead?</a>
                        </div>
                        
                        <!-- /.col -->
                        <div class="submitBtnArea">
                            <button type="submit" class="btn btn-block btn-flat loginBtn">Send Email</button>
                        </div>
                        <br>
                        <!-- /.col -->
                    </div>
                    <br>
                </form>
                </div>
            </div>
        

            <div class="footer">
                    <p>© 2017 Powered by <a href="https://www.tier5.us">Tier5</a></p>
                </div>

        {{-- <script type="text/javascript" src="{{url('/')}}/theme/js/jquery.js"></script> --}}

<script type="text/javascript">
    $('document').ready(function() {
        console.log(123);
        $(".rememberCheck").click(function(){
            if($(this).prop('checked')){
                $(".checkBox span").css("border-color","#e9c12b");
                $(".checkBox").css("background","#000");
            }else{
                $(".checkBox span").css("border-color","#fff");
                $(".checkBox").css("background","#fff");
            }
        });
    });
</script>
</body>
</html>
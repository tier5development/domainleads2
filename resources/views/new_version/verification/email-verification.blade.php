<!DOCTYPE html>
<html lang="en">
    @include('new_version.section.head')
  
    <body>
        <!-- banner -->
        <section class="banner signUpCon">

            <!-- header -->
            @include('new_version.section.header_menu')
            <!-- inner content -->
            <div class="innerContent clearfix">
                <div class="container customCont cancelDomain signUpConfirmation">
                    <div class="col-sm-8 innerContentWrap">
                        <div class="col-sm-12 createForm">
                            <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/icon_success.png" alt="domain cancel">
                            <h2>Congratulation <span>{{$user->name}}</span></h2>
                                <p>You have succesfully verified your email account.<br></p>
                                <p>Now you can continue to domainleads dashboard.<br></p>
                                <br><br>
                                <p><a href="{{route('search')}}" class="greenBtn anchor-btn">Go To Dashboard</a></p>    
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- footer -->
            @include('section.footer_menu')
        </section>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/bootstrap.min.js"></script>
        <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/custom.js"></script>
    </body>
</html>

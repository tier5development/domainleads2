<!DOCTYPE html>
<html lang="en">
    <!-- head -->
    @include('new_version.section.head')
  
    <body>

    <!-- banner -->
    <section class="banner">
    
        <!-- header -->
        @include("new_version.section.header_menu")
        
        <!-- inner content -->
        <div class="container" id="bannercaptionWrap">
            <div class="bannerCaption">
                <h2>Terms of use will be updated within a couple of days. Thanks.</h2>
                <br><br><br>
                <a href="{{route('home')}}" class="button gradiant-orange">back to landing page</a>
            </div>
        </div>
    </section>
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>

    @include('section.footer_menu')

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/jQuery-min-3.3.1.min.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/bootstrap.min.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/lity-2.3.1.min.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/custom.js"></script>
  </body>
</html>

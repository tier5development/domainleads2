<!DOCTYPE html>
<html lang="en">
    @include('section.user_panel_head', ['title' => 'Domainleads | Dashboard | Search'])
<body>
    @include('new_version.shared.loader')

    <div class="container">
        @include('section.user_panel_header', ['user' => $user])
        <section class="mainBody">
            {{-- Include common dashboard right panel --}}
            @include('new_version.shared.right-panel')

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
                {{-- Search form lies here --}}
                @include('new_version.search.search-form')
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
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/right-panel.js"></script>
    <script src="{{config('settings.APPLICATION-DOMAIN')}}/public/js/common.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>

<script>

var tdlExtensions = {};

var pushExtension = function(ext) {
    console.log(typeof tdlExtensions.ext, tdlExtensions.ext === undefined, tdlExtensions.ext === null, tdlExtensions.ext === undefined || tdlExtensions.ext === null);
    if(tdlExtensions.ext === undefined || tdlExtensions.ext === null) {
        tdlExtensions[ext] = 1;
    }
}

var popExtension = function(ext) {
    if(typeof tdlExtensions.ext !== undefined && typeof tdlExtensions.ext !== null) {
        delete tdlExtensions[ext];
    }
}

$(document).ready(function(){

    var PATH = "{{config('settings.APPLICATION-DOMAIN')}}/public/";

    setTimeout(() => {
        $('#loader-icon').hide();    
    }, 400);

    console.log(PATH+"images/icon_calendar.png");

        $('#postSearchDataForm input[type=radio]').on('change', function() {
        var mode = $(this).val();
        if(mode == 'newly_registered') {
            $('#created_date_div').show();
            $('#expired_date_div').hide();
            $('#domains_expired_date').val('');
            $('#domains_expired_date2').val('');
        } else if(mode == 'getting_expired') {
            $('#expired_date_div').show();
            $('#created_date_div').hide();
            $('#registered_date').val('');
            $('#registered_date2').val('');
        }
    });

    $('.selectOption').each(function(){
        var thisVar = $(this), numberOfOptions = $(this).children('option').length;

        thisVar.addClass('select-hidden'); 
        thisVar.wrap('<div class="select"></div>');
        thisVar.after('<div class="select-styled"><div class="tagContainer"><div class="tagArea"><div class="tagAreaInner"></div></div></div><div class="tglBtn"></div></div>');

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
            e.stopPropagation();
            
            $('div.select-styled.active').not(this).each(function(){
                $(this).removeClass('active').next('ul.select-options').fadeOut(200);
            });
            $(this).toggleClass('active').next('ul.select-options').fadeToggle(200);
        });

        $listItems.click(function(e) {
            e.stopPropagation();
            //styledSelect.text($(this).text()).removeClass('active');
            $('div.select-styled .tagArea .tagAreaInner').append("<p><span class='tagTxt'>" + $(this).text() + "</span><span class='cl'>x</span></p>");
            $(this).hide();
            thisVar.val($(this).attr('rel'));
            $list.fadeOut(200);
            pushExtension($(this).text());
            //console.log(thisVar.val());
            var tagAreaWidth = 0;
            $(".tagAreaInner p").each(function(){
                tagAreaWidth += $(this).outerWidth()+3;
            });
            $(".tagAreaInner").css("width", tagAreaWidth + "px");
            
            $(".select-styled p .cl").click(function(e){
                e.stopPropagation();
                var a = $(this).prev(".tagTxt").text();
                $(this).parent("p").remove();
                popExtension(a);
                $('ul.select-options li').each(function(){
                    if($(this).text() == a){
                        $(this).show();
                    }
                });
            });
        });

        $(document).click(function(e) {
            console.log('removing class');
            styledSelect.removeClass('active');
            $list.fadeOut(200);
        });

        var sc = 0;
        $('body').on('mousewheel', function(e) {
            if($(e.target).closest(".select-styled").hasClass("select-styled")){
                return false;
            // e.preventDefault();
            // e.stopPropagation();
            }
        });

        $('.tagAreaInner').on('mousewheel', function(event) {
            optionScrollWidth = $(".tagAreaInner").width() - $('.tagArea').width();
            if(event.deltaY == -1){
                if(sc > optionScrollWidth){
                sc = optionScrollWidth;
            }
                sc += 10;
            } 
            else if(event.deltaY == 1){
                if(sc < 0){
                sc = 0;
            }
                sc -= 10;
            }
                
            $(".tagArea").scrollLeft(sc);        
        });
    });


    $('#searchDomains').click(function(e) {
        e.preventDefault();
        $('#loader-icon').show();
        var tldOptionsStr = '';
        Object.keys(tdlExtensions).map(function(key, index) {
            if(tldOptionsStr != '') {
                tldOptionsStr += ','+key;
            } else {
                tldOptionsStr += key;
            }
        });
        console.log(tldOptionsStr);
        $('#domain_ext').val(tldOptionsStr);
        $('#postSearchDataForm').submit();
        // console.log(tdlExtensions.toString());
        // var x = tdlExtensions.map(() => function(a, b) {
        //     console.log(a, b);
        // });
        // console.log('x = ', x);
    });



    // $('.selectOption').each(function(){
    //     var thisVar = $(this), numberOfOptions = $(this).children('option').length;

    //     thisVar.addClass('select-hidden'); 
    //     thisVar.wrap('<div class="select"></div>');
    //     thisVar.after('<div class="select-styled"></div>');

    //     var styledSelect = thisVar.next('div.select-styled');
    //     //styledSelect.text(thisVar.children('option').eq(0).text());

    //     var list = $('<ul />', {
    //         'class': 'select-options'
    //     }).insertAfter(styledSelect);

    //     for (var i = 0; i < numberOfOptions; i++) {
    //         $('<li />', {
    //             text: thisVar.children('option').eq(i).text(),
    //             rel: thisVar.children('option').eq(i).val()
    //         }).appendTo(list);
    //     }

    //     var listItems = list.children('li');

    //     styledSelect.click(function(e) {
    //         console.log('here1');
    //         e.stopPropagation();
    //         $('div.select-styled.active').not(this).each(function(){
    //             $(this).removeClass('active').next('ul.select-options').fadeOut(200);
    //         });
    //         $(this).toggleClass('active').next('ul.select-options').fadeToggle(200);
    //     });

    //     listItems.click(function(e) {
    //         // e.stopPropagation();
    //         console.log('here2');
    //         //styledSelect.text($(this).text()).removeClass('active');
    //         styledSelect.append("<p>" + $(this).text() + "<span class='cl'>x</span></p>");
    //         $(this).hide();
    //         thisVar.val($(this).attr('rel'));
    //         list.fadeOut(200);
    //         //console.log(thisVar.val());
    //     });
    //     $(document).click(function(e) {
    //         console.log('here3', e);
    //         styledSelect.removeClass('active');
    //         list.fadeOut(200);
    //     });
    // });

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



    $( "#registered_date, #registered_date2, #expired_date, #expired_date2").datepicker({
        dateFormat: "yy-mm-dd",
        showOn: "button",
        buttonImage: "/public/images/icon_calendar.png",
        buttonImageOnly: true,
    });

    // $( "#registered_date2").datepicker({
    //     dateFormat: "yy-mm-dd",
    //     showOn: "button",
    //     buttonImage: "/public/images/icon_calendar.png",
    //     buttonImageOnly: true,
    // });

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
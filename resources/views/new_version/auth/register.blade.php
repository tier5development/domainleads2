<!DOCTYPE html>
<html lang="en">
  @include('new_version.section.head')
  <body>

  <!-- banner -->
  <section class="banner">
    <!-- header -->
    @include('new_version.section.header_menu')

    <!-- inner content -->
    <div class="innerContent signUp clearfix">
      <div class="container customCont">
        <div class="innerContentWrap">
            <form action="#">
              <div class="leftSide">
                  <h2>Get an account to unlock leads</h2>
                  <h3>personal information</h3>
                    <div class="fieldWrap">
                      <input type="text" class="form-control" placeholder="full name">
                      <input type="text" class="form-control" placeholder="email">
                      <input type="text" class="form-control" placeholder="password">
                    </div>
                    <h3>credit card information</h3>
                    <div class="fieldWrap clearfix">
                      <div class="formRow">
                          <div class="cardNum">
                              <label for="">card number</label>
                              <input type="text" class="form-control" name="" id="">
                          </div>
                          <div class="cvc">
                              <label for="">CVC code</label>
                              <input type="text" class="form-control" name="" id="">
                          </div>
                      </div>
                      <div class="formRow">
                          <div class="fieldCover">
                              <label for="">expiry month</label>
                              <div class="largeSelectBox">
                                <select data-stopsubmit='1' class="selectpage" id="sort" name="sort">
                                    <option value="01" >01</option>
                                    <option value="02" >02</option>
                                    <option value="03" >03</option>
                                    <option value="04" >04</option>
                                </select>
                            </div>
                          </div>
                          <div class="fieldCover exp">
                              <label for="">expiry year</label>
                              <div class="largeSelectBox">
                                <select data-stopsubmit='1' class="selectpage" id="sort" name="sort">
                                    <option value="2019" >2019</option>
                                    <option value="2020" >2020</option>
                                    <option value="2021" >2021</option>
                                    <option value="2022" >2022</option>
                                </select>
                            </div>
                          </div>
                      </div>
                    </div>
                    <div class="fieldWrap spaceTop">
                      <button id="advanced-search-btn" type="submit" class="orangeBtn">get an account</button>
                      <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/cards.webp" alt="cards">
                      <span>Have an account?</span>
                      <a href="#">login now!</a>
                    </div> 
              </div>
              <div class="rightSide">
                  <h3>subcription plans</h3>
                  <div class="fieldWrap">
                    <label class="radioItem">basic
                      <p>$47/m</p>
                      <input type="radio" checked="checked" name="radio">
                      <span class="checkmark"></span>
                    </label>
                    <label class="radioItem">pro
                        <p>$97/m</p>
                      <input type="radio" name="radio">
                      <span class="checkmark"></span>
                    </label>
                    <label class="radioItem">agency
                        <p>$197/m</p>
                      <input type="radio" name="radio">
                      <span class="checkmark"></span>
                    </label>
                  </div>
                  <div class="cartWrap">
                    <img src="{{config('settings.APPLICATION-DOMAIN')}}/public/images/cart.png" alt="cart">
                    <span>order total</span>
                    <h4>$47/m</h4>
                  </div>
              </div>
            </form>
        </div>
      </div>
    </div>
    @include('new_version.section.signin-footer')
  </section>

  
   <!-- footer -->
  <footer class="footer clearboth">
    <div class="container">
      <div class="col-md-12 pull-left">
        <span>&copy; 2017 Powered by Tier5</span>
        <a href="#">privacy policy</a>
        <a href="#">terms of use</a>
      </div>
    </div>
  </footer>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/custom.js"></script>
    <script src="js/custom2.js"></script>
    <script>
      $(document).ready(function(){
      
          var optionScrollWidth;
      
          $('.selectOption').each(function(){
              var thisVar = $(this), numberOfOptions = $(this).children('option').length;
      
              thisVar.addClass('select-hidden'); 
              thisVar.wrap('<div class="select"></div>');
              thisVar.after(`
              <div class="select-styled">
                  <div class="tagContainer">
                      <div class="tagArea">
                          <div class="tagAreaInner"></div>
                      </div>
                  </div>
                  <div class="tglBtn"></div>
              </div>
              `);
      
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
                  //console.log('here1');
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
                      $('ul.select-options li').each(function(){
                          if($(this).text() == a){
                              $(this).show();
                          }
                      });
                  });  
              });
              $(document).click(function(e) {
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
      
      
      // for custom dropdown
          $('.selectpage').each(function(){
      
          var thisInstance = $(this);
      
          var thisVal = $(this), numberOfOptions = $(this).children('option').length;
      
          thisVal.addClass('select-hidden'); 
          thisVal.wrap('<div class="select"></div>');
          thisVal.after('<div class="select-styled"></div>');
      
          var styledSelect = thisVal.next('div.select-styled');
          // styledSelect.text(thisVal.children('option').eq(0).text());
          styledSelect.text(thisVal.children('option:selected').text());
      
          var list = $('<ul />', {
              'class': 'select-options'
          }).insertAfter(styledSelect);
      
          for (var i = 0; i < numberOfOptions; i++) {
              $('<li />', {
                  text: thisVal.children('option').eq(i).text(),
                  rel: thisVal.children('option').eq(i).val()
              }).appendTo(list);
          };
      
          var listItems = list.children('li');
          styledSelect.click(function(e) {
              e.stopPropagation();
              $('div.select-styled.active').not(this).each(function(){
                  $(this).removeClass('active').next('ul.select-options').fadeOut(200);
              });
              $(this).toggleClass('active').next('ul.select-options').fadeToggle(200);
          });
      
          listItems.click(function(e) {
              e.stopPropagation();
              console.log('clicked');
              styledSelect.text($(this).text()).removeClass('active');
              thisVal.val($(this).attr('rel'));
              list.fadeOut(200);
      
              if(thisInstance.data('pagination') !== undefined) {
                  req_pagination = thisVal.val();
                  console.log('pagination', thisInstance.data('pagination'), thisVal.val());
                  $('#pagination').val(thisVal.val());
              }
      
              // Used for advanced-search-box
              if(thisInstance.data('stopsubmit') === undefined) {
                  console.log('not pagination', thisInstance.data('stopsubmit'));
                  submitFormCustom(); 
              }
              // console.log('afsdckhjtaykj kjfy', thisInstance.data('stopsubmit'));
              // submitFormCustom();
          });
      
          $(document).click(function() {
              styledSelect.removeClass('active');
              list.fadeOut(200);
          });
      });
      
      });
      
      </script>
      <script>
    $(document).ready(function(){

        var optionScrollWidth;

        $('.selectOption').each(function(){
            var thisVar = $(this), numberOfOptions = $(this).children('option').length;

            thisVar.addClass('select-hidden'); 
            thisVar.wrap('<div class="select"></div>');
            thisVar.after(`
            <div class="select-styled">
                <div class="tagContainer">
                    <div class="tagArea">
                        <div class="tagAreaInner"></div>
                    </div>
                </div>
                <div class="tglBtn"></div>
            </div>
            `);

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
                //console.log('here1');
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
                    $('ul.select-options li').each(function(){
                        if($(this).text() == a){
                            $(this).show();
                        }
                    });
                });  
            });
            $(document).click(function(e) {
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


    // for custom dropdown
        $('.selectpage').each(function(){

        var thisInstance = $(this);

        var thisVal = $(this), numberOfOptions = $(this).children('option').length;

        thisVal.addClass('select-hidden'); 
        thisVal.wrap('<div class="select"></div>');
        thisVal.after('<div class="select-styled"></div>');

        var styledSelect = thisVal.next('div.select-styled');
        // styledSelect.text(thisVal.children('option').eq(0).text());
        styledSelect.text(thisVal.children('option:selected').text());

        var list = $('<ul />', {
            'class': 'select-options'
        }).insertAfter(styledSelect);

        for (var i = 0; i < numberOfOptions; i++) {
            $('<li />', {
                text: thisVal.children('option').eq(i).text(),
                rel: thisVal.children('option').eq(i).val()
            }).appendTo(list);
        };

        var listItems = list.children('li');
        styledSelect.click(function(e) {
            e.stopPropagation();
            $('div.select-styled.active').not(this).each(function(){
                $(this).removeClass('active').next('ul.select-options').fadeOut(200);
            });
            $(this).toggleClass('active').next('ul.select-options').fadeToggle(200);
        });

        listItems.click(function(e) {
            e.stopPropagation();
            console.log('clicked');
            styledSelect.text($(this).text()).removeClass('active');
            thisVal.val($(this).attr('rel'));
            list.fadeOut(200);

            if(thisInstance.data('pagination') !== undefined) {
                req_pagination = thisVal.val();
                console.log('pagination', thisInstance.data('pagination'), thisVal.val());
                $('#pagination').val(thisVal.val());
            }

            // Used for advanced-search-box
            if(thisInstance.data('stopsubmit') === undefined) {
                console.log('not pagination', thisInstance.data('stopsubmit'));
                submitFormCustom(); 
            }
            // console.log('afsdckhjtaykj kjfy', thisInstance.data('stopsubmit'));
            // submitFormCustom();
        });

        $(document).click(function() {
            styledSelect.removeClass('active');
            list.fadeOut(200);
        });
    });

    });

    </script>
  </body>
</html>

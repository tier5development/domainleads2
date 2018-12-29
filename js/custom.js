$(document).ready(function(){
//responsive menu

  $('.menu-button').click(function(){
    $('.bottomRight').addClass('pull');
  });
  $('.menuClose').click(function(){
    $('.bottomRight').removeClass('pull');
  });

  $('.fancybox').fancybox();
  

  $('#bannercaptionWrap').scroll(function() { 
    $('#banner_img_left').animate({top:$(this).scrollTop()},100,"linear");
})
 });




// sidepopup
function PopUp(hideOrshow) {
      if (hideOrshow == 'hide') document.getElementById('stickyBoxWrap').style.display = "none";
      else document.getElementById('stickyBoxWrap').removeAttribute('style');
      }
      window.onload = function () {
          setTimeout(function () {
              PopUp('show' , '1000');
          }, 2000);
      }



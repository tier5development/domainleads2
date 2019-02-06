$(document).ready(function(){
// for responsive menu

  $('.menu-button').click(function(){
    $('.bottomRight').addClass('pull');
  });
  $('.menuClose').click(function(){
    $('.bottomRight').removeClass('pull');
  });
  // for vier more toggle
  $(".viewMore1").click(function(){
    $(".viewMorePanel1").toggle();
  });
  $(".viewMore2").click(function(){
    $(".viewMorePanel2").toggle();
  });
  $(".viewMore3").click(function(){
    $(".viewMorePanel3").toggle();
  });

// for rightside panel
  $(".rightPanTgl").click(function(){  
      
    if($(this).hasClass("open")){
        $(this).removeClass("open");
        $(".rightPanel").removeClass("open");
        $(".mainBody").scrollTop(bodyScroll);
        $(".leftPanel").css("opacity","1");
        $(".rightPanel").css("display","none");
    } else {
        bodyScroll = $(".mainBody").scrollTop();
        $(".mainBody").scrollTop(0);
        $(this).addClass("open");
        $(".rightPanel").addClass("open");
        $(".leftPanel").css("opacity","0.2");
        $(".rightPanel").css("display","block");
    }
});
});


  // $('.fancybox').fancybox();
  

//   $('#bannercaptionWrap').scroll(function() { 
//     $('#banner_img_left').animate({top:$(this).scrollTop()},100,"linear");
// })

// for sidepopup
// function PopUp(hideOrshow) {
//       if (hideOrshow == 'hide') document.getElementById('stickyBoxWrap').style.display = "none";
//       else document.getElementById('stickyBoxWrap').removeAttribute('style');
//       }
//       window.onload = function () {
//           setTimeout(function () {
//               PopUp('show' , '1000');
//           }, 2000);
//       }


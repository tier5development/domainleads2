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

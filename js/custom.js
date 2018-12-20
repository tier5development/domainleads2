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


// Funtionality for select dropdown
    
$('.select').each(function(){
    var $this = $(this), numberOfOptions = $(this).children('option').length;

    $this.addClass('select-hidden'); 
    $this.wrap('<div class="select"></div>');
    $this.after('<div class="select-styled"></div>');

    var $styledSelect = $this.next('div.select-styled');
    $styledSelect.text($this.children('option').eq(0).text());

    var $list = $('<ul />', {
        'class': 'select-options'
    }).insertAfter($styledSelect);

    for (var i = 0; i < numberOfOptions; i++) {
        $('<li />', {
            text: $this.children('option').eq(i).text(),
            rel: $this.children('option').eq(i).val()
        }).appendTo($list);
    }

    var $listItems = $list.children('li');

    $styledSelect.click(function(e) {
        e.stopPropagation();
        $('div.select-styled.active').not(this).each(function(){
            $(this).removeClass('active').next('ul.select-options').fadeOut(200);
        });
        $(this).toggleClass('active').next('ul.select-options').fadeToggle(200);
    });

    $listItems.click(function(e) {
        e.stopPropagation();
        $styledSelect.text($(this).text()).removeClass('active');
        $this.val($(this).attr('rel'));
        $list.fadeOut(200);
        //console.log($this.val());
    });

    $(document).click(function() {
        $styledSelect.removeClass('active');
        $list.fadeOut(200);
    });

});

var max = 0;
var last = null;
function changeColor(element, color){
  $(element).css('background-color', '#'+color);
}
$(function(){
  $(".tog").each(function() {
    $(this).hide();
  });
  $(".subnav").each(function() {
    $(this).click(function(event){
      event.preventDefault();
      hideWindows();
      removeActive();
      $($(this).attr('href')).show({duration: 500});
      $(this).parent().addClass('active');
    });
  });
  $(".subnav").first().trigger("click");
});
/*$(function(){
  $(".fake-window").each(function() {
    $(this).draggable({
        scroll: false,
        //containment: 'body',
        cancel: ".fake-window *"
    });
    $(this).resizable();
    $(this).prepend("<span class='close' onclick='$(this).parent().hide();'>x</span>");
    $(this).hide();
    $(this).click(function(){
      max = max+1;
      $(this).css("z-index", max);
    }).mousedown(function() {
      max = max+1;
      $(this).css("z-index", max);
    });
  });
});*/

function removeActive(){
  $(".subnav").each(function() {
    $(this).parent().removeClass('active');
  });
}

function hideWindows(){
  $(".tog").each(function() {
    $(this).hide();
  });
}

function showFakeWindow(fake){
  hideWindows();
  $(fake).show({duration: 500});
  $(fake)
}

function hideMenu(){
  $(".subnav").each(function(){
    var glyph = $(this).attr('altsym');
    $(this).html('<span class="glyphicon '+glyph+'" aria-hidden="true"/>');
  })
  $('#navleft').removeClass("col-sm-1 col-md-1", {duration:500});
  $('#mpage').addClass("pad", {duration:500}).removeClass("col-sm-offset-1 col-md-offset-1 cols-sm-10 col-md-11", {duration:500});
  $('#mpage').removeClass("cols-sm-10 col-md-11").addClass("cols-sm-10 col-md-11",{duration:500});
  $('#hide2').removeClass("glyphicon-chevron-left").addClass("glyphicon-chevron-right");
  $('#hide').attr('href','javascript:showMenu();');
}
function showMenu(){
  $('#navleft').addClass("col-sm-1 col-md-1", {duration:500});
  $('#mpage').removeClass("pad cols-sm-10 col-md-11").addClass("col-sm-offset-1 col-md-offset-1 cols-sm-10 col-md-11", {duration:500});
  $('.main').attr('padding-left','40px');
  setTimeout(function(){
    $(".subnav").each(function(){
      var glyph = $(this).attr('altname');
      $(this).html(glyph);
    })
  }, 300)
  $('#hide2').removeClass("glyphicon-chevron-right").addClass("glyphicon-chevron-left");
  $('#hide').attr('href','javascript:hideMenu();');
}

//Original work by Brent Profera. You can find the liscense @ http://199.19.225.11:9683/license.txt
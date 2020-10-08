/**
* Theme: Minton Admin Template
* Author: Coderthemes
* Module/App: Main Js
*/


(function($){

  'use strict';

  function initNavbar () {

    $('.navbar-toggle').on('click', function(event) {
      $(this).toggleClass('open');
      $('#navigation').slideToggle(400);
    });

    $('.navigation-menu>li').slice(-1).addClass('last-elements');

    $('.navigation-menu li.has-submenu a[href="#"]').on('click', function(e) {
      if ($(window).width() < 992) {
        e.preventDefault();
        $(this).parent('li').toggleClass('open').find('.submenu:first').toggleClass('open');
      }
    });
  }

  // === following js will activate the menu in left side bar based on url ====
  function initNavbarMenuActive() {
    $(".navigation-menu a").each(function () {
      if (this.href == window.location.href) {
        $(this).parent().addClass("active"); // add active to li of the current link
        $(this).parent().parent().parent().addClass("active"); // add active class to an anchor
        $(this).parent().parent().parent().parent().parent().addClass("active"); // add active class to an anchor
      }
    });
  }

  function init () {
    initNavbar();
    initNavbarMenuActive();
  }

  init();
  
  var oldlocation = location.href;
  
  function getParameterByName(name, url) {
    if (!url) url = oldlocation;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
      results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
  }
  
  //counter
  $('.counter').counterUp({
    delay: 100,
    time: 1200
  });
  //circle progress
  $('.circliful-chart').circliful();
  //sweetalert error
  if(getParameterByName('error') != null){
    setTimeout(function(){
      swal({
        title: "Error",
        text: getParameterByName('error'),
        type: "error",
        timer: 3000,
        showCancelButton: false,
        confirmButtonClass: "btn-danger waves-effect waves-light",
        confirmButtonText: "OK"
      });
    }, 500);
  }
  //sweetalert error
  if(getParameterByName('warning') != null){
    setTimeout(function(){
      swal({
        title: "Warning",
        text: getParameterByName('warning'),
        type: "warning",
        timer: 3000,
        showCancelButton: false,
        confirmButtonClass: "btn-warning waves-effect waves-light",
        confirmButtonText: "OK"
      });
    }, 500);
  }
  //sweetalert success
  if(getParameterByName('success') != null){
    setTimeout(function(){
      swal({
        title: "Success",
        text: getParameterByName('success'),
        type: "success",
        timer: 3000,
        showCancelButton: false,
        confirmButtonClass: "btn-success waves-effect waves-light",
        confirmButtonText: "OK"
      });
    }, 500);
  }
  //functions
  function removeURLParameter(url, parameter) {
    //prefer to use l.search if you have a location/link object
    var urlparts= url.split('?');   
    if (urlparts.length>=2) {

        var prefix= encodeURIComponent(parameter)+'=';
        var pars= urlparts[1].split(/[&;]/g);

        //reverse iteration as may be destructive
        for (var i= pars.length; i-- > 0;) {    
            //idiom for string.startsWith
            if (pars[i].lastIndexOf(prefix, 0) !== -1) {  
                pars.splice(i, 1);
            }
        }

        url= urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : "");
        return url;
    } else {
        return url;
    }
}
  //query strings
  if (Modernizr.history) {
    var url = removeURLParameter(location.href, 'error');
    url = removeURLParameter(url, 'warning');
    url = removeURLParameter(url, 'success');
    history.pushState(null, "", url);
  }
})(jQuery)


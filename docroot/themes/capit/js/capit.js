(function($, Drupal) {
Drupal.behaviors.myBehavior = {
  attach: function (context, settings) {
    // Menu option for Mobile
    $('.mob-menu-div').click(function(){
        $(".leftMobMenu").toggle();
    })
    // Add icons to content
    $('.block-landing-block h2').prepend('<span class="fa fa-pencil-square-o"></span> ');
    $('.region-content nav h2').prepend('<span class="fa fa fa-info-circle"></span> ');
    //Remove Clock Icon
    $( "section[class*='block-views-blockimportant-deadlines-block'] h2" ).find('span').remove();
    //Add Clock Icon
    $("section[class*='block-views-blockimportant-deadlines-block'] h2").prepend('<span class="fa fa-clock-o"></span> ');
  }
};

$(document).ready(function() {
  msgpack_popup();
  
  // Change the Serach box placeholder text
  $('form').find("input[type=search]").each(function(ev) {
      if(!$(this).val()) { 
          $(this).attr("placeholder", "Search Programs, Departments, Institutes");
      }
  });

  //packet basic cart
  $('.menu-icn').click(function(e) {
    $('body').removeClass('msg-pck-act');
      if($('body').hasClass('menu-act')){
          $('body').removeClass('menu-act');
      }else{
        $('body').addClass('menu-act');
      }
  });

  $(this).find("ul.navbar-nav li:first-child").click(function(){
      $('body').removeClass('menu-act');
  });

 $('.msg-pck').click(function(){
   $('body').removeClass('menu-act');
    if($('body').hasClass('msg-pck-act')){
        $('body').removeClass('msg-pck-act');
    }else{
        $('body').addClass('msg-pck-act');
    }
  });
 
  var v_status;
  var menulistbtn_timeout;
  $(document).delegate('.lgn-lst', 'mouseover' ,function() {
    $('body').addClass('user-login-act');
    clearTimeout(menulistbtn_timeout);
  });
  $(document).delegate('.lgn-lst', 'mouseleave' ,function() {
    v_status = true;
    toggleLoginMenu(this);
  });
  $(document).delegate('.user-login', 'mouseenter' ,function() {
    v_status = false;
    toggleLoginMenu(this);
  });
  $(document).delegate('.user-login', 'mouseleave' ,function() {
    v_status = true;
    toggleLoginMenu(this);
  });
  function toggleLoginMenu(a){
    if(v_status){
      menulistbtn_timeout = setTimeout(function(){
        $('body').removeClass('user-login-act');
      }, 300);
    }
    else{
      clearTimeout(menulistbtn_timeout);
    }
  }
});

$(window).load(function(){
  $(".srch-blk, .bscart-pop").mCustomScrollbar({
    scrollButtons:{enable:true},
    theme: "dark-thick",
    advanced:{  
      updateOnBrowserResize:true,   
      updateOnContentResize:true   
    } 
  });
  $(".table-responsive").mCustomScrollbar({
    theme:"dark-3",
    scrollButtons:{enable:true},
    axis:"x",
    advanced:{  
      updateOnBrowserResize:true,   
      updateOnContentResize:true   
    } 
  });
});

$(window).resize(function () {
  msgpack_popup();
})

function msgpack_popup(){
  var width = $(window).width();
  if ((width >= 768)) {
    $('.basic-cart-pck').css('top', 70);
    if ($('.msg-pck > a').length > 0) {
      $('.basic-cart-pck').css('right', ($(window).width() - $('.msg-pck > a').offset().left - 170));
    }
  }
}
$(document).on('click', function(e) {
    if ( ($(e.target).closest('.menu-icn').length) || ($(e.target).closest('.msg-pck').length)  ) {
    }else if ( ! ($(e.target).closest('.user-menus').length) || ($(e.target).closest('.basic-cart-pck').length) ) {
        $('body').removeClass('menu-act');
        $('body').removeClass('msg-pck-act');
    }
});

})(jQuery, Drupal);


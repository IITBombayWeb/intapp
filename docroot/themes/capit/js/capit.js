(function($, Drupal) {
Drupal.behaviors.myBehavior = {
  attach: function (context, settings) {

     

   /* $("#login").hover(
        function () {
            $('#block-capit-account-menu').stop(true,true).slideDown('medium');
        }, 
        function () {
            $('#block-capit-account-menu').stop(true,true).slideUp('medium');
        }
    });*/

    // Change the Serach box placeholder text
    $('form').find("input[type=search]").each(function(ev) {
        if(!$(this).val()) { 
            $(this).attr("placeholder", "Search programs, Departments, Institutes");
        }
    });

    // View option for Institute
    size_li_inst = $(".block-facet-blockinstitute > ul > li").size();
    var x=5;
    $('.viewMoreInst').remove();
    if(size_li_inst > x) {
        $(".block-facet-blockinstitute > ul > li").hide();
        $('.block-facet-blockinstitute > ul > li:lt('+x+')').show();
        $( ".block-facet-blockinstitute" ).append( '<div class="view-more viewMoreInst"><a> View More...</a></div>');
        $('.viewMoreInst').click(function () {
            x= (x+5 <= size_li_inst) ? x+5 : size_li_inst;
            $('.block-facet-blockinstitute > ul > li:lt('+x+')').show();
            var active_inst = $('.block-facet-blockinstitute > ul').find("li:visible").length;
            if(size_li_inst == active_inst) {
                $('.viewMoreInst').remove();
            }
        });
    }

    // View option for Degree
    size_li_deg = $(".block-facet-blockdegree > ul > li").size();
    var deg=5;
    $('.viewMoreDeg').remove();
    if(size_li_deg > x) {
        $(".block-facet-blockdegree > ul > li").hide();
        $('.block-facet-blockdegree > ul > li:lt('+deg+')').show();
        $( ".block-facet-blockdegree" ).append( '<div class="view-more viewMoreDeg"><a> View More...</a></div>');
        $('.viewMoreDeg').click(function () {
            deg= (deg+5 <= size_li_deg) ? deg+5 : size_li_deg;
            $('.block-facet-blockdegree > ul > li:lt('+deg+')').show();
            var active_deg = $('.block-facet-blockdegree > ul').find("li:visible").length;
            if(size_li_deg == active_deg) {
                $('.viewMoreDeg').remove();
            }
        });
    }

    // View option for Department
    size_li_dept = $(".block-facet-blockdepartment > ul > li").size();
    var dept=5;
    $('.viewMoreDept').remove();
    if(size_li_dept > dept) {
        $(".block-facet-blockdepartment > ul > li").hide();
        $('.block-facet-blockdepartment > ul > li:lt('+dept+')').show();
        $( ".block-facet-blockdepartment" ).append( '<div class="view-more viewMoreDept"><a> View More...</a></div>');
        $('.viewMoreDept').click(function () {
            dept= (dept+5 <= size_li_dept) ? dept+5 : size_li_dept;
            $('.block-facet-blockdepartment > ul > li:lt('+dept+')').show();
            var active_dept = $('.block-facet-blockdepartment > ul').find("li:visible").length;
            if(size_li_dept == active_dept) {
                $('.viewMoreDept').remove();
            }
        });
    }

    // View option for Department
    size_li_mstviw = $(".view-most-viewed > .view-content > .views-row").size();
    var mstviw =5;
    $('.viewMoreMstVw').remove();
    if(size_li_mstviw > mstviw) {
        $(".view-most-viewed > .view-content > .views-row").hide();
        $('.view-most-viewed > .view-content > .views-row:lt('+mstviw+')').show();
        $( ".view-most-viewed" ).append( '<div class="view-more viewMoreMstVw"><a> View More...</a></div>');
        $('.viewMoreMstVw').click(function () {
            mstviw= (mstviw+5 <= size_li_mstviw) ? mstviw+5 : size_li_mstviw;
            $('.view-most-viewed > .view-content > .views-row:lt('+mstviw+')').show();
            var active_mstvw = $('.view-most-viewed > .view-content').find(".views-row:visible").length;
            if(size_li_mstviw == active_mstvw) {
                $('.viewMoreMstVw').remove();
            }
        });
    }

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
        $("section#block-capit-department-2").mCustomScrollbar ({
        scrollButtons:{enable:true},
        theme: "dark-thick",
    });

    $("#block-capit-institute-2").mCustomScrollbar ({
        scrollButtons:{enable:true},
        theme: "dark-thick",
    });


});
$(window).resize(function () {
  msgpack_popup();
})
function msgpack_popup(){
  $('.basic-cart-pck').css('top', $('.msg-pck > a').offset().top + 50);
  if ($('.msg-pck > a').length > 0) {
    $('.basic-cart-pck').css('right', ($(window).width() - $('.msg-pck > a').offset().left) - 170);
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


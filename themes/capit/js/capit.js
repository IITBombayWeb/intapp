(function($) {
Drupal.behaviors.myBehavior = {
  attach: function (context, settings) {

    //Login Code
    $("#login").click(function() {
      $('#block-capit-account-menu').toggle();
    });
  }
};
})(jQuery);
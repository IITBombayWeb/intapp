/**
 * @file
 * Contains js for the accordion example.
 */
/*(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.basiccart = {
    attach: function (context, settings) {

    }
  };
})(jQuery, Drupal, drupalSettings); */

(function ($) {
  $(function () {
   // $a = $(".basic-cart-block").length;
   // $("#forquantitydynamictext_1008").hide();
    //alert($a);
    $(".basiccart-cart-node-title").each(function(){
      $('#forquantitydynamictext_'+$(this).find('a').attr('href').split('/node/')[1]).addClass('sent-pckt');
      $('#forquantitydynamictext_'+$(this).find('a').attr('href').split('/node/')[1]).removeAttr("href");
      
    });
    
    
  $(".addtocart-quantity-wrapper-container").each(function(){
    
                var this_id = $(this).attr('id');
                id_split = this_id.split("_");
                var dynamic_id = "quantitydynamictext_"+id_split[1];
                var dynamic_input = '<label for="edit-quantity" class="js-form-required form-required">Quantity</label> <input type="text" class="quantity_dynamic_text form-text required" id="'+dynamic_id+'">';
                $(this).html(dynamic_input);
    
           });
      
      $(document).on('click',".basiccart-get-quantity",function(e){
        e.preventDefault();   e.stopPropagation();
        $(this).addClass('sent-pckt');
        
        var this_ids = $(this).attr('id');
        id_splited = this_ids.split("_");
        var quantity = $('#quantitydynamictext_'+id_splited[1]).val();
        var basiccart_throbber = '<div id="basiccart-ajax-progress-throbber_'+id_splited[1]+'" class="basiccart-ajax-progress-throbber ajax-progress ajax-progress-throbber"><div class="basiccart-throbber throbber">&nbsp;</div></div>';
         
         $('#forquantitydynamictext_'+id_splited[1]).after(basiccart_throbber);
         //alert("hi");
         $.ajax({url: this.href+quantity, success: function(result){
              $(".basiccart-grid").each(function(){
                //console.log(result.block);
                $(this).html(result.block);               
              });
              //alert(result.id);
              $("#"+result.id).hide();
              $("#cnt").html($('.basiccart-cart-contents').length);
              $("#"+result.id).html(result.text);
              $("#"+result.id).fadeIn('slow').delay(1000).hide(2000);
              $('#basiccart-ajax-progress-throbber_'+id_splited[1]).remove();
              $(".bscart-pop").mCustomScrollbar({
                scrollButtons:{enable:true},
                theme: "dark-thick",
                set_height: "auto",
              });
          }});
         $(this).removeAttr("href");
      });
  })
})(jQuery);
          


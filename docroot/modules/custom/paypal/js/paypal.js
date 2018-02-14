  
/**
 * @file
 * Contains js for the accordion example.
 */

/*

(function ($) {
  $(function () {
      $(document).on('click',".paypal_submit",function(e){
        //var name1 = $('#edit-business').val();
        var unique_id = $('#edit-custom').val();
        var usr_id = $('#edit-user-id').val();
        var cc = $('#edit-currency-code').val();
        var order = $('#edit-order-id').val();
        var status = 'pending';
        var tx = 0;
        var amt = 20;
       
        //var app_id = 1020 1020;
        var formData = {user_id:usr_id,orders_id:order,amount:amt,currency_code:cc,custom_id:unique_id,transaction_id:tx,payment_status:status};
        $.ajax({
            url : "IitInapdev",
            type: "POST",
            data : formData,
            success: function(data, textStatus, jqXHR)
            {
                //data - response from server
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
         
            }
        });
      });
  })
})(jQuery);
*/
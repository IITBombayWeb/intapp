<?php
/**
 * @file
 * Contains \Drupal\paypal\Form\ResumeForm.
 */
namespace Drupal\paypal\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\basiccart\Utility;
use Drupal\basiccart\Form\CartForm;

class payment_before_insert extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'payment_before_insert';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
  
          module_load_include('inc', 'basiccart');
          $application_id = basiccart_get_cart();
          //dpm($application_id['cart']);
          $applicationArray = $application_id['cart'];
              foreach ($applicationArray as $key => $value) {
              $iit[] =$key;
              }
              $commaList = implode(',', $iit);
	      
    $user = \Drupal::currentUser();
    $Utility = new Utility();
    $price = $Utility::get_total_price();
    $total = $price->total;
    $cartform = new CartForm();
    
    module_load_include('inc', 'basiccart');
     $application_id = basiccart_get_cart();
     $applicationArray = $application_id['cart'];
   
  if(!empty($_REQUEST)){
    
   \Drupal::database()->insert('paypal_payment_status')
	->fields([
		'user_id',
                'orders_id',
                'before_amount',
                'after_amount',
                'currency_code',
                'custom_id',
                'transaction_id',
                'payment_status',
	])
	->values(array(
		$user-> id(),
		$commaList,
                $total.".00",
                0,
                $_REQUEST['currency_code'],
                $_REQUEST['custom_id'],
                $_REQUEST['transaction_id'],
                $_REQUEST['payment_status'],
            
	))
	->execute();
     
  }
  
  
  
  
  //\Drupal::logger('paypal')->notice("test"); 

    
   $form['welcome'] = array(
		     '#markup' => 'welcome',
		     '#prefix' => '<div class="basiccart-cart basiccart-grid bascart-totl">',
		     '#suffix' => '</div>',
		     
	      );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
    public function validateForm(array &$form, FormStateInterface $form_state) {

   
    }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  
   }
}
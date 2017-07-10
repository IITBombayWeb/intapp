<?php
/**
 * @file
 * Contains \Drupal\paypal\Form\PaymentSuccess.
 */
namespace Drupal\paypal\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\profile\Entity\Profile;
use Drupal\profile\Entity\ProfileType;
use Drupal\field\FieldConfigInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use \Drupal\node\Entity\Node;
use Drupal\Core\Url;
use Drupal\workflow\Entity\Workflow;
use Drupal\workflow\Entity\WorkflowState;
use Drupal\workflow\Entity\WorkflowConfigTransition;
use Drupal\workflow\Entity\WorkflowTransitionInterface;
use Drupal\workflow\Controller\WorkflowTransitionListController;
use Drupal\user\Entity\user;

use Drupal\file\Entity\File;
use Dompdf\Dompdf;
///naga june 29
use Drupal\basiccart\Controller\CartController;
use Drupal\basiccart\Utility;
use Drupal\basiccart\Form\CartForm;

class PaymentSuccess extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'payment_success';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    
          module_load_include('inc', 'basiccart');
          $application_id = basiccart_get_cart();
          $applicationArray = $application_id['cart'];
              foreach ($applicationArray as $key => $value) {
              $iit[] =$key;
              }
              $commaList = implode(',', $iit);
         
      
       if ((isset($application_id['cart']) && is_array($application_id['cart']))) {
	      ///////// naga paypal integration
	      $Utility = new Utility();
	      $cartform = new CartForm();
              $price = $Utility::get_total_price();
              $total = $price->total;
              $cart_value = $cartform->get_total_price_markup();
              $rm_inr = str_replace("INR", "", $total);
	      $cart = $Utility::get_cart();
	      $config = $Utility::cart_settings();  
	      $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();   
	      $user = \Drupal::currentUser();
              /*
              if ($user ->id()){
		     $price = $Utility::get_total_price();
		     $total = $Utility::price_format($price->total);
		     if (empty($_GET['tx'])){
			    if($price->total > 0) { 
				    $form['#action'] ='https://www.sandbox.paypal.com/cgi-bin/webscr';
				   //$form['#action'] ='/paypal/IitInapdev';
			    }
		     }else {
			    $form['#action'] ='/paypal/success';
		     }
	      }else {
		     $form['#action'] = '/user/login/?destination=get-profile';
	      }
	      */
	      
	      
	      $form['welcome'] = array(
		     
		     '#markup' => '<h4><b>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book </b></h4>',
		     '#prefix' => '<div class="basiccart-cart basiccart-grid bascart-totl">',
		     '#suffix' => '</div>',
		     // '#theme' => 'cart_total_price',
	      );
	      
	      
	      
	      
	      /*
             // dpm($total);
              //dpm($rm_inr);
              $unique_id = md5(uniqid(rand(), true));  
	      $form['business'] = array(
		     '#type' => 'hidden',
		     '#value' => 'malathi.s@unimity.com',
		     '#name' => "business",
	      );
	      $form['cmd'] = array(
		     '#type' => 'textfield',
		     '#value' => '_cart',
		     '#name' => "cmd",
	      );
	      $form['upload'] = array(
		     '#type' => 'textfield',
		     '#value' => '1',
		     '#name' => "upload",
	      );
              $form['user_id'] = array(
		     '#type' => 'textfield',
		     '#value' => $user->id(),
		     
	      );
              
              
	      
	      foreach ($cart['cart_quantity'] as $pgm_nid => $node) {
                     $order_id[]=$pgm_nid;
		     $node_load = node::load($pgm_nid);
		     $iit_name = $node_load->getTranslation('en')->get('field_institute')->getValue()[0]['target_id'];
		     $list_of_iits[]=$iit_name;
	      }
              
              $form['order_id'] = array(
                     '#title' => 'ordeid',
		     '#type' => 'textfield',
		     '#value' => $commaList,
	      );
              
	      $filtr_iits = array_unique($list_of_iits);
	      foreach($filtr_iits as $key => $tax_term){
		     $tax_term_load = taxonomy_term_load($tax_term);
		     $institute_price = $tax_term_load->getTranslation('en')->get('field_iit_app_price')->getValue()[0]['value'];
		     $iit = $tax_term_load->getTranslation('en')->get('name')->getValue()[0]['value'];
	      }
	      
	      $c = 1;
	      foreach($filtr_iits as $key => $tax_term){
		     $tax_term_load = taxonomy_term_load($tax_term);
		     $institute_price = $tax_term_load->getTranslation('en')->get('field_iit_app_price')->getValue()[0]['value'];
		     $iit = $tax_term_load->getTranslation('en')->get('name')->getValue()[0]['value'];
		     $form['item_name_'.$c] = array(
			    '#type' => 'textfield',
			    '#value' => $iit,
			    '#name' => "item_name_".$c,
		     );
                     $form['item_number_'.$c] = array(
			    '#type' => 'textfield',
			    '#value' => 450,
			    '#name' => "item_number_".$c,
		     );
		      $form['amount_'.$c] = array(
			    '#type' => 'textfield',
			    '#value' => (!empty($institute_price) ? $institute_price : 0),
			    '#name' => "amount_".$c,
		     );
		     $c++;
	      }
	      $form['custom'] = array(
		     '#type' => 'textfield',
		     '#value' => $unique_id,
		     '#name' => "custom",
                     '#required' => TRUE,
	      );
	      $form['cpp_header_image'] = array(
		     '#type' => 'textfield',
		     '#value' => 'http://www.phpgang.com/wp-content/uploads/gang.jpg',
		     '#name' => "cpp_header_image",
	      );
	      $form['no_shipping'] = array(
		     '#type' => 'textfield',
		     '#value' => '1',
		     '#name' => "no_shipping",
	      );
	      $form['currency_code'] = array(
		     '#type' => 'textfield',
		     '#value' => 'USD',
		     '#name' => "currency_code",
	      );
	      $form['handling'] = array(
		     '#type' => 'textfield',
		     '#value' => '0',
		     '#name' => "handling",
	      );
	      $form['cancel_return'] = array(
		     '#type' => 'textfield',
		     '#value' => 'http://iitinapdev.unimity.com/paypal/success',
		     '#name' => "cancel_return",
	      );
	      $form['return'] = array(
		     '#type' => 'textfield',
		     '#value' => 'http://iitinapdev.unimity.com/paypal/thankyou',
		     '#name' => "return",
	      );
	      
	       $form['notify_url'] = array(
		     '#type' => 'textfield',
		     '#value' => 'http://iitinapdev.unimity.com/paypal/thankyou',
		     '#name' => "return",
	      );
	   
	      // Total price.
	      $form['total_price'] = array(
		     '#markup' => $cartform->get_total_price_markup(),
		     '#markup' => 'welcome',
		     '#prefix' => '<div class="basiccart-cart basiccart-grid bascart-totl">',
		     '#suffix' => '</div>',
		     // '#theme' => 'cart_total_price',
	      );
	      */
	         
               $form['actions']['submit'] = array(
                      '#type' => 'submit',
                      '#value' => $this->t('Apply '),
                      '#attributes' => array('class' => array('paypal_submit')),
              );
              $form['actions']['cancel'] = array(
                      '#type' => 'button',
                      '#value' => $this->t('Cancel'),
              );
      
          return $form;
    }
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
  
  //paypal Custom value
  
	      module_load_include('inc', 'basiccart');
	      $application_id = basiccart_get_cart();
	      $applicationArray = $application_id['cart'];
		foreach ($applicationArray as $key => $value) {
		$iits[] =$key;
		}
              $commaList = implode(',', $iits);
         
  
   if ((isset($application_id['cart']) && is_array($application_id['cart']))) {
    $paypal_email = 'malathi.s@unimity.com';
    $return_url = 'http://dev-intapp.iitb.ac.in/paypal/thankyou';
    $cancel_url = 'http://dev-intapp.iitb.ac.in/paypal/success';
    $notify_url = 'http://dev-intapp.iitb.ac.in/paypal/ipn_notification';
   // $notify_url = 'http://dev-intapp.iitb.ac.in/paypal/success';
    $cart_mthd ='_cart';
    $upload = 1;
    $no_note = 0;
    $user_id = 510;
    $no_shipping = 1;
    $currency_code = 'USD';
    $handling = 0;
    //$custom = 'nagaraj';
    $order_id = 1020;
    $item_no1 = 1;

	      $Utility = new Utility();
	      $cartform = new CartForm();
              $price = $Utility::get_total_price();
              $total = $price->total;
              $cart_value = $cartform->get_total_price_markup();
              $rm_inr = str_replace("INR", "", $total);
	      $cart = $Utility::get_cart();
	      $config = $Utility::cart_settings();  
	      $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();   
	      $user = \Drupal::currentUser();
              $unique_id = md5(uniqid(rand(), true)); 
              foreach ($cart['cart_quantity'] as $pgm_nid => $node) {
                     $order_id[]=$pgm_nid;
		     $node_load = node::load($pgm_nid);
		     $iit_name = $node_load->getTranslation('en')->get('field_institute')->getValue()[0]['target_id'];
		     $list_of_iits[]=$iit_name;
	      }
              $filtr_iits = array_unique($list_of_iits);
         //if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"])){
		    $querystring = '';
		    // Firstly Append paypal account to querystring
		    $querystring .= "?business=".urlencode($paypal_email)."&";
		    $c = 1;
		    foreach($filtr_iits as $key => $tax_term){
			   $tax_term_load = taxonomy_term_load($tax_term);
			   $institute_price = $tax_term_load->getTranslation('en')->get('field_iit_app_price')->getValue()[0]['value'];
			   $iit = $tax_term_load->getTranslation('en')->get('name')->getValue()[0]['value'];
			      $institute_price1 = urlencode(stripslashes($institute_price));
			      $iit1 = urlencode(stripslashes($iit));
			      $querystring .= "item_name_".$c."=$iit1&";
			      $querystring .= "amount_".$c."=$institute_price1&";
			      $c++;
		    }
		    $querystring .= "cmd=".urlencode($cart_mthd)."&";
		    $querystring .= "upload=".urlencode($upload)."&";
		    $querystring .= "no_shipping=".urlencode($no_shipping)."&";
		    $querystring .= "currency_code=".urlencode($currency_code)."&";
		    $querystring .= "handling=".urlencode($handling)."&";
		    $querystring .= "custom=".urlencode($unique_id)."&";
		    // Append paypal return addresses
		    $querystring .= "return=".urlencode(stripslashes($return_url))."&";
		    $querystring .= "cancel_return=".urlencode(stripslashes($cancel_url))."&";
		    $querystring .= "notify_url=".urlencode($notify_url);
		    
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
							$currency_code,
							$unique_id,
							'processing',
							'pending',
						    
						))
						->execute();
	
		    header('location:https://www.sandbox.paypal.com/cgi-bin/webscr'.$querystring);	
		    exit;
	   /* }else{
      
		//  {
		   
		     // Response from Paypal
	     
		     // read the post from PayPal system and add 'cmd'
		     /
		     $req = 'cmd=_notify-validate';
		     foreach ($_POST as $key => $value) {
			     $value = urlencode(stripslashes($value));
			     $value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i','${1}%0D%0A${3}',$value);// IPN fix
			     $req .= "&$key=$value";
		     }
		     
		     // assign posted variables to local variables
		     $data['item_name']		= $_POST['item_name'];
		     $data['item_number'] 	= $_POST['item_number'];
		     $data['payment_status'] 	= $_POST['payment_status'];
		     $data['payment_amount'] 	= $_POST['mc_gross'];
		     $data['payment_currency']	= $_POST['mc_currency'];
		     $data['txn_id']		= $_POST['txn_id'];
		     $data['receiver_email'] 	= $_POST['receiver_email'];
		     $data['payer_email'] 	= $_POST['payer_email'];
		     $data['custom'] 		= $_POST['custom'];
			     
		     // post back to PayPal system to validate
		     $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
		     $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		     $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		     
		     $fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);
		     
		     if (!$fp) {
			     // HTTP ERROR
			     
		     } else {
			     fputs($fp, $header . $req);
			     while (!feof($fp)) {
				     $res = fgets ($fp, 1024);
				     if (strcmp($res, "VERIFIED") == 0) {
					     
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
							     'test',
							     'test',
							     'test',
							     'test',
							     'test',
							     'test',
							     'test',
							     'test',
							 
						     ))
						     ->execute();
					     /*
					     $valid_txnid = check_txnid($data['txn_id']);
					     $valid_price = check_price($data['payment_amount'], $data['item_number']);
					     // PAYMENT VALIDATED & VERIFIED!
					     if ($valid_txnid && $valid_price) {
						     
						     $orderid = updatePayments($data);
						     
						     if ($orderid) {
							     // Payment has been made & successfully inserted into the Database
						     } else {
							     // Error inserting into DB
							     // E-mail admin or alert user
							     // mail('user@domain.com', 'PAYPAL POST - INSERT INTO DB WENT WRONG', print_r($data, true));
						     }
					     } else {
						     // Payment made but data has been changed
						     // E-mail admin or alert user
					     }
					     
					     */
				     /*
				     } else if (strcmp ($res, "INVALID") == 0) {
				     
					     // PAYMENT INVALID & INVESTIGATE MANUALY!
					     // E-mail admin or alert user
					     
					     // Used for debugging
					     //@mail("user@domain.com", "PAYPAL DEBUGGING", "Invalid Response<br />data = <pre>".print_r($post, true)."</pre>");
				     }
			     }
		     fclose ($fp);
		     }
		   }
		   
		   */
		 // }
		}
  }
    
function application_save_submit(&$form, \Drupal\Core\Form\FormStateInterface $form_state) {
 
  
      
     }
}

/*
 profile load
 
 
 use Drupal\profile\Entity\Profile;
use Drupal\profile\Entity\ProfileType;

 $query = \Drupal::entityQuery('profile')
    ->condition('status', 1)
    ->condition('uid', 510);
  $nids = $query->execute();
dpm($nids);
  $nids = array_values($nids);
  if(isset($nids[0])) {
    $profile = Profile::load($nids[0]);
   dpm($profile);
  }

//$profile = Profile::load(510);
//dpm($profile);

use Drupal\user\Entity\User;
$user = User::load(510);
dpm($user);
 
 
 
 
 */



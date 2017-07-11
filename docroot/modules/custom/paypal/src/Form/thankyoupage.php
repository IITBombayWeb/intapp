<?php
/**
 * @file
 * Contains \Drupal\paypal\Form\ResumeForm.
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
use Drupal\user\Entity\user;
use Drupal\file\Entity\File;

use Drupal\basiccart\Controller\CartController;
use Drupal\basiccart\Utility;
use Drupal\basiccart\Form\CartForm;

class thankyoupage extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'thankyou';
  }

/**
 * {@inheritdoc}
 */
public function buildForm(array $form, FormStateInterface $form_state) {
  // pdt tokens   			 
 //if(!empty($_GET['tx'])){
 if(isset($_GET['tx']) && !empty($_GET['tx']) && isset($_GET['cc']) && !empty($_GET['cc']) && isset($_GET['amt']) && !empty($_GET['amt']) && isset($_GET['cm']) && !empty($_GET['cm']) && isset($_GET['tx']) && !empty($_GET['tx']) ) {
 
 
  if(isset($_GET['tx']) && !empty($_GET['tx'])) {
    function pdt_token($tx){
      $pp_hostname = "www.sandbox.paypal.com"; // Change to www.sandbox.paypal.com to test against sandbox
      // read the post from PayPal system and add 'cmd'
      $req = 'cmd=_notify-synch';
      $tx_token = $tx;
      $auth_token = "GfqCw6b45-T-AZYVEMwCwy0IB4gNCO8QXVvHd0irgnbk4hhB80Xm437MddW";
      $req .= "&tx=$tx_token&at=$auth_token";
      $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, "https://$pp_hostname/cgi-bin/webscr");
       curl_setopt($ch, CURLOPT_POST, 1);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
       //set cacert.pem verisign certificate path in curl using 'CURLOPT_CAINFO' field here,
       //if your server does not bundled with default verisign certificates.
       curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
       curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: $pp_hostname"));
       $res = curl_exec($ch);
       curl_close($ch);
       if (!$res){
	   //HTTP ERROR
       } else {
	    // parse the data
	   $lines = explode("\n", trim($res));
	   $keyarray = array();
	   if (strcmp ($lines[0], "SUCCESS") == 0) {
	      $status = $lines[0];
	   }
	   else if (strcmp ($lines[0], "FAIL") == 0) {
	      $status = $lines[0];
	   }
	}
	return $status;
    }
     $tx = $_GET['tx'];
     $tx_status = pdt_token($tx);
  }
  //dpm($tx_status);
  module_load_include('inc', 'basiccart');
  $application_id = basiccart_get_cart();
  $applicationArray = $application_id['cart'];
      foreach ($applicationArray as $key => $value) {
      $iit[] =$key;
      }
      $commaList = implode(',', $iit);
    


    if(isset($_GET['tx']) && !empty($_GET['tx'])) {
	   // get response
	 if($tx_status == 'SUCCESS') {
	      //status Update
	       $query = \Drupal::database()->update('paypal_payment_status');
	       $query->fields([ 
		 'after_amount' => $_GET['amt'],
		 'transaction_id'=> $_GET['tx'],
		 'payment_status'=> $_GET['st'],
	       ]);
	       $query->condition('custom_id', $_GET['cm']);
	       $query->condition('payment_status', 'payment_status');
	       $result = $query->execute();
	       dpm($result);
	       //application created
		$langcode = 'en';      
		$applicationArray = $application_id['cart'];
		foreach ($applicationArray as $key => $value) {
		$username = \Drupal::currentUser()->getUsername();
		$account = \Drupal::currentUser()->id();
		$institute_id = $value->getTranslation($langcode)->get('field_institute')->getValue()[0]['target_id'];
		// Get the Taxonomy details
		$institute_tid = taxonomy_term_load($institute_id);
		$institute_code = $institute_tid->getTranslation($langcode)->get('field_short_code')->getValue()[0]['value'];
		$created = date("Y").'-'.date("m");
		$node = Node::create([
			      //'field_application_path' => $application_path,
			      'type'        => 'application',
			      'title'       => $value->getTranslation($langcode)->get('title')->getValue()[0]['value'],
			      'field_documents_uploaded' => array('value' => $doc_data,),
			      'field_programme' => ['target_id' => $key,],
		       ]);
		$node->save();
		$node_id = $node->id();
		//pdf
		$filename = 'public://applications/'. $username . '_' . $account . '_' .  $node_id . '.pdf';
		//$filename = 'public://applications/'. $status1 . '_' . $account . '_' .  $node_id . '.pdf';
		$file = file_unmanaged_save_data($pdfoutput, $filename, FILE_EXISTS_REPLACE);
		$node = Node::load($node_id);
		$new_application_id = "$institute_code-$created-$node_id";
		$node->setTitle($new_application_id);
		$node->set("field_status", 'apply_apply');
		$node->set("field_application_path",$filename);
		$node->save();
	       }
	       basiccart_empty_cart();
	       //drupal_set_message("Successfully applied to " . count($applicationArray) . " programmes.");
	       drupal_set_message("<h6>Thank you!</h6><br>
                    Your transaction is successful.Successfully applied to " . count($applicationArray) . " programmes. Please note your transaction ID : ".$_GET['tx']." for future reference");

	    } else if ($tx_status == 'FAIL'){
		drupal_set_message("Please Try again Later  ");
	      
	    }
	   
	  } else{
		drupal_set_message("Invalid Transaction Please Contact Admin for any assistance.");
	    }

						
						  /*   //////////////////
						    if($_GET['tx'] && $_GET['amt'] && $_GET['st'] && $_GET['cm']) {
							    $query = \Drupal::database()->select('paypal_payment_status', 'pay_st');
							    $query->fields('pay_st', ['before_amount', 'after_amount', 'custom_id','transaction_id','payment_status']);
							    $query->condition('custom_id', $_GET['cm']);
							    $result = $query->execute()->fetchAssoc();
							    
						    if( ($result['custom_id'] == $_GET['cm']) && ($result['payment_status'] == 'pending') &&  ($result['transaction_id'] == 0) ){   
						      
							   $query = \Drupal::database()->update('paypal_payment_status');
							    $query->fields([ 
							      'after_amount' => $_GET['amt'],
							      'transaction_id'=> $_GET['tx'],
							      'payment_status'=> $_GET['st'],
							    ]);
							    $query->condition('custom_id', $_GET['cm']);
							    $query->execute();
							    
							   
							   // dpm($result);
						      //if($result['custom_id'] == $_GET['cm'] && )
							$langcode = 'en';      
							$applicationArray = $application_id['cart'];
							foreach ($applicationArray as $key => $value) {
							$username = \Drupal::currentUser()->getUsername();
							$account = \Drupal::currentUser()->id();
							$institute_id = $value->getTranslation($langcode)->get('field_institute')->getValue()[0]['target_id'];
							// Get the Taxonomy details
							$institute_tid = taxonomy_term_load($institute_id);
							$institute_code = $institute_tid->getTranslation($langcode)->get('field_short_code')->getValue()[0]['value'];
							$created = date("Y").'-'.date("m");
							$node = Node::create([
								      //'field_application_path' => $application_path,
								      'type'        => 'application',
								      'title'       => $value->getTranslation($langcode)->get('title')->getValue()[0]['value'],
								      'field_documents_uploaded' => array('value' => $doc_data,),
								      'field_programme' => ['target_id' => $key,],
							       ]);
							$node->save();
							$node_id = $node->id();
							//pdf
							$filename = 'public://applications/'. $username . '_' . $account . '_' .  $node_id . '.pdf';
							//$filename = 'public://applications/'. $status1 . '_' . $account . '_' .  $node_id . '.pdf';
							$file = file_unmanaged_save_data($pdfoutput, $filename, FILE_EXISTS_REPLACE);
							$node = Node::load($node_id);
							$new_application_id = "$institute_code-$created-$node_id";
							$node->setTitle($new_application_id);
							$node->set("field_status", 'apply_apply');
							$node->set("field_application_path",$filename);
							$node->save();
						    }
						       basiccart_empty_cart();
						       drupal_set_message("Successfully applied to " . count($applicationArray) . " programmes.");
						    } else {
						      drupal_set_message("Please Try again Later  ");
						    }  
						}
					      */
       } else {
	     drupal_set_message("Please Try again Later  ");
	}
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
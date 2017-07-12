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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
    $user = \Drupal::currentUser()->id();
    if (isset($_GET['tx']) && !empty($_GET['tx']) && isset($_GET['cc']) && !empty($_GET['cc']) && isset($_GET['amt']) && !empty($_GET['amt']) && isset($_GET['cm']) && !empty($_GET['cm']) && isset($_GET['tx']) && !empty($_GET['tx']) ) {
	 $query = \Drupal::database()->select('paypal_payment_status', 'pay_st');
	 $query->fields('pay_st', ['user_id','before_amount', 'after_amount', 'custom_id','transaction_id','payment_status']);
	 $query->condition('custom_id', $_GET['cm']);
	 $query->condition('user_id', $user);
	 $result = $query->execute()->fetchAssoc();
	 //dpm($result);
	  if ($result['user_id'] == $user && $result['custom_id'] == $_GET['cm'] ) {
	    //condition for pdt token check
	    
	     $tx = $_GET['tx'];
	     module_load_include('inc', 'paypal');
	     $tx_status = pdt_token($tx);
	     if ($tx_status == 'SUCCESS'){ 
		  if ($result['payment_status'] == 'pending' && $result['before_amount'] == $_GET['amt']){
		      $query = \Drupal::database()->update('paypal_payment_status');
		      $query->fields([ 
			      'after_amount' => $_GET['amt'],
			      'transaction_id'=> $_GET['tx'],
			      'payment_status'=> $_GET['st'],
			    ]);
		      $query->condition('custom_id', $_GET['cm']);
		      $query->condition('payment_status', 'pending');
		      $result = $query->execute();
		      module_load_include('inc', 'basiccart');
		      $application_id = basiccart_get_cart();
		      $applicationArray = $application_id['cart'];
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
			  // Sending Mail 
			    $mailConfig = \Drupal::config('workflow_mail.settings');
			    $send_mail_admin = $mailConfig->get('send_mail_admin');
			    $send_mail_user = $mailConfig->get('send_mail_user');
			    $mail_subject_admin = $mailConfig->get('mail_subject_admin');
			    $mail_subject_user = $mailConfig->get('mail_subject_user');
			    $send_test_mail = $mailConfig->get('send_test_mail');
			    //$send_test_mail = $mailConfig->get('send_test_mail');
			    $send_to = $mailConfig->get('send_to');
			    $mailManager = \Drupal::service('plugin.manager.mail');
			    $userCurrent = \Drupal::currentUser();
			    $user = user_load($userCurrent->id());
			    $curr_user_name = $user->get('name')->value;
			    $curr_user_mail = $user->get('mail')->value;
			    $old_state = 'Created';
			    $new_state = 'Apply';
			    $to = $curr_user_mail;
			    $module = 'iitinap';
			    $pgm_id = $node->getTranslation($langcode)->get('field_programme')->getValue()[0]['target_id'];
			    $programme = node_load($pgm_id);
			    $ownermail = $programme->getOwner()->getEmail();
			    $degree  = $programme->getTranslation($langcode)->get('field_degree')->getValue()[0]['value'];
			    $depart  = $programme->getTranslation($langcode)->get('field_departments')->getValue()[0]['target_id'];
			    $departs = taxonomy_term_load($depart);
			    $departments = $departs->getTranslation($langcode)->get('name')->getValue()[0]['value'];
			    $term = $programme->field_institute;
			    $tax = $term->target_id;
			    $ins = taxonomy_term_load($tax);
			    $institute = $ins->getTranslation($langcode)->get('name')->getValue()[0]['value'];
			    if($send_mail_user == '1') {
			      $params['message'] = 'Dear '.$curr_user_name.',<br><p></p> You are successfully applied for the programme.<table cellpadding = "3" cellspacing = "3" ><tr><td>Application</td><td> : <b>'.$new_application_id.'</b></td></tr><tr><td>Instittue</td><td> : '.$institute.'</td></tr><tr><td>Degree</td><td> : '.$degree.'</td></tr><tr><td>Department</td><td> : '.$departments.'</td></tr></table> <br> Regards,<br>'.$institute;
			      $key = 'application_created';
			      $params['app_id'] = $new_application_id;
			      $params['user_subject'] = $mail_subject_user;
			      $send = true;
			      $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
				   if ($result['result'] === true) {
				     drupal_set_message(t('Aplication details sent to your registered mail.'));
				   }
			    }
			    // Admin side mail
			    if($send_mail_admin == '1') {
			      $key = 'admin_mail';
			      $to = $ownermail;
			      $params['title'] = 'New Application received : '.$new_application_id;
			      $params['admin_subject'] = $mail_subject_admin;
			      $params['message'] = 'Dear Admin,<br><p></p><p>New Application received</p><p>Appication No: '.$new_application_id.'</p>';
			      $send = true;
			      $response = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
				   if ($response['result'] === true) {
				     drupal_set_message(t('Details send to Department'));
				   }
			    }			  
		        }
			basiccart_empty_cart();
			drupal_set_message("Thank you! 
			    Your transaction is successful and applied to " . count($applicationArray) . " programmes. Please note your transaction ID : ".$_GET['tx']." for future reference");		 
		  } elseif ( $result['payment_status'] == $_GET['st'] && $result['before_amount'] == $_GET['amt']){
		     //application Already created.
		    drupal_set_message("Your transaction has been completed. Please note your transaction ID : ".$_GET['tx']." for future reference. Please Contact Admin for any assistance.");
		  } 
	     } elseif ($tx_status == 'FAIL') {
		   drupal_set_message("Invalid Transaction Please Contact Admin for any assistance.");
	      } 
	  } else {
	   dpm('u r not a same user'); 
	  }	  						
    } else {
	     drupal_set_message("Get elements Empty ");
	     throw new AccessDeniedHttpException();
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
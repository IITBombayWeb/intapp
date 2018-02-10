<?php

namespace Drupal\paypal\Form;

use Drupal\file\Entity\File;
use Drupal\user\Entity\User;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\profile\Entity\Profile;
use Drupal\node\Entity\Node;
use Dompdf\Dompdf;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 *
 */
class Thankyoupage extends FormBase {
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
    $user = \Drupal::currentUser()->id();
    if (isset($_GET['tx']) && !empty($_GET['tx']) && isset($_GET['cc']) && !empty($_GET['cc']) && isset($_GET['amt']) && !empty($_GET['amt']) && isset($_GET['cm']) && !empty($_GET['cm']) && isset($_GET['tx']) && !empty($_GET['tx'])) {
      $query = \Drupal::database()->select('paypal_payment_status', 'pay_st');
      $query->fields('pay_st', ['order_id', 'user_id', 'before_amount', 'after_amount', 'custom_id', 'transaction_id', 'payment_status']);
      $query->condition('custom_id', $_GET['cm']);
      $query->condition('user_id', $user);
      $result = $query->execute()->fetchAssoc();
      if ($result['user_id'] == $user && $result['custom_id'] == $_GET['cm']) {
        // Condition for pdt token check.
        $tx = $_GET['tx'];
        module_load_include('inc', 'paypal');
        $tx_status = pdt_token($tx);
        if ($tx_status == 'SUCCESS') {
        
          if ($result['payment_status'] == 'pending' && $result['before_amount'] == $_GET['amt']) {
            $time = time();
            $query = \Drupal::database()->update('paypal_payment_status');
            $query->fields([
              'after_amount' => $_GET['amt'],
              'transaction_id' => $_GET['tx'],
              'payment_status' => $_GET['st'],
              'updated' => $time,
            ]);
            $query->condition('custom_id', $_GET['cm']);
            $query->condition('payment_status', 'pending');
            $result1 = $query->execute();
            module_load_include('inc', 'basiccart');
            $application_id = basiccart_get_cart();
            $applicationArray = $application_id['cart'];
            // Application created.
            $langcode = 'en';
            $user = \Drupal::currentUser()->getUsername();
            $username = str_replace(" ", "_", $user);
            $account = \Drupal::currentUser()->id();
            $Email = \Drupal::currentUser()->getEmail();
            $query = \Drupal::entityQuery('profile')
              ->condition('status', 1)
              ->condition('uid', $account);
            $nids = $query->execute();
            $nids = array_values($nids);
            if (isset($nids[0])) {
              $profile = Profile::load($nids[0]);
            }
            $order_id = $result['order_id'];
            foreach ($applicationArray as $key => $value) {
              $langcode = $value->language()->getId();
              $degree = $value->getTranslation($langcode)->get('field_degree')->getValue()[0]['value'];
              $depart = $value->getTranslation($langcode)->get('field_departments')->getValue()[0]['target_id'];
              $specialisation = $value->getTranslation($langcode)->get('field_specialisation')->getValue()[0]['value'];
              $departs = taxonomy_term_load($depart);
              $departments = $departs->getTranslation($langcode)->get('name')->getValue()[0]['value'];
              $in = $value->getTranslation($langcode)->get('field_institute')->getValue()[0]['target_id'];
              $ins = taxonomy_term_load($in);
              $institute = $ins->getTranslation($langcode)->get('name')->getValue()[0]['value'];
              $programme_id = $value->getTranslation($langcode)->get('nid')->getValue()[0]['value'];
              $current_date = format_date(time(), 'custom', 'dmY hms');
              // Image code.
              $uid = User::load(\Drupal::currentUser()->id());
              if(!empty($uid)){
                $user_fid = $uid->get('user_picture')->getValue();
                $fid = $user_fid[0]['target_id'];
                $file = File::load($fid);
                $path = $file->uri->value;
                $test = explode('//', $path);
                $path_array = $test[1];
                $base_path = 'sites/default/files/' . $path_array;
                $image = '<img src = "'.$base_path.'" alt="My Logo" />';
               }
              else{
                $default_image = 'sites/default/files/pictures/Icon-user.png';
               }
              
              //print_r($image);
              //exit;
              $first_name = $profile->get('field_first_name')->getValue()[0]['value'];
              $sur_name = $profile->get('field_surname')->getValue()[0]['value'];
              $gender = $profile->get('field_gender')->getValue()[0]['value'];
              $dob = $profile->get('field_date_of_birth')->getValue()[0]['value'];
              $field_permanent_address = $profile->get('field_permanent_address')->getValue()[0]['value'];
              $fpa_city = $profile->get('field_city')->getValue()[0]['value'];
              $fpa_state = $profile->get('field_state')->getValue()[0]['value'];
              $fpa_country = $profile->get('field_country')->getValue()[0]['value'];
              $fpa_pin = $profile->get('field_pin')->getValue()[0]['value'];
              $fpa_mob_num = $profile->get('field_mobile_num_com')->getValue()[0]['value'];
              $fpa_pho_num = $profile->get('field_telephone_num_com')->getValue()[0]['value'];
              $qualify_exam = $profile->get('field_name_of_the_qualifying_exa')->getValue()[0]['value'];
              $board_univ = $profile->get('field_name_of_the_board_universi')->getValue()[0]['value'];
              $year_of_passing = $profile->get('field_year_of_passing')->getValue()[0]['value'];
              $school_lst_stud = $profile->get('field_school')->getValue()[0]['value'];
              $ed_city = $profile->get('field_city_school')->getValue()[0]['value'];
              $ed_state = $profile->get('field_state_school')->getValue()[0]['value'];
              $ed_pin = $profile->get('field_pincode_school')->getValue()[0]['value'];
              $ed_country = $profile->get('field_country_school')->getValue()[0]['value'];
              $ed_md_inst = $profile->get('field_medium_of_instruction_')->getValue()[0]['value'];
              // Generate Programs Applied table.
              $perform_pgm_apply_result = "<table>
			<tr>
			  <td>Institute</td>
			  <td>Department</td>
			  <td>Degree</td>
			  <td>Specialisation</td>
			</tr>";
              $perform_pgm_apply_result .= "<tr>
		      <td>" . $institute . "</td>
		      <td>" . $departments . "</td>
		      <td>" . $degree . "</td>
		      <td>" . $specialisation . "</td></tr></table>";
              // Generate Proficiency English table.       
              $proficiency_eng = $profile->get('field_proficiency_in_english')->getValue()[0]['value'];
              $proficiency_eng_result = "<table>";
              $proficiency_array = $proficiency_eng;
              for ($i = 0; $i < count($proficiency_array); $i++) {
                $proficiency_eng_result .= "<tr>";
                foreach ($proficiency_array[$i] as $eng => $engvalue) {
                  $proficiency_eng_result .= "<td>$engvalue</td>";
                }
                $proficiency_eng_result .= "</tr>";
              }
              $proficiency_eng_result .= "</table>";

              // Generate Performance Competitive table.
              $perform_competitive = $profile->get('field_performance_in_competitive')->getValue()[0]['value'];
              $perform_competitive_result = "<table>";
              $competitive_array = $perform_competitive;
              for ($i = 0; $i < count($competitive_array); $i++) {
                $perform_competitive_result .= "<tr>";
                foreach ($competitive_array[$i] as $key1 => $value1) {
                  $perform_competitive_result .= "<td>$value1</td>";
                }
                $perform_competitive_result .= "</tr>";
              }
              $perform_competitive_result .= "</table>";
              // Generate Academic Records table.
              $academic_record = $profile->get('field_details_of_academic_record')->getValue()[0]['value'];
              $academic_record_result = "<table>";
              $academic_record_array = $academic_record;
              for ($i = 0; $i < count($academic_record_array); $i++) {
                $academic_record_result .= "<tr>";
                foreach ($academic_record_array[$i] as $key2 => $value2) {
                  $academic_record_result .= "<td>$value2</td>";
                }
                $academic_record_result .= "</tr>";
              }
              $academic_record_result .= "</table>";
              // Generate Research Professional table.
              $research_professional = $profile->get('field_research_professional_expe')->getValue()[0]['value'];
              $research_professional_result = "<table>";
              $research_professional_array = $research_professional;
              for ($i = 0; $i < count($research_professional_array); $i++) {
                $research_professional_result .= "<tr>";
                foreach ($research_professional_array[$i] as $key3 => $value3) {
                  $research_professional_result .= "<td>$value3</td>";
                }
                $research_professional_result .= "</tr>";
              }
              $research_professional_result .= "</table>";
              // Generate Employment Details table.
              $employment_details = $profile->get('field_employment_details')->getValue()[0]['value'];
              $employment_details_result = "<table>";
              $employment_details_array = $employment_details;

              for ($i = 0; $i < count($employment_details_array); $i++) {
                $employment_details_result .= "<tr>";
                foreach ($employment_details_array[$i] as $key4 => $value4) {
                  $employment_details_result .= "<td>$value4</td>";
                }
                $employment_details_result .= "</tr>";
              }
              $employment_details_result .= "</table>";

              // HTML Structure for Application pdf.
              $html = '<!DOCTYPE html>
			       <html>
				 <head>
				   <title></title>
				      <style>
					     body {
						    margin: 0 auto;
						    max-width: 1200px;
						    font-size: 14px;
					     }
					     .middle {
						    text-align: center;
					     }
					     table, td, th {
						    border: 1px solid black;
					     }
					     table {
						    border-collapse: collapse;
					     }
					     table tr:first-child td {
						    background-color: #D8D8D8;
						    text-align: left;
					     }
					     td { 
						    padding: 5px 10px;
					     }
					     .alm {
						    display:inline-block;
					     }
					     .width_75 {
						    width:75%;
					     }
					     .width_25 {
					      width:25%;
					     }
					     .width_50 {
						    width:50%;
					     }
					     .label {
						    display: inline-block;
						    width: 100px;
						    text-align: left;
					     }
					     .ml {
						    margin-left : 5px;
					     }
				      </style>
				 </head>
				    <body>
				      <div class="header middle">
					     <h2>International Student Application to PG Programs 2017</h2>
				      </div>
				      <div class="address-wrap">
					<div class="addr-inner-wrap alm width_75">
					     <div class="addr-title"><h4>' . $first_name . ' ' . $sur_name . '</h4></div>
					     <div class="alm width_50"><span class="label">Mobile:</span> <span class="ml">' . $fpa_mob_num . '</span></div>
					     <div class="alm width_50"><span class="label">Email:</span> <span class="ml">' . $Email . '</span></div>
					     <div><span class="label">Communication address:</span> <span class="ml">' . $field_permanent_address . ',' . $fpa_city . '-' . $fpa_pin . ',' . $fpa_state . ',' . $fpa_country . '</span></div>  
					</div>
					<div class="prof-img alm width_25">' . $image . '</div>
					<div class="alm width_25"><span class="label">Gender:</span>' . $gender . '</div>
					<div class="alm width_25"><span class="label">Date of Birth:</span>' . $dob . '</div>
				      </div>
				      <div class="main-container">
					<h3>Programs Applied</h3> ' . $perform_pgm_apply_result . '
                                        <h3>Proficiency in English </h3>' . $proficiency_eng_result . '
					<h3>Performance in Competitive Examination</h3>' . $perform_competitive_result . '
					<h3>Details of Academic Record (Secondary onwards)</h3>' . $academic_record_result . '
					<h3>Research/ Professional Experience, Papers published etc. (upload a separate pdf for details, if required)</h3>' . $research_professional_result . '
					<h3>Employment details</h3>' . $employment_details_result . '
				      </div>
				      <footer>
					<div class="foot clearfix">
					  <div class="f-left">
					    <label class="label"></label>
					  </div>
					  <div class="f-right">
					    <label class="label"></label> 
					  </div>
					</div>
				      </footer>
				   </body>
			       </html>';
              // Instantiate and use the dompdf class.
              $dompdf = new Dompdf();
              $dompdf->loadHtml($html);
              $dompdf->setPaper('A4', 'portrait');
              $dompdf->render();
              $pdfoutput = $dompdf->output();
              $institute_id = $value->getTranslation($langcode)->get('field_institute')->getValue()[0]['target_id'];
              // Get the Taxonomy details.
              $institute_tid = taxonomy_term_load($institute_id);
              $institute_code = $institute_tid->getTranslation($langcode)->get('field_short_code')->getValue()[0]['value'];
              $created = date("Y") . '-' . date("m");
              $node = Node::create([
                'type'        => 'application',
                'title'       => $value->getTranslation($langcode)->get('title')->getValue()[0]['value'],
                'field_documents_uploaded' => ['value' => $doc_data],
                'field_programme' => ['target_id' => $key],
              ]);
              $node->save();
              $node_id = $node->id();
              $filename = 'sites/default/private/applications/' . $username . '_' . $account . '_' . $node_id . '.pdf';
              $file = file_unmanaged_save_data($pdfoutput, $filename, FILE_EXISTS_REPLACE);
              $node = Node::load($node_id);
              $new_application_id = "$institute_code-$created-$node_id";
              $node->setTitle($new_application_id);
              $node->set("field_status", 'apply_apply');
              $node->set("field_application_path", $filename);
              $node->save();
              // Most popular programmes.
              $application_list = \Drupal::database()->insert('applications_list')
                ->fields([
                  'user_id',
                  'order_id',
                  'programme_id',
                  'application_id',
                  'created',
                ])
                ->values([
                  $account,
                  $order_id,
                  $programme_id,
                  $node->id(),
                  $time,
                ])
                ->execute();
              // Sending Mail.
              $mailConfig = \Drupal::config('workflow_mail.settings');
              $send_mail_admin = $mailConfig->get('send_mail_admin');
              $send_mail_user = $mailConfig->get('send_mail_user');
              $mail_subject_admin = $mailConfig->get('mail_subject_admin');
              $mail_subject_user = $mailConfig->get('mail_subject_user');
              $send_test_mail = $mailConfig->get('send_test_mail');
              // $send_test_mail = $mailConfig->get('send_test_mail');.
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
              $degree = $programme->getTranslation($langcode)->get('field_degree')->getValue()[0]['value'];
              $depart = $programme->getTranslation($langcode)->get('field_departments')->getValue()[0]['target_id'];
              $departs = taxonomy_term_load($depart);
              $departments = $departs->getTranslation($langcode)->get('name')->getValue()[0]['value'];
              $term = $programme->field_institute;
              $tax = $term->target_id;
              $ins = taxonomy_term_load($tax);
              $institute = $ins->getTranslation($langcode)->get('name')->getValue()[0]['value'];
              if ($send_mail_user == '1') {
                $params['message'] = 'Dear ' . $curr_user_name . ',<br><p></p> You are successfully applied for the programme.<table cellpadding = "3" cellspacing = "3" ><tr><td>Application</td><td> : <b>' . $new_application_id . '</b></td></tr><tr><td>Instittue</td><td> : ' . $institute . '</td></tr><tr><td>Degree</td><td> : ' . $degree . '</td></tr><tr><td>Department</td><td> : ' . $departments . '</td></tr></table> <br> Regards,<br>' . $institute;
                $key = 'application_created';
                $params['app_id'] = $new_application_id;
                $params['user_subject'] = $mail_subject_user;
                $send = TRUE;
                $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
                if ($result['result'] === TRUE) {
                    $app_sent = " Application details sent to your registered mail.";
                }
              }
              // Admin side mail.
              if ($send_mail_admin == '1') {
                $key = 'admin_mail';
                $to = $ownermail;
                $params['title'] = 'New Application received : ' . $new_application_id;
                $params['admin_subject'] = $mail_subject_admin;
                $params['message'] = 'Dear Admin,<br><p></p><p>New Application received</p><p>Appication No: ' . $new_application_id . '</p>';
                $send = TRUE;
                $response = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
                if ($response['result'] === TRUE) {
                  $det_send = " Details send to Department.";
                }
              }
            }
            basiccart_empty_cart();
            $notify_msg = "Thank you!  Your transaction is successful and applied to " . count($applicationArray) . " programmes. Please note your Order ID : " . $order_id . " for future reference";
          }
          elseif ($result['payment_status'] == $_GET['st'] && $result['before_amount'] == $_GET['amt']) {
            // Application Already created.
            $notify_msg = "Your transaction has been completed. Please Contact Admin for any assistance.";
          }
        }
        elseif ($tx_status == 'FAIL') {
          drupal_set_message("Invalid Transaction Please Contact Admin for any assistance.");
        }
      }
      else {
        throw new AccessDeniedHttpException();
      }
    }
    else {
      throw new AccessDeniedHttpException();
    }
    $form['message'] = [

      '#markup' => '<h4><b>' . $app_sent . '<br>' . $det_send . '<br>' . $notify_msg . '</b></h4>',
      '#prefix' => '<div class="thankyou-page">',
      '#suffix' => '</div>',
    ];
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

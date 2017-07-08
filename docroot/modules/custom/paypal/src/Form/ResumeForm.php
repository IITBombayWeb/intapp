<?php
/**
 * @file
 * Contains \Drupal\paypal\Form\ResumeForm.
 */
namespace Drupal\paypal\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class ResumeForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'paypal_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

  
  \Drupal::logger('paypal')->notice("test"); 

    
    $form['candidate_name'] = array(
      '#type' => 'textfield',
      '#title' => t('Candidate Name:'),
      '#required' => TRUE,
    );

    $form['candidate_mail'] = array(
      '#type' => 'email',
      '#title' => t('Email ID:'),
      '#required' => TRUE,
    );
       
    $form['candidate_number'] = array (
      '#type' => 'tel',
      '#title' => t('Mobile no'),
    );

    $form['candidate_dob'] = array (
      '#type' => 'date',
      '#title' => t('DOB'),
      '#required' => TRUE,
    );

    $form['candidate_gender'] = array (
      '#type' => 'select',
      '#title' => ('Gender'),
      '#options' => array(
        'Female' => t('Female'),
        'male' => t('Male'),
      ),
    );

    $form['candidate_confirmation'] = array (
      '#type' => 'radios',
      '#title' => ('Are you above 18 years old?'),
      '#options' => array(
        'Yes' =>t('Yes'),
        'No' =>t('No')
      ),
    );

    $form['candidate_copy'] = array(
      '#type' => 'checkbox',
      '#title' => t('Send me a copy of the application.'),
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
    public function validateForm(array &$form, FormStateInterface $form_state) {

      if (strlen($form_state->getValue('candidate_number')) < 10) {
        $form_state->setErrorByName('candidate_number', $this->t('Mobile number is too short.'));
      }

    }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
   // dpm($form_state);
      $candidate_name = $form_state ->getValue('candidate_name');
      $candidate_mail = $form_state ->getValue('candidate_mail');
      $candidate_number = $form_state ->getValue('candidate_number');
      $candidate_dob = $form_state ->getValue('candidate_dob');
      $candidate_gender = $form_state ->getValue('candidate_gender');
      $candidate_confirmation = $form_state ->getValue('candidate_confirmation');
      $candidate_copy = $form_state ->getValue('candidate_copy');
      \Drupal::database()->insert('paypal')
	->fields([
		'candidate_name',
		'candidate_mail',
		'candidate_number',
                'candidate_dob',
		'candidate_gender',
		'candidate_confirmation',
                'candidate_copy',
	])
	->values(array(
		$candidate_name,
		$candidate_mail,
		$candidate_number,
                $candidate_dob,
                $candidate_gender,
                $candidate_confirmation,
                $candidate_copy,
	))
	->execute();
     
   // drupal_set_message($this->t('@can_name ,Your application is being submitted!', array('@can_name' => $form_state->getValue('candidate_name'))));
/*
    foreach ($form_state->getValues() as $key => $value) {
      drupal_set_message($key . ': ' . $value);
    }
*/
   }
}
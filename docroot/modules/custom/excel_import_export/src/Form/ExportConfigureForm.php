<?php

/**
 * @file
 * Contains \Drupal\faq\Form\ExpertsForm.
 */

namespace Drupal\excel_import_export\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for the FAQ settings page - categories tab.
 */
class ExportConfigureForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'excel_export_configure_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $excel_export_settings =  \Drupal::config('excel_import_export.settings');
    
    $form['export'] = array(
      '#type' => 'details',
      '#title' => $this->t('Export configuration:'),
      '#open' => TRUE,
    );
     
    $form['export']['enable_zip'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Document export'),
      '#description' => $this->t('Enable to export supporting documents file to zip.'),
      '#default_value' => $excel_export_settings->get('enable_zip'),
    );
    
    $form['export']['export_file_location'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('XLS file Path'),
      '#description' => $this->t('Specify valid server path to hold zip exports. please ends with "/". Example: public://export/'),
      '#default_value' => $excel_export_settings->get('export_file_location'),
    );
    
    $form['export']['export_zip_locaton'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Zip file Path'),
      '#description' => $this->t('Specify valid server path to hold zip exports. please ends with "/". Example: public://export/'),
      '#default_value' => $excel_export_settings->get('export_zip_locaton'),
    );
    
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save'),
    );
    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Remove unnecessary values.
    $form_state->cleanValues();
    
    $this->configFactory()->getEditable('excel_import_export.settings')
      ->set('enable_zip', $form_state->getValue('enable_zip'))
      ->set('export_zip_locaton', $form_state->getValue('export_zip_locaton'))
      ->set('export_file_location', $form_state->getValue('export_file_location'))
      ->save();
      
    parent::submitForm($form, $form_state);
  }
  
}

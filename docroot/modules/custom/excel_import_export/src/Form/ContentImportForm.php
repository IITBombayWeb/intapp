<?php

/**
 * @file
 * Contains \Drupal\faq\Form\ExpertsForm.
 */

namespace Drupal\excel_import_export\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\excel_import_export\ExcelImportExportHelper;

/**
 * Form for the FAQ settings page - categories tab.
 */
class ContentImportForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'excel_content_import_form';
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
    
    $form['file'] = array(
      '#type' => 'managed_file',
      '#upload_location'    => "public://excel_import/",
      "#upload_validators"  => array("file_validate_extensions" => array("xls xlsx ods")),
      '#title' => t('Update application status'),
      '#description' => t('Upload a file, allowed extensions: xls, xlsx, or ods.'),
      '#required' => TRUE,
     );
    
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Import'),
    );
    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Remove unnecessary values.
    $form_state->cleanValues();
    $file_value = $form_state->getValue('file');
    $file = file_load($file_value[0]);
    $filepath = $file->get('uri')->value;
    $batch = array(
      'title' => t('Importing Applications'),
      'operations' => array(
        array('excel_import_export_batch_import', array($filepath)),
      ),
      'finished' => 'excel_import_export_batch_import_finish',
      'file' => drupal_get_path('module', 'excel_import_export') . '/excel_import_export.batch.inc',
    );
    batch_set($batch);
  }

}

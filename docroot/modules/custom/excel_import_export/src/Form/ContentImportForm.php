<?php

namespace Drupal\excel_import_export\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

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

    $form['file'] = [
      '#type' => 'managed_file',
      '#upload_location'    => "public://excel_import/",
      "#upload_validators"  => ["file_validate_extensions" => ["xls xlsx ods"]],
      '#title' => t('Update application status'),
      '#description' => t('Upload a file, allowed extensions: xls, xlsx, or ods.'),
      '#required' => TRUE,
    ];

    $form['import'] = [
      '#type' => 'markup',
      '#suffix' => t('Note: Using the downloaded excel sheet, change the status to offer given or application rejected and input it here.'),
      '#weight' => 100,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Import'),
    ];
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
    $batch = [
      'title' => $this->t('Importing Applications'),
      'operations' => [
        ['excel_import_export_batch_import', [$filepath]],
      ],
      'finished' => 'excel_import_export_batch_import_finish',
      'file' => drupal_get_path('module', 'excel_import_export') . '/excel_import_export.batch.inc',
    ];
    batch_set($batch);
  }

}

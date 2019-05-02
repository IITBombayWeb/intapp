<?php

namespace Drupal\iitinap_general\Form;

/**
 * @file
 * Contains \Drupal\iitinap_general\Form\LandingConfigForm.
 */

use Drupal\Core\Form\ConfigFormBase;

use Drupal\Core\Form\FormStateInterface;

/**
 * Contains class LandingConfigForm.
 */
class LandingConfigForm extends ConfigFormBase {

  /**
   * Implements getFormId().
   */
  public function getFormId() {

    return 'iitinap_general_landing_sets';

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    $config = $this->config('iitinap_general.settings');

    $form['instruction'] = [

      '#type' => 'text_format',

      '#title' => $this->t('Instruction'),

      '#default_value' => $config->get('instruction'),

      '#required' => TRUE,

    ];
    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = $this->config('iitinap_general.settings');

    $config->set('instruction', $form_state->getValue('instruction')['value']);

    $config->save();

    return parent::submitForm($form, $form_state);

  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {

    return [

      'iitinap_general.settings',

    ];

  }

}

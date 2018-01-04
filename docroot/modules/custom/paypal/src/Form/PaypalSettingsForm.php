<?php

/**
 * @file
 * Contains Drupal\paypal\Form\SettingsForm.
 */

namespace Drupal\paypal\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SettingsForm.
 *
 * @package Drupal\paypal\Form
 */
class PaypalSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'paypal.settings_file',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('paypal.settings_file');
    
    $form['Paypal'] = array(
    //'#title' => t('Paypal'),
    '#type' => 'fieldset',
    );
    $form['Paypal']['paypal_email'] = array(
        '#title' => t('Account Email '),
        '#type' => 'textfield',
        '#description' => t("Please Enter the paypal email."),
        '#default_value' => $config->get('email'),
    );
    $form['Paypal']['paypal_return'] = array(
        '#title' => t('Return URL '),
        '#type' => 'textfield',
        '#description' => t("Please Enter the Return URL."),
        '#default_value' => $config->get('return'),
    );
    $form['Paypal']['paypal_cancel'] = array(
        '#title' => t('Cancel URL '),
        '#type' => 'textfield',
        '#description' => t("Please Enter the Cancel URL."),
        '#default_value' => $config->get('cancel'),
    );
    $form['Paypal']['paypal_notify'] = array(
        '#title' => t('Notify URL '),
        '#type' => 'textfield',
        '#description' => t("Please Enter the Return URL."),
        '#default_value' => $config->get('notify'),
    );
    $form['Paypal']['paypal_cc'] = array(
        '#title' => t('Currency Code '),
        '#type' => 'textfield',
        '#description' => t("Please Enter the Currency Code ."),
        '#default_value' => $config->get('cc'),
    );
    $form['Paypal']['paypal_hostname'] = array(
        '#title' => t('Paypal Hostname For PDT  '),
        '#type' => 'textfield',
        '#description' => t("Paypal Hostname name For PDT Verification ."),
        '#default_value' => $config->get('hostname'),
    );
    $form['Paypal']['paypal_auth_token'] = array(
        '#title' => t('Paypal Authentication Token For PDT '),
        '#type' => 'textfield',
        '#description' => t("Paypal Authentication Token For PDT Verification ."),
        '#default_value' => $config->get('auth_token'),
    );
    
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
     
    $this->config('paypal.settings_file')
      ->set('email', $form_state->getValue('paypal_email'))
      ->set('return', $form_state->getValue('paypal_return'))
      ->set('cancel', $form_state->getValue('paypal_cancel'))
      ->set('notify', $form_state->getValue('paypal_notify'))
      ->set('cc', $form_state->getValue('paypal_cc'))
      ->set('hostname', $form_state->getValue('paypal_hostname'))
      ->set('auth_token', $form_state->getValue('paypal_auth_token'))
      ->save();
       parent::submitForm($form, $form_state);

    
  }

}

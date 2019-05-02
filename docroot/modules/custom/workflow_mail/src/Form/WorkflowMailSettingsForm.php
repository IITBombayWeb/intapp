<?php

namespace Drupal\workflow_mail\Form;

/**
 * @file
 * Contains \Drupal\workflow_mail\Form\WorkflowMailSettingsForm.
 */

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure example settings for this site.
 */
class WorkflowMailSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'workflow_mail_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'workflow_mail.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('workflow_mail.settings');

    $form['mail_config_admin'] = [
      '#type'  => 'details',
      '#title' => t('Mail settings - Admin'),
      '#open' => TRUE,
    ];
    $form['mail_config_admin']['send_mail_admin'] = [
      '#type' => 'checkbox',
      '#title' => t('Allow to send e-mails to admin'),
      '#description' => t('Checking this box will allow e-mails to be sent with the SMTP protocol.'),
      '#default_value' => $config->get('send_mail_admin'),
    ];

    $form['mail_config_admin']['mail_subject_admin'] = [
      '#type' => 'textfield',
      '#title' => t('Mail subject'),
      '#description' => t('The value in this textbox will be subject of the email'),
      '#default_value' => $config->get('mail_subject_admin'),
    ];

    $form['mail_config_user'] = [
      '#type'  => 'details',
      '#title' => t('Mail settings - User'),
      '#open' => TRUE,
    ];

    $form['mail_config_user']['send_mail_user'] = [
      '#type' => 'checkbox',
      '#title' => t('Allow to send e-mails to user'),
      '#description' => t('Checking this box will allow e-mails to be sent with the SMTP protocol.'),
      '#default_value' => $config->get('send_mail_user'),
    ];

    $form['mail_config_user']['mail_subject_user'] = [
      '#type' => 'textfield',
      '#title' => t('Mail subject'),
      '#description' => t('The value in this textbox will be subject of the email'),
      '#default_value' => $config->get('mail_subject_user'),
    ];

    /*$form['test_mail_option'] = array(
    '#type'  => 'details',
    '#title' => t('Test mail options'),
    '#open' => TRUE,
    );

    $form['test_mail_option']['send_test_mail'] = array(
    '#type' => 'checkbox',
    '#title' => t('Allow send test mail'),
    '#description' => t('Checking this box will send test mail.'),
    '#default_value' => $config->get('send_test_mail'),
    );

    $form['test_mail_option']['send_to'] = array(
    '#type' => 'textfield',
    '#title' => t('To'),
    '#description' =>
    t('Test mail send to mail id which entered in the text box'),
    '#default_value' => $config->get('send_to'),
    );
     */
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = \Drupal::service('config.factory')->getEditable('workflow_mail.settings');
    $config->set('send_mail_admin', $form_state->getValue('send_mail_admin'))->save();
    $config->set('mail_subject_admin', $form_state->getValue('mail_subject_admin'))->save();
    $config->set('send_mail_user', $form_state->getValue('send_mail_user'))->save();
    $config->set('mail_subject_user', $form_state->getValue('mail_subject_user'))->save();
    $config->set('send_test_mail', $form_state->getValue('send_test_mail'))->save();
    $config->set('send_to', $form_state->getValue('send_to'))->save();
    parent::submitForm($form, $form_state);
  }

}

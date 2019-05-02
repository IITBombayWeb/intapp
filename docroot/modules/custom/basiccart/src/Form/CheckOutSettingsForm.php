<?php

namespace Drupal\basiccart\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure checkout settings for this site.
 */
class CheckOutSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'basiccart_admin_checkout_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'basiccart.checkout',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('basiccart.checkout');
    $form['email_messages'] = [
      '#title' => $this->t('Email messages'),
      '#type' => 'fieldset',
      '#description' => $this->t('Here you can customize the mails sent to the site administrator and customer, after an order is placed.'),
    ];

    $form['email_messages']['basiccart_administrator_emails'] = [
      '#title' => $this->t('Administrator emails'),
      '#type' => 'textarea',
      '#description' => $this->t('After each placed order, an email with the order details will be sent to all the addresses from the list above. Please add one email address per line.'),
      '#default_value' => $config->get('admin_emails') ? $config->get('admin_emails') : \Drupal::config('system.site')->get('mail'),
    ];

    $form['email_messages']['basiccart_subject_admin'] = [
      '#title' => $this->t('Subject'),
      '#type' => 'textfield',
      '#description' => $this->t("Subject field for the administrator's email."),
      '#default_value' => $config->get('admin')['subject'],
    ];

    $form['email_messages']['basiccart_administer_message'] = [
      '#title' => $this->t('Admin email'),
      '#type' => 'textarea',
      '#description' => $this->t('This email will be sent to the site administrator just after an order is placed. Please see all available tokens below. For listing the products, please use: [basiccart_order:products]'),
      '#default_value' => $config->get('admin')['body'],
    ];

    $form['email_messages']['basiccart_send_emailto_user'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Send an email to the customer after an order is placed'),
      '#default_value' => $config->get('send_emailto_user'),
      // '#description' => t('Send an email to the customer after an order is
      // placed').
    ];

    $form['email_messages']['basiccart_subject_user'] = [
      '#title' => $this->t('Subject'),
      '#type' => 'textfield',
      '#description' => $this->t("Subject field for the user's email."),
      '#default_value' => $config->get('user')['subject'],
    ];

    $form['email_messages']['basiccart_user_message'] = [
      '#title' => $this->t('User email'),
      '#type' => 'textarea',
      '#description' => $this->t('This email will be sent to the user just after an order is placed. Please see all available tokens below. For listing the products, please use: [basic_cart_order:products]'),
      '#default_value' => $config->get('user')['body'],
    ];

    $form['thankyou'] = [
      '#title' => $this->t('Thank you page'),
      '#type' => 'fieldset',
      '#description' => $this->t('Thank you page customization.'),
    ];

    $form['thankyou']['basiccart_thankyou_page_title'] = [
      '#title' => $this->t('Title'),
      '#type' => 'textfield',
      '#description' => $this->t("Thank you page title."),
      '#default_value' => $config->get('thankyou')['title'],
    ];

    $form['thankyou']['basiccart_thankyou_page_text'] = [
      '#title' => $this->t('Text'),
      '#type' => 'textarea',
      '#description' => $this->t('Thank you page text.'),
      '#default_value' => $config->get('thankyou')['text'],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $admin = ["subject" => $form_state->getValue('basiccart_subject_admin'), "body" => $form_state->getValue('basiccart_administer_message')];
    $user = ["subject" => $form_state->getValue('basiccart_subject_user'), "body" => $form_state->getValue('basiccart_user_message')];
    $thankyou = ["title" => $form_state->getValue('basiccart_thankyou_page_title'), "text" => $form_state->getValue('basiccart_thankyou_page_text')];
    $this->config('basiccart.checkout')
      ->set('admin_emails', $form_state->getValue('basiccart_administrator_emails'))
      ->set('admin', $admin)
      ->set('user', $user)
      ->set('send_emailto_user', $form_state->getValue('basiccart_send_emailto_user'))
      ->set('thankyou', $thankyou)
      ->save();
    parent::submitForm($form, $form_state);
  }

}

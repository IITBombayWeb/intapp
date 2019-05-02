<?php

namespace Drupal\paypal\Form;

/**
 * @file
 * Contains \Drupal\paypal\Form\ProcessPageIPN.
 */

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

// Use Drupal\paypal\PaypalIPN.
/**
 * Class ProcessPage.
 *
 * @package Drupal\paypal\Form
 */
class ProcessPage extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ipn_notification';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /*
    $ipn = new PaypalIPN();
    // Use the sandbox endpoint during testing.
    $a = $ipn->useSandbox();
    \Drupal::logger('paypal')->notice('@type: deleted %title.',
    array(
    '@type' => $a,
    '%title' => 'test',
    ));
    $verified = $ipn->verifyIPN();
    if ($verified) {
    /*
     * Process IPN
     * A list of variables is available here:
     * https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNandPDTVariables/
     */
    /*
    \Drupal::database()->insert('paypal_payment_status')
    ->fields([
    'user_id',
    'orders_id',
    'before_amount',
    'after_amount',
    'currency_code',
    'custom_id',
    'transaction_id',
    'payment_status',
    ])
    ->values(array(
    'test',
    'test',
    'test',
    'test',
    'test',
    'test',
    'test',
    'test',
    ))
    ->execute();
    }
    // Reply with an empty 200 response
    to indicate to paypal the IPN was received correctly.
    header("HTTP/1.1 200 OK");
     */
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

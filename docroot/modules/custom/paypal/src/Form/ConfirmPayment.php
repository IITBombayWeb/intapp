<?php

namespace Drupal\paypal\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Url;
use Drupal\basiccart\Utility;
use Drupal\basiccart\Form\CartForm;

/**
 * Provides the base form for confirm payment.
 */
class ConfirmPayment extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'payment_success';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    module_load_include('inc', 'basiccart');
    $application_id = basiccart_get_cart();
    $applicationArray = $application_id['cart'];
    foreach ($applicationArray as $key => $value) {
      $iit[] = $key;
    }
    $commaList = implode(',', $iit);
    if ((isset($application_id['cart']) && is_array($application_id['cart']))) {
      // Naga paypal integration.
      $utility = new Utility();
      $cartform = new CartForm();
      $price = $utility::get_total_price();
      $total = $price->total;
      $cart_value = $cartform->get_total_price_markup();
      $rm_inr = str_replace("INR", "", $total);
      $cart = $utility::get_cart();
      $config = $utility::cart_settings();
      $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
      $user = \Drupal::currentUser();
      $form['welcome'] = [
        '#markup' => '<h4><b><h3> Do You Want to Continue payment? </b></h4>',
        '#prefix' => '<div class="basiccart-cart basiccart-grid bascart-totl">',
        '#suffix' => '</div>',
      ];
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Apply'),
        '#attributes' => ['class' => ['paypal_submit']],
      ];
      $form['actions']['cancel'] = [
        '#type' => 'submit',
        '#value' => $this->t('Cancel'),
        '#submit' => [[$this, 'cancel_submit']],
        '#attributes' => ['class' => ['paypal_submit']],
      ];
      return $form;
    }
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
    module_load_include('inc', 'basiccart');
    $application_id = basiccart_get_cart();
    $applicationArray = $application_id['cart'];
    foreach ($applicationArray as $key => $value) {
      $iits[] = $key;
    }
    $commaList = implode(',', $iits);

    if ((isset($application_id['cart']) && is_array($application_id['cart']))) {
      $paypal_url = \Drupal::config('paypal.settings_file')->get('hostname');
      $paypal_email = \Drupal::config('paypal.settings_file')->get('email');
      $return_url = \Drupal::config('paypal.settings_file')->get('return');
      $cancel_url = \Drupal::config('paypal.settings_file')->get('cancel');
      $notify_url = \Drupal::config('paypal.settings_file')->get('notify');
      $currency_code = \Drupal::config('paypal.settings_file')->get('cc');
      $cart_mthd = '_cart';
      $upload = 1;
      $no_note = 0;
      $no_shipping = 1;
      $handling = 0;
      $item_no1 = 1;
      $utility = new Utility();
      $cartform = new CartForm();
      $price = $utility::get_total_price();
      $total = $price->total;
      $cart_value = $cartform->get_total_price_markup();
      $rm_inr = str_replace("INR", "", $total);
      $cart = $utility::get_cart();
      $config = $utility::cart_settings();
      $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
      $user = \Drupal::currentUser();
      $time = time();
      $unique_id = md5(uniqid(rand(), TRUE));
      foreach ($cart['cart_quantity'] as $pgm_nid => $node) {
        $order_id[] = $pgm_nid;
        $node_load = node::load($pgm_nid);
        $iit_name = $node_load->getTranslation('en')->get('field_institute')->getValue()[0]['target_id'];
        $list_of_iits[] = $iit_name;
      }
      $filtr_iits = array_unique($list_of_iits);

      $querystring = '';
      // Firstly Append paypal account to querystring.
      $querystring .= "?business=" . urlencode($paypal_email) . "&";
      $c = 1;
      foreach ($filtr_iits as $key => $tax_term) {
        $tax_term_load = taxonomy_term_load($tax_term);
        $institute_price = $tax_term_load->getTranslation('en')->get('field_iit_app_price')->getValue()[0]['value'];
        $iit = $tax_term_load->getTranslation('en')->get('name')->getValue()[0]['value'];
        $institute_price1 = urlencode(stripslashes($institute_price));
        $iit1 = urlencode(stripslashes($iit));
        $querystring .= "item_name_" . $c . "=$iit1&";
        $querystring .= "amount_" . $c . "=$institute_price1&";
        $c++;
      }
      $querystring .= "cmd=" . urlencode($cart_mthd) . "&";
      $querystring .= "upload=" . urlencode($upload) . "&";
      $querystring .= "no_shipping=" . urlencode($no_shipping) . "&";
      $querystring .= "currency_code=" . urlencode($currency_code) . "&";
      $querystring .= "handling=" . urlencode($handling) . "&";
      $querystring .= "custom=" . urlencode($unique_id) . "&";
      // Append paypal return addresses.
      $querystring .= "return=" . urlencode(stripslashes($return_url)) . "&";
      $querystring .= "cancel_return=" . urlencode(stripslashes($cancel_url)) . "&";
      $querystring .= "notify_url=" . urlencode($notify_url);

      $insert_id = \Drupal::database()->insert('paypal_payment_status')
        ->fields([
          'user_id',
          'programme_id',
          'before_amount',
          'after_amount',
          'currency_code',
          'custom_id',
          'transaction_id',
          'payment_status',
          'created',
          'updated',
        ])
        ->values([
          $user->id(),
          $commaList,
          $total . ".00",
          0,
          $currency_code,
          $unique_id,
          'processing',
          'pending',
          $time,
          $time,
        ])
        ->execute();
      header('location:https://' . $paypal_url . '/cgi-bin/webscr' . $querystring);
      exit;

    }
  }

  /**
   * Additional Submit For profile redirection.
   */
  public function cancel_submit(array &$form, FormStateInterface $form_state) {
    $user = \Drupal::currentUser()->id();
    $url = URL::fromUserInput('/user/' . $user . '/student_application_');
    $form_state->setRedirectUrl($url);
  }

}

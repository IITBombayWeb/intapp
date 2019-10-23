<?php

namespace Drupal\basiccart\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\basiccart\Utility;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Contains the CartForm Class.
 */
class CartForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'basiccart_cart_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Getting the shopping cart.
    $connection = \Drupal::database();
    $utility = new Utility();
    $cart = $utility::get_cart();
    $config = $utility::cart_settings();
    $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $user = \Drupal::currentUser();
    if ($user->id()) {
      $form['#action'] = '/user/' . $user->id() . '/student_application_';
    }
    else {
      $form['#action'] = '/user/login/?destination=get-profile';
    }

    // And now the form.
    $form['cartcontents'] = [
      // Make the returned array come back in tree form.
      '#tree' => TRUE,
      '#prefix' => '<div class="basiccart-cart apl-pck">',
      '#suffix' => '</div>',
    ];
    // Cart elements.
    foreach ($cart['cart_quantity'] as $nid => $quantity) {
      $form['cartcontents'][$nid] = [
        '#type' => $config->get('quantity_status') ? 'textfield' : 'markup',
        '#size' => 1,
        '#quantity_id' => $nid,
        "#suffix" => '</div></div></div>',
        "#prefix" => $this->get_quantity_prefix_suffix($nid, $langcode),
        '#default_value' => $quantity,
      ];
      if ($user->id()) {
        if($nid) {
          $number_of_rows = $connection->select('basiccart_cart','c');
          $number_of_rows->condition('c.uid', $user->id());
          $number_of_rows->condition('c.id', $nid);
          $rows_count = $number_of_rows->countQuery()->execute()->fetchField();
          if(!$rows_count) {
            $entity_type = $cart['cart'][$nid]->getEntityTypeId();
            $query = $connection->insert('basiccart_cart')->fields(['uid' => $user->id(), 'id' => $nid, 'entitytype' => $entity_type, 'quantity' => $quantity])->execute();
          } else {
            $update_quantity = $connection->update('basiccart_cart')->fields(['quantity' => $quantity])->condition('uid', $user->id())->condition('id', $nid)->execute();
          }
        }
      }
    }

    // Total price.
    $form['total_price'] = [
      '#markup' => $this->get_total_price_markup(),
      '#prefix' => '<div class="basiccart-cart basiccart-grid bascart-totl">',
      '#suffix' => '</div>',
    ];

    // Buttons.
    $form['buttons'] = [
      // Make the returned array come back in tree form.
      '#tree' => TRUE,
      '#prefix' => '<div class="pck-btn"><div class="basiccart-call-to-action">',
      '#suffix' => '</div></div>',
    ];
    $form['buttons']['update'] = [
      '#type' => 'submit',
      '#value' => t("@value", ['@value' => $config->get('cart_update_button')]),
      '#name' => "update",
    ];
    if ($config->get('order_status')) {
      $form['buttons']['checkout'] = [
        '#type' => 'submit',
        '#value' => t('Checkout'),
        '#name' => "checkout",
      ];
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $utility = new Utility();
    $config = $utility::cart_settings();

    if ($config->get('quantity_status')) {

      foreach ($form_state->getValue('cartcontents') as $nid => $value) {
        $quantity = (int) $value;
        if ($quantity > 0) {
          $_SESSION['basiccart']['cart_quantity'][$nid] = $quantity;
        }
        // If the quantity is zero, we just remove the node from the cart.
        elseif ($quantity == 0) {
          unset($_SESSION['basiccart']['cart'][$nid]);
          unset($_SESSION['basiccart']['cart_quantity'][$nid]);
        }
      }
      $utility::cart_updated_message();
    }
    $config = Utility::cart_settings();
    if ($config->get('order_status') && $form_state->getValue('checkout')) {
      $url = new Url('basiccart.checkout');
      $form_state->setRedirectUrl($url);
    }
  }

  /**
   * Implements get_total_price_markup().
   */
  public function get_total_price_markup() {
    $utility = new Utility();
    $price = $utility::get_total_price();
    $total = $utility::price_format($price->total);
    $config = $utility::cart_settings();
    // Building the HTML.
    $html = '<div class="basiccart-cart-total-price-contents"> ';
    $html .= t('<div class="basiccart-total-price"> @label : <strong> @total </strong></div>', ['@label' => $config->get('total_price_label'), '@total' => $total]);
    $html .= '</div>';

    $vat_is_enabled = (int) $config->get('vat_state');
    if (!empty($vat_is_enabled) && $vat_is_enabled) {
      $vat_value = $utility::price_format($price->vat);
      $html .= '<div class="basiccart-cart-total-vat-contents">';
      $html .= '  <div class="basiccart-total-vat">' . t('Total VAT') . ': <strong>' . $vat_value . '</strong></div>';
      $html .= '</div>';
    }
    return $html;
  }

  /**
   * Implements get_quantity_prefix_suffix().
   */
  public function get_quantity_prefix_suffix($nid, $langcode) {
    $url = new Url('basiccart.cartremove', ["nid" => $nid]);
    $link = new Link('X', $url);
    $delete_link = '<span class="basiccart-delete-image-image">' . $link->toString() . '</span>';
    $cart = Utility::get_cart($nid);
    if (!empty($cart['cart'])) {
      $unit_price = $cart['cart']->getTranslation($langcode)->get('add_to_cart_price')->getValue();
      $unit_price = isset($unit_price[0]['value']) ? $unit_price[0]['value'] : 0;
      $title = $cart['cart']->getTranslation($langcode)->get('title')->getValue()[0]['value'];
      // Price and currency.
      $url = new Url('entity.node.canonical', ["node" => $nid]);
      $link = new Link($title, $url);
      $unit_price = isset($unit_price) ? $unit_price : 0;
      $unit_price = Utility::price_format($unit_price);

      // Prefix.
      $prefix = '<div class="basiccart-cart-contents tb-rw">';
      $prefix .= '  <div class="basiccart-cart-node-title tb-cel">' . $link->toString() . '<br />';
      $prefix .= '  </div>';
      $prefix .= '  <div class="basiccart-delete-image tb-cel">' . $delete_link . '</div>';
      $prefix .= '  <div class="basiccart-cart-quantity tb-cel">';
      $prefix .= '    <div class="cell">';
    }
    else {
      $prefix = '';
    }
    return $prefix;
  }

  /**
   * Implements get_cart_prefix_suffix().
   */
  public function get_cart_prefix_suffix($nid, $langcode) {
    $url = new Url('basiccart.cartremove', ["nid" => $nid]);
    $link = new Link('X', $url);
    $delete_link = '<span class="basiccart-delete-image-image">' . $link->toString() . '</span>';
    $cart = Utility::get_cart($nid);
    if (!empty($cart['cart'])) {
      $unit_price = $cart['cart']->getTranslation($langcode)->get('add_to_cart_price')->getValue();
      $unit_price = isset($unit_price[0]['value']) ? $unit_price[0]['value'] : 0;
      $title = $cart['cart']->getTranslation($langcode)->get('title')->getValue()[0]['value'];
      // Price and currency.
      $url = new Url('entity.node.canonical', ["node" => $nid]);
      $link = new Link($title, $url);
      $unit_price = isset($unit_price) ? $unit_price : 0;
      $unit_price = Utility::price_format($unit_price);

      // Prefix.
      $prefix = $link->toString() . '_' . $unit_price;
    }
    else {
      $prefix = '';
    }
    return $prefix;
  }

  /**
   * Implements convert_INR_to_USD().
   */
  public function convert_INR_to_USD($amount, $from, $to) {

    $amount = urlencode($amount);
    $from_Currency = urlencode($from);
    $to_Currency = urlencode($to);
    $url = 'http://finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s=' . $from_Currency . $to_Currency . '=X';
    $handle = @fopen($url, 'r');
    if ($handle) {
      $result = fgets($handle, 4096);
      fclose($handle);
      $allData = explode(',', $result); /* Get all the contents to an array */
      $dollarValue = $allData[1] * $amount;
      $usd = (float) $dollarValue;
    }
    else {
      $usd = 0;
    }
    return $usd;
  }

}
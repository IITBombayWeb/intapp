<?php

namespace Drupal\basiccart;

/**
 * @file
 * Contains \Drupal\basiccart\Utility.
 */

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Core\Url;
use Drupal\Core\Link;

use Drupal\node\Entity\Node;

/**
 * Contains the Utility class.
 */
class Utility {

  const FIELD_ADDTOCART    = 'addtocart';
  const FIELD_ORDERCONNECT = 'orderconnect';
  const BASICCART_ORDER    = 'basiccart_order';

  /**
   * Returns the available price formats.
   *
   * @return formats
   *   A list with the available price formats.
   */
  public static function _price_format() {
    $config = self::cart_settings();
    $currency = $config->get('currency');
    return [
      0 => t('1 234,00 @currency', ['@currency' => $currency]),
      1 => t('1 234.00 @currency', ['@currency' => $currency]),
      2 => t('1,234.00 @currency', ['@currency' => $currency]),
      3 => t('1.234,00 @currency', ['@currency' => $currency]),

      4 => t('@currency 1 234,00', ['@currency' => $currency]),
      5 => t('@currency 1 234.00', ['@currency' => $currency]),
      6 => t('@currency 1,234.00', ['@currency' => $currency]),
      7 => t('@currency 1.234,00', ['@currency' => $currency]),
    ];
  }

  /**
   * Function for shopping cart retrieval.
   *
   * @param int $nid
   *   We are using the node id to store the node in the shopping cart.
   *
   * @return mixed
   *   Returning the shopping cart contents.
   *   An empty array if there is nothing in the cart
   */
  public static function get_cart($nid = NULL) {
    if (isset($nid)) {
      return ["cart" => $_SESSION['basiccart']['cart'][$nid], "cart_quantity" => $_SESSION['basiccart']['cart_quantity'][$nid]];
    }
    // Courses added by user & didn't go to payment gateway (or) logout from site
    $user = \Drupal::currentUser();
    if ($user->id()) {
      $connection = \Drupal::database();
      $number_of_rows = $connection->select('basiccart_cart','c');
      $number_of_rows->condition('c.uid', $user->id());
      //$number_of_rows->condition('c.id', $nid);
      $rows_count = $number_of_rows->countQuery()->execute()->fetchField();
      if($rows_count) {
        $users_pending_packet_query = $connection->select('basiccart_cart','c')->fields('c', ['uid','id','entitytype','quantity']);
        $users_pending_packet_query->condition('c.uid', $user->id());
        $result = $users_pending_packet_query->execute()->fetchAll();
        foreach($result as $entity_data) {
          $entity = \Drupal::entityTypeManager()->getStorage($entity_data->entitytype)->load($entity_data->id);
          $_SESSION['basiccart']['cart'][$entity_data->id] = $entity;
          if(in_array($entity_data->id, array_keys($_SESSION['basiccart']['cart'])) && (isset($_SESSION['basiccart']['cart_quantity'][$entity_data->id]) && $_SESSION['basiccart']['cart_quantity'][$entity_data->id])) {
            $_SESSION['basiccart']['cart_quantity'][$entity_data->id] = $_SESSION['basiccart']['cart_quantity'][$entity_data->id];
          } else {
            $_SESSION['basiccart']['cart_quantity'][$entity_data->id] = $entity_data->quantity;
          }
        }
      }
    }
    if (isset($_SESSION['basiccart']['cart'])) {
      return ["cart" => $_SESSION['basiccart']['cart'], "cart_quantity" => $_SESSION['basiccart']['cart_quantity']];
    }
    // Empty cart.
    return ["cart" => [], "cart_quantity" => []];
  }

  /**
   * Returns the final price for the shopping cart.
   *
   * @return mixed $total_price
   *   The total price for the shopping cart.
   */
  public static function get_total_price() {

    $config = self::cart_settings();
    $vat = $config->get('vat_state');
    // Building the return array.
    $return = [
      'price' => 0,
      'vat' => 0,
      'total' => 0,
    ];
    $cart = self::get_cart();

    if (empty($cart)) {
      return (object) $return;
    }

    $total_price = 2000;
    /* for single applicaton wise price
    foreach ($cart['cart'] as $nid => $node) {
    $langcode = $node->language()->getId();

    $value = $node->getTranslation($langcode)->get('add_to_cart_price')->
    getValue();
    if (isset($cart['cart_quantity'][$nid]) && isset($value[0]['value'])) {
    $total_price += $cart['cart_quantity'][$nid] * $value[0]['value'];
    }
     */

    foreach ($cart['cart'] as $nid => $node) {
      $node_load = node::load($nid);
      $iit_name = $node_load->getTranslation('en')->get('field_institute')->getValue()[0]['target_id'];
      $list_of_iits[] = $iit_name;
    }
    if (is_array($list_of_iits)) {
      $filtr_iits = array_unique($list_of_iits);
      if (is_array($filtr_iits)) {
        foreach ($filtr_iits as $key => $tax_term) {
          $tax_term_load = taxonomy_term_load($tax_term);
          $institute_price = $tax_term_load->getTranslation('en')->get('field_iit_app_price')->getValue()[0]['value'];
          if (isset($institute_price)) {
            //$total_price += $institute_price;
            $total_price=2000;
          }
          $value = 0;
        }
      }
    }
    $return['price'] = 2000;

    // Checking whether to apply the VAT or not.
/*    $vat_is_enabled = (int) $config->get('vat_state');
    if (!empty($vat_is_enabled) && $vat_is_enabled) {
      $vat_value = (float) $config->get('vat_value');
      $vat_value = ($total_price * $vat_value) / 100;
      $total_price += $vat_value;
      // Adding VAT and total price to the return array.
      $return['vat'] = $vat_value;
    }*/
    $return['total'] = 2000;
    return (object) $return;
  }

  /**
   * Callback function for cart/remove/.
   *
   * @param int $nid
   *   We are using the node id to remove the node in the shopping cart.
   */
  public static function remove_from_cart($nid = NULL) {
    $nid = (int) $nid;
    if ($nid > 0) {
      unset($_SESSION['basiccart']['cart'][$nid]);
      unset($_SESSION['basiccart']['cart_quantity'][$nid]);
    }
    self::cart_updated_message();

  }

  /**
   * Shopping cart reset.
   */
  public static function empty_cart() {
    unset($_SESSION['basiccart']['cart']);
    unset($_SESSION['basiccart']['cart_quantity']);
  }

  /**
   * Formats the input $price in the desired format.
   *
   * @param float $price
   *   The price in the raw format.
   *
   * @return price
   *   The price in the custom format.
   */
  
  public static function price_format($price) {
    $config = self::cart_settings();
    $format = $config->get('price_format');
    $currency = $config->get('currency');

    $price = (int) $price;
   switch ($format) {
      case 0:
        $price = number_format($price, 2, ',', ' ') . ' ' . $currency;
        break;

      case 1:
        $price = number_format($price) . ' ' . $currency;
        break;

      case 2:
        $price = number_format($price, 2, '.', ',') . ' ' . $currency;
        break;

      case 3:
        $price = number_format($price, 2, ',', '.') . ' ' . $currency;
        break;

      case 4:
        $price = $currency . ' ' . number_format($price, 2, ',', ' ');
        break;

      case 5:
        $price = $currency . ' ' . number_format($price, 2, '.', ' ');
        break;

      case 6:
        $price = $currency . ' ' . number_format($price, 2, '.', ',');
        break;

      case 7:
        $price = $currency . ' ' . number_format($price, 2, ',', '.');
        break;

      default:
        $price = number_format($price, 2, ',', ' ') . ' ' . $currency;
        break;
    }
    return $price;
  }

  /**
   * Implements cart_settings().
   */
  public static function cart_settings() {
    $return = \Drupal::config('basiccart.settings');
    return $return;
  }

  /**
   * Implements add_to_cart().
   */
  public static function add_to_cart($id, $params = []) {
    $config = self::cart_settings();
    if (!empty($params)) {
      $quantity = $params['quantity'];
      $entitytype = $params['entitytype'];
      $quantity = $params['quantity'];

      if ($id > 0 && $quantity > 0) {
        // If a node is added more times, just update the quantity.
        $cart = self::get_cart();
        if ($config->get('quantity_status') && !empty($cart['cart']) && in_array($id, array_keys($cart['cart']))) {
          // Clicked 2 times on add to cart button. Increment quantity.
          $_SESSION['basiccart']['cart_quantity'][$id] += $quantity;
        }
        else {
          $entity = \Drupal::entityTypeManager()->getStorage($entitytype)->load($id);
          $_SESSION['basiccart']['cart'][$id] = $entity;
          $_SESSION['basiccart']['cart_quantity'][$id] = $quantity;
        }
      }
      self::cart_updated_message();
    }
  }

  /**
   * Returns the fields we need to create.
   *
   * @return mixed
   *   Key / Value pair of field name => field type.
   */
  public static function get_fields_config($type = NULL) {

    $config = Utility::cart_settings();
    $fields['bundle_types'] = $config->get('content_type');
    foreach ($config->get('content_type') as $key => $value) {
      if ($value) {
        $bundles[$key] = $key;
      }
    }
    $fields['bundle_types'] = $bundles;
    if ($type == self::FIELD_ORDERCONNECT) {

      $fields['bundle_types'] = ['basiccart_connect' => 'basiccart_connect'];
      $fields['fields'] = [
        'basiccart_contentoconnect' => [
          'type' => 'entity_reference',
          'entity_type' => 'node',
          'bundle' => 'basiccart_connect',
          'title' => t('Basic Cart Content Connect'),
          'label' => t('Basic Cart Content Connect'),
          'required' => FALSE,
          'description' => t('Basic Cart content connect'),
          'settings' => [
            'handler' => 'default:node',
            'handler_settings' => [
              "target_bundles" => $bundles,
            ],
          ],
        ],
      ];
    }
    else {
      $fields['fields'] = [
        'add_to_cart_price' => [
          'type' => 'decimal',
          'entity_type' => 'node',
          'title' => t("@title", ['@title' => $config->get('price_label')]),
          'label' => t("@label", ['@label' => $config->get('price_label')]),
          'required' => FALSE,
          'description' => t("Please enter this item's price."),
          'widget' => ['type' => 'number'],
          'formatter' => [
            'default' => [
              'label' => 'inline',
              'type' => 'int',
              'weight' => 11,
            ],
            'search_result' => 'default',
            'teaser' => 'default',
          ],
        ],
        'add_to_cart' => [
          'type' => 'addtocart',
          'entity_type' => 'node',
          'title' => t("@value", ['@value' => $config->get('add_to_cart_button')]),
          'label' => t("@value", ['@value' => $config->get('add_to_cart_button')]),
          'required' => FALSE,
          'description' => '',
          'widget' => ['type' => 'addtocart'],
          'formatter' => [
            'default' => [
              'label' => 'hidden',
              'weight' => 11,
              'type' => $config->get('quantity_status') ? 'addtocartwithquantity' : 'addtocart',
            ],
            'search_result' => [
              'label' => 'hidden',
              'weight' => 11,
              'type' => 'addtocart',
            ],
            'teaser' => [
              'label' => 'hidden',
              'weight' => 11,
              'type' => 'addtocart',
            ],
          ],
        ],
      ];
    }
    return (object) $fields;
  }

  /**
   * Implements create_fields().
   */
  public static function create_fields($type = NULL) {

    $fields = ($type == self::FIELD_ORDERCONNECT) ? self::get_fields_config(self::FIELD_ORDERCONNECT) : self::get_fields_config();
    $view_modes = \Drupal::entityManager()->getViewModes('node');
    foreach ($fields->fields as $field_name => $config) {
      $field_storage = FieldStorageConfig::loadByName($config['entity_type'], $field_name);
      if (empty($field_storage)) {
        FieldStorageConfig::create([
          'field_name' => $field_name,
          'entity_type' => $config['entity_type'],
          'type' => $config['type'],
        ])->save();
      }
    }
    foreach ($fields->bundle_types as $bundle) {
      foreach ($fields->fields as $field_name => $config) {
        $config_array = [
          'field_name' => $field_name,
          'entity_type' => $config['entity_type'],
          'bundle' => $bundle,
          'label' => $config['label'],
          'required' => $config['required'],
        ];
        if (isset($config['settings'])) {
          $config_array['settings'] = $config['settings'];
        }
        $field = FieldConfig::loadByName($config['entity_type'], $bundle, $field_name);
        if (empty($field) && $bundle !== "" && !empty($bundle)) {
          FieldConfig::create($config_array)->save();
        }

        if ($bundle !== "" && !empty($bundle)) {
          if (!empty($field)) {
            $field->setLabel($config['label'])->save();
            $field->setRequired($config['required'])->save();
          }
          if ($config['widget']) {
            entity_get_form_display($config['entity_type'], $bundle, 'default')
              ->setComponent($field_name, $config['widget'])
              ->save();
          }
          if ($config['formatter']) {
            foreach ($config['formatter'] as $view => $formatter) {
              if (isset($view_modes[$view]) || $view == "default") {
                entity_get_display($config['entity_type'], $bundle, $view)
                  ->setComponent($field_name, !is_array($formatter) ? $config['formatter']['default'] : $config['formatter']['default'])
                  ->save();
              }
            }
          }
        }
      }
    }
  }

  /**
   * Implements cart_updated_message().
   */
  public static function cart_updated_message() {
    $config = Utility::cart_settings();
    drupal_set_message(t("@message", ['@message' => $config->get('cart_updated_message')]));
  }

  /**
   * Implements order_connect_fields().
   */
  public static function order_connect_fields() {
    self::create_fields(self::FIELD_ORDERCONNECT);
  }

  /**
   * Implements is_basiccart_order().
   */
  public static function is_basiccart_order($bundle) {
    if ($bundle == self::BASICCART_ORDER) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Implements checkout_settings().
   */
  public static function checkout_settings() {
    $return = \Drupal::config('basiccart.checkout');
    return $return;
  }

  /**
   * Implements get_cart_content().
   */
  public function get_cart_content($status = 'add') {
    $utility = $this;
    $config = $utility->cart_settings();
    $cart = $utility->get_cart();
    $quantity_enabled = $config->get('quantity_status');
    $total_price = $utility->get_total_price();
    $cart_cart = isset($cart['cart']) ? $cart['cart'] : [];
    $output = '';
    if (empty($cart_cart)) {
      $output .= '<div class="basiccart-grid basic-cart-block empt-cart welcome">' . t($config->get('empty_cart')) . '</div>';
    }
    else {

      $output .= '<div class="basiccart-grid basic-cart-block test bscart-pop clearfix">';
      $output .= '<div class="tle clearfix"> Application Packets </div><div class="cart-x cell">x</div>';
      if (is_array($cart_cart) && count($cart_cart) >= 1) {
        foreach ($cart_cart as $nid => $node) {
          $langcode = $node->language()->getId();
          $price_value = $node->getTranslation($langcode)->get('add_to_cart_price')->getValue();
          $title = $node->getTranslation($langcode)->get('title')->getValue();
          $url = new Url('entity.node.canonical', ["node" => $nid]);
          $link = new Link($title[0]['value'], $url);
          $output .= '<div class="basicart-view clearfix"><div class="basiccart-cart-contents clearfix">
          <div class="basiccart-delete-image cell">
            <span class="basiccart-delete-image-image">
            <a href="/cart/remove/' . $nid . '">X</a>
            </span>
          </div>
          <div class="basiccart-cart-node-title cell">' . $link->toString() . '</div>';
          if ($quantity_enabled) {
            $output .= '<div class="basiccart-cart-quantity cell">' . $cart['cart_quantity'][$nid] . '</div>';
            $output .= '<div class="basiccart-cart-x cell">x</div>';
          }
          $output .= ' </div></div>';
        }
        if (!empty($config->get('vat_state'))) {
          $output .= '<div class="basiccart-block-total-vat-contents">
          <div class="basiccart-total-vat cell"> Total VAT : <strong>' . $utility->price_format($total_price->vat) . '</strong></div>
        </div>';
        }
        $url = new Url('basiccart.cart');
        $link = "<a href='" . $url->toString() . "' class='button'>" . t("@message", ['@message' => $config->get('view_cart_button')]) . "</a>";
        $output .= '<div class="basiccart-cart-checkout-button basiccart-cart-checkout-button-block">
        ' . $link . '
      </div>';
      }
      $output .= '</div>';
    }
    return $output;
  }

}
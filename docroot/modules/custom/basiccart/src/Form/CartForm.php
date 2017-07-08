<?php

namespace Drupal\basiccart\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\basiccart\Utility;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\user\Entity\user;

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
    $Utility = new Utility();  
    $cart = $Utility::get_cart();
    $config = $Utility::cart_settings();  
    $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $user = \Drupal::currentUser();
	
    $form['#action'] ='/user/'.$user->id().'/student_application_';
 
    // And now the form.
    $form['cartcontents'] = array(
      // Make the returned array come back in tree form.
      '#tree' => TRUE,
      '#prefix' => '<div class="basiccart-cart apl-pck">',
      '#suffix' => '</div>',
    );
    // Cart elements.
    foreach ($cart['cart_quantity'] as $nid => $quantity) {
      $form['cartcontents'][$nid] = array(
        '#type' => $config->get('quantity_status') ? 'textfield' : 'markup',
        '#size' => 1,
        '#quantity_id'  => $nid,
        "#suffix" =>    '</div></div></div>',
        "#prefix" => $this->get_quantity_prefix_suffix($nid,$langcode),
        '#default_value' => $quantity,
        // TO DO  
       //'#url' => $cart['cart'][$nid]->urlInfo('canonical'),
        //'#theme' => 'basiccart_quantity',
      );
    }
  
	// Total price.
    $form['total_price'] = array(
      '#markup' => $this->get_total_price_markup(),
      '#prefix' => '<div class="basiccart-cart basiccart-grid bascart-totl">',
      '#suffix' => '</div>',
     // '#theme' => 'cart_total_price',
    );
    
    // Buttons.
    $form['buttons'] = array(
      // Make the returned array come back in tree form.
      '#tree' => TRUE,
      '#prefix' => '<div class="pck-btn"><div class="basiccart-call-to-action">',
      '#suffix' => '</div></div>',
    );		
    $form['buttons']['update'] = array(
      '#type' => 'submit',
      '#value' =>  t($config->get('cart_update_button')),
      '#name' => "update",
    );
    if($config->get('order_status')) {
       $form['buttons']['checkout'] = array(
          '#type' => 'submit',
          '#value' =>  t('Checkout'),
          '#name' => "checkout",
       );
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
 public function submitForm(array &$form, FormStateInterface $form_state) {
    $Utility = new Utility();
    $config = $Utility::cart_settings();  

    if($config->get('quantity_status')) {

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
      $Utility::cart_updated_message();
    }
    $config = Utility::cart_settings();
    if($config->get('order_status') && $form_state->getValue('checkout')) {
      $url = new Url('basiccart.checkout');    
      $form_state->setRedirectUrl($url);
    }
  }


  public function get_total_price_markup(){
    $Utility = new Utility();
    $price = $Utility::get_total_price();
    $total = $Utility::price_format($price->total);
    $config = $Utility::cart_settings();
    // Building the HTML.
    $html  = '<div class="basiccart-cart-total-price-contents">';
    $html .= '  <div class="basiccart-total-price">' . t($config->get('total_price_label')) . ': <strong>' . $total . '</strong></div>';
    $html .= '</div>';
    
    $vat_is_enabled = (int) $config->get('vat_state');
    if (!empty ($vat_is_enabled) && $vat_is_enabled) {
      $vat_value = $Utility::price_format($price->vat);
      $html .= '<div class="basiccart-cart-total-vat-contents">';
      $html .= '  <div class="basiccart-total-vat">' . t('Total VAT') . ': <strong>' . $vat_value . '</strong></div>';
      $html .= '</div>';
    }
    return $html;
  }

  public function get_quantity_prefix_suffix($nid,$langcode) {
    $url = new Url('basiccart.cartremove', array("nid" => $nid));
    $link = new Link('X',$url);
    $delete_link = '<span class="basiccart-delete-image-image">'.$link->toString().'</span>';
    $cart = Utility::get_cart($nid);
     if(!empty($cart['cart'])) {
    $unit_price = $cart['cart']->getTranslation($langcode)->get('add_to_cart_price')->getValue();  
    $unit_price = isset($unit_price[0]['value']) ? $unit_price[0]['value'] : 0;
    $title = $cart['cart']->getTranslation($langcode)->get('title')->getValue()[0]['value'];
    // Price and currency.
    $url = new Url('entity.node.canonical',array("node"=>$nid));
    $link = new Link($title,$url);
    $unit_price = isset($unit_price) ? $unit_price : 0;
    $unit_price = Utility::price_format($unit_price);
    
    // Prefix.
    $prefix  = '<div class="basiccart-cart-contents tb-rw">';
    $prefix .= '  <div class="basiccart-cart-node-title tb-cel">' . $link->toString() . '<br />';
    $prefix .= '  </div>';
    //$prefix .= '  <div class="basiccart-cart-unit-price tb-cel"><strong>' . $unit_price . '</strong></div>';
    $prefix .= '  <div class="basiccart-delete-image tb-cel">' . $delete_link . '</div>';
    $prefix .= '  <div class="basiccart-cart-quantity tb-cel">';
    $prefix .= '    <div class="cell">';
    }else{
      $prefix = '';
    }
    return $prefix;
  }
 public function get_cart_prefix_suffix($nid,$langcode) {
    $url = new Url('basiccart.cartremove', array("nid" => $nid));
    $link = new Link('X',$url);
    $delete_link = '<span class="basiccart-delete-image-image">'.$link->toString().'</span>';
    $cart = Utility::get_cart($nid);
     if(!empty($cart['cart'])) {
    $unit_price = $cart['cart']->getTranslation($langcode)->get('add_to_cart_price')->getValue();  
    $unit_price = isset($unit_price[0]['value']) ? $unit_price[0]['value'] : 0;
    $title = $cart['cart']->getTranslation($langcode)->get('title')->getValue()[0]['value'];
    // Price and currency.
    $url = new Url('entity.node.canonical',array("node"=>$nid));
    $link = new Link($title,$url);
    $unit_price = isset($unit_price) ? $unit_price : 0;
    $unit_price = Utility::price_format($unit_price);
    
    // Prefix.
    $prefix  = $link->toString().'_'.$unit_price;
    }else{
      $prefix = '';
    }
    return $prefix;
  }
  public function convert_INR_to_USD($amount,$from,$to) {

       $amount = urlencode($amount);
       $from_Currency = urlencode($from);
       $to_Currency = urlencode($to);
       $url = 'http://finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s='. $from_Currency . $to_Currency .'=X';
       $handle = @fopen($url, 'r');
       if ($handle) {
           $result = fgets($handle, 4096);
           fclose($handle);
           $allData = explode(',',$result); /* Get all the contents to an array */
           $dollarValue = $allData[1]*$amount;
           $usd = (float)$dollarValue;
       }else{
           $usd = 0;
       }
       return $usd;
  }
}


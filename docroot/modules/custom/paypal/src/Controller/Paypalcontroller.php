<?php
/**
 * @file
 * Contains \Drupal\basiccart\Controller\CartController.
 */

namespace Drupal\paypal\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Contains the cart controller.
 */
class PaypalController extends ControllerBase {
 
  /*
  public function remove_from_cart($nid) {
    \Drupal::service('page_cache_kill_switch')->trigger();
    $cart = Utility::remove_from_cart($nid); 
    return new RedirectResponse(Url::fromUri($_SERVER['HTTP_REFERER'])->toString());  
  }
  */
  public function test(){
    
  $build = array(
      '#type' => 'markup',
      '#markup' => t('Hello World!'),
    );
    return $build;
  }
 
}
  

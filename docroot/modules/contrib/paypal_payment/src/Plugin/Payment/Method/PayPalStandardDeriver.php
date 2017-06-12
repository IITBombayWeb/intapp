<?php

/**
 * Contains \Drupal\paypal_payment\Plugin\Payment\Method\PayPalStandardDeriver.
 */

namespace Drupal\paypal_payment\Plugin\Payment\Method;

/**
 * Derives payment method plugin definitions based on configuration entities.
 */
class PayPalStandardDeriver extends PayPalBasicDeriver {

  /**
   * @inheritDoc
   */
  protected function getId() {
    return 'paypal_payment_standard';
  }

}

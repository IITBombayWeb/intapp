<?php

/**
 * Contains \Drupal\paypal_payment\Plugin\Payment\Method\PayPalExpressDeriver.
 */

namespace Drupal\paypal_payment\Plugin\Payment\Method;

/**
 * Derives payment method plugin definitions based on configuration entities.
 */
class PayPalExpressDeriver extends PayPalBasicDeriver {

  /**
   * @inheritDoc
   */
  protected function getId() {
    return 'paypal_payment_express';
  }

}

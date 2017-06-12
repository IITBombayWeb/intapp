<?php

/**
 * Contains \Drupal\paypal_payment\Plugin\Payment\Method\PayPalStandard.
 */

namespace Drupal\paypal_payment\Plugin\Payment\Method;

/**
 * PayPal Standard payment method.
 *
 * @PaymentMethod(
 *   deriver = "\Drupal\paypal_payment\Plugin\Payment\Method\PayPalStandardDeriver",
 *   id = "paypal_payment_standard",
 *   operations_provider = "\Drupal\paypal_payment\Plugin\Payment\Method\PayPalStandardOperationsProvider",
 * )
 */
class PayPalStandard extends PayPalBasic {

  /**
   * {@inheritdoc}
   */
  public function getApiContext() {
    // TODO: Implement getApiContext() method.
  }

}

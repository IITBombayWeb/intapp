<?php

/**
 * Contains \Drupal\paypal_payment\Plugin\Payment\Method\PayPalBasicOperationsProvider.
 */

namespace Drupal\paypal_payment\Plugin\Payment\Method;

use Drupal\payment\Plugin\Payment\Method\BasicOperationsProvider;

/**
 * Abstract class for PayPal payment method operation providers.
 */
abstract class PayPalBasicOperationsProvider extends BasicOperationsProvider {

  /**
   * {@inheritdoc}
   */
  protected function getPaymentMethodConfiguration($plugin_id) {
    $entity_id = explode(':', $plugin_id)[1];

    return $this->paymentMethodConfigurationStorage->load($entity_id);
  }

}

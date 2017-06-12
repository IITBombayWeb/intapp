<?php

/**
 * Contains \Drupal\paypal_payment\Plugin\Payment\Method\PayPalExpress.
 */

namespace Drupal\paypal_payment\Plugin\Payment\Method;

use Drupal\Core\PhpStorage\PhpStorageFactory;
use Drupal\Core\Url;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

/**
 * PayPal Express payment method.
 *
 * @PaymentMethod(
 *   deriver = "\Drupal\paypal_payment\Plugin\Payment\Method\PayPalExpressDeriver",
 *   id = "paypal_payment_express",
 *   operations_provider = "\Drupal\paypal_payment\Plugin\Payment\Method\PayPalExpressOperationsProvider",
 * )
 */
class PayPalExpress extends PayPalBasic {

  /**
   * @inheritDoc
   */
  public function getWebhookUrl() {
    $configuration = $this->getPluginDefinition();
    list(, $id) = explode(':', $configuration['id']);
    return self::webhookUrl($id);
  }

  /**
   * @inheritDoc
   */
  public function getWebhookId() {
    $configuration = $this->getPluginDefinition();
    return $configuration['webhookId'];
  }


  /**
   * {@inheritdoc}
   */
  public function getApiContext($type) {
    return self::apiContext($this->getPluginDefinition(), $type);
  }

  public static function apiContext($configuration, $type) {
    $apiContext = new ApiContext(
      new OAuthTokenCredential(
        $configuration['clientId'],
        $configuration['clientSecret']
      )
    );

    $storage = PhpStorageFactory::get('paypal_api_context');
    if (!$storage->exists('auth.cache')) {
      $storage->save('auth.cache', '');
    }

    $apiContext->setConfig([
      'mode' => $configuration['production'] ? 'live' : 'sandbox',
      'log.LogEnabled' => $configuration['logging'][$type],
      'log.FileName' => file_directory_temp() . '/DrupalPayPal.log',
      'log.LogLevel' => $configuration['loglevel'],
      'cache.enabled' => TRUE,
      'cache.FileName' => DRUPAL_ROOT . '/' . $storage->getFullPath('auth.cache'),
    ]);

    return $apiContext;
  }

  public static function webhookUrl($id) {
    $webhook = new Url('paypal_payment.webhook',
      ['payment_method_id' => $id], ['absolute' => TRUE]);
    return $webhook->toString(TRUE)->getGeneratedUrl();
  }

}

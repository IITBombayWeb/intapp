<?php

/**
 * @file
 * Contains \Drupal\paypal_payment\Controller\Webhook.
 */

namespace Drupal\paypal_payment\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\paypal_payment\Plugin\Payment\Method\PayPalBasic;
use PayPal\Api\VerifyWebhookSignature;
use PayPal\Api\WebhookEvent;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles the "webhook" route.
 */
class Webhook extends ControllerBase {

  public function access($payment_method_id) {
    return AccessResult::allowedIf($this->verify($payment_method_id));
  }

  /**
   * @param string $payment_method_id
   * @return bool
   */
  private function verify(string $payment_method_id) {
    $request = \Drupal::request();
    try {
      /** @var PayPalBasic $payment_method */
      $payment_method = \Drupal\payment\Payment::methodManager()->createInstance('paypal_payment_express:' . $payment_method_id);
      if (!($payment_method instanceof PayPalBasic)) {
        throw new \Exception('Unsupported web hook');
      }

      $webhook = new WebhookEvent($request->getContent());

      $resource = new VerifyWebhookSignature();
      $resource->setAuthAlgo($request->headers->get('paypal-auth-algo'));
      $resource->setCertUrl($request->headers->get('paypal-cert-url'));
      $resource->setTransmissionId($request->headers->get('paypal-transmission-id'));
      $resource->setTransmissionSig($request->headers->get('paypal-transmission-sig'));
      $resource->setTransmissionTime($request->headers->get('paypal-transmission-time'));
      $resource->setWebhookEvent($webhook);
      $resource->setWebhookId($payment_method->getWebhookId());

      $response = $resource->post($payment_method->getApiContext($payment_method::PAYPAL_CONTEXT_TYPE_WEBHOOK));
      if ($response->getVerificationStatus() == 'SUCCESS') {
        return TRUE;
      }

    } catch (\Exception $ex) {
      // TODO: Error handling
    }

    return FALSE;
  }

  /**
   * PayPal calls this after the payment status has been changed. PayPal only
   * gives us an id leaving us with the responsibility to get the payment status.
   *
   * @param string $payment_method_id
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function execute(string $payment_method_id) {
    $request = \Drupal::request();
    $body = $request->getContent();

    # TODO: Implement the webhook, access has already been verified.

    return new Response();
  }

}

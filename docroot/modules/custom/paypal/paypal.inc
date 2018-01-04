<?php

/**
 * @file
 */

/**
 *
 */
function pdt_token($tx) {
  $paypal_url = \Drupal::config('paypal.settings_file')->get('hostname');
  $paypal_auth_token = \Drupal::config('paypal.settings_file')->get('auth_token');
  // Change to www.sandbox.paypal.com to test against sandbox.
  $pp_hostname = $paypal_url;
  // Read the post from PayPal system and add 'cmd'.
  $req = 'cmd=_notify-synch';
  $tx_token = $tx;
  $auth_token = $paypal_auth_token;
  $req .= "&tx=$tx_token&at=$auth_token";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "https://$pp_hostname/cgi-bin/webscr");
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
  // Set cacert.pem verisign certificate path in curl using 'CURLOPT_CAINFO' field here,
  // if your server does not bundled with default verisign certificates.
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ["Host: $pp_hostname"]);
  $res = curl_exec($ch);
  curl_close($ch);
  if (!$res) {
    // HTTP ERROR.
  }
  else {
    // Parse the data.
    $lines = explode("\n", trim($res));
    $keyarray = [];
    if (strcmp($lines[0], "SUCCESS") == 0) {
      $status = $lines[0];
    }
    elseif (strcmp($lines[0], "FAIL") == 0) {
      $status = $lines[0];
    }
  }
  return $status;
}

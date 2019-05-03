<?php

/**
 * @file
 * Contains Get and empty cart functions.
 */

use Drupal\basiccart\Utility;

/**
 * Implements basiccart_get_cart().
 *
 * @return cart
 *   Cart Value
 */
function basiccart_get_cart() {
  $cart = Utility::get_cart();
  return $cart;
}

/**
 * Implements basiccart_empty_cart().
 *
 * @return cart
 *   Cart value
 */
function basiccart_empty_cart() {
  $cart = Utility::empty_cart();
  return $cart;
}

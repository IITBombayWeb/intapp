<?php

namespace Drupal\basiccart\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\basiccart\Utility;

/**
 * Configure basiccart settings for this site.
 */
class CartSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'basiccart_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'basiccart.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('basiccart.settings');
    $node_types = node_type_get_types();
    if (empty($node_types)) {
      return NULL;
    }

    $options = [];
    foreach ($node_types as $node_type => $type) {
      if ($node_type == 'basiccart_order' || $node_type == 'basiccart_connect') {
        continue;
      }
      $options[$node_type] = $type->get('name');
    }

    $form['content_type'] = [
      '#title' => $this->t('Content type selection'),
      '#type' => 'fieldset',
      '#description' => $this->t('Please select the content types for which you wish to have the "Add to cart" option.'),
    ];
    $form['content_type']['basiccart_content_types'] = [
      '#title' => $this->t('Content types'),
      '#type' => 'checkboxes',
      '#options' => $options,
      '#default_value' => $config->get('content_type'),
    ];
    $form['content_type']['basiccart_all_content_types'] = [
      '#type' => 'hidden',
      '#default_value' => $config->get('content_type'),
    ];
    $form['currency'] = [
      '#title' => $this->t('Currency and price'),
      '#type' => 'fieldset',
      '#description' => $this->t('Please select the currency in which the prices will be calculated.'),
    ];
    $form['currency']['basiccart_currency_status'] = [
      '#title' => $this->t('Enable Currency'),
      '#type' => 'checkbox',
      '#description' => $this->t('Enable Currency for your cart price,this will available only if price is enabled'),
      '#default_value' => $config->get('currency_status'),
    ];
    $form['currency']['basiccart_currency'] = [
      '#title' => $this->t('Currency'),
      '#type' => 'textfield',
      '#description' => $this->t("Please choose the currency."),
      '#default_value' => $config->get('currency'),
    ];
    $form['currency']['basiccart_price_format'] = [
      '#title' => $this->t('Price format'),
      '#type' => 'select',
      '#options' => Utility::_price_format(),
      '#description' => $this->t("Please choose the format in which the price will be shown."),
      '#default_value' => $config->get('price_format'),
    ];
    $form['currency']['basiccart_quantity_status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable quantity'),
      '#default_value' => $config->get('quantity_status'),
      '#description' => $this->t('Enable quantity  for your cart, if quantity not enabled you can add to a cart without quantity'),
    ];
    $form['currency']['basiccart_price_status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable price'),
      '#default_value' => $config->get('price_status'),
      '#description' => $this->t('Enable price for your cart, if price not enabled you can add to a cart without price'),
    ];
    $form['currency']['basiccart_total_price_status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable total price'),
      '#default_value' => $config->get('total_price_status'),
      '#description' => $this->t('Enable total price for your cart, if total price is not enabled your cart would not have total price calcutaion'),
    ];
    $form['vat'] = [
      '#title' => $this->t('VAT'),
      '#type' => 'fieldset',
    ];
    $form['vat']['basiccart_vat_state'] = [
      '#title' => $this->t('Check if you want to apply the VAT tax on the total amount in the checkout process.'),
      '#type' => 'checkbox',
      '#default_value' => $config->get('vat_state'),
    ];
    $form['vat']['basiccart_vat_value'] = [
      '#title' => $this->t('VAT value'),
      '#type' => 'textfield',
      '#description' => $this->t("Please enter VAT value."),
      '#field_suffix' => '%',
      '#size' => 10,
      '#default_value' => $config->get('vat_value'),
    ];
    $form['order'] = [
      '#title' => $this->t('Basic Cart Order'),
      '#type' => 'fieldset',
    ];
    $form['order']['basiccart_order_status'] = [
      '#title' => $this->t('Check if you want to create order for the cart.'),
      '#type' => 'checkbox',
      '#default_value' => $config->get('order_status'),
    ];
    $form['redirect'] = [
      '#title' => $this->t('Redirect user after adding an item to the shopping cart'),
      '#type' => 'fieldset',
    ];
    $form['redirect']['basiccart_add_to_cart_redirect'] = [
      '#title' => $this->t('Add to cart redirect'),
      '#type' => 'textfield',
      '#description' => $this->t("Enter the page you wish to redirect the customer to when an item is added to the cart, or &lt;none&gt; for no redirect."),
      '#default_value' => $config->get('add_to_cart_redirect'),
    ];
    $form['configure'] = [
      '#title' => $this->t('Configure texts'),
      '#type' => 'fieldset',
    ];
    $form['configure']['basiccart_cart_page_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Page title'),
      '#default_value' => $config->get('cart_page_title'),
      '#description' => $this->t('Please configure page title to be shown in your cart page'),
    ];
    $form['configure']['basiccart_empty_cart'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Empty Cart'),
      '#default_value' => $config->get('empty_cart'),
      '#description' => $this->t('Please configure a text when your cart is empty'),
      '#maxlength' => 500,
    ];
    $form['configure']['basiccart_cart_block_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Block Title'),
      '#default_value' => $config->get('cart_block_title'),
      '#description' => $this->t('Please configure your cart block title'),
    ];
    $form['configure']['basiccart_view_cart_button'] = [
      '#type' => 'textfield',
      '#title' => $this->t('View cart'),
      '#default_value' => $config->get('view_cart_button'),
      '#description' => $this->t('Please configure your text on view cart button'),
    ];
    $form['configure']['basiccart_cart_update_button'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Update cart button'),
      '#default_value' => $config->get('cart_update_button'),
      '#description' => $this->t('Please configure your text on update cart button'),
    ];
    $form['configure']['basiccart_cart_updated_message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cart updated message'),
      '#default_value' => $config->get('cart_updated_message'),
      '#description' => $this->t('Please configure message to show after the cart updated'),
    ];
    $form['configure']['basiccart_quantity_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Quantity label'),
      '#default_value' => $config->get('quantity_label'),
      '#description' => $this->t('Please configure your text for quantity label,this will available only if quantity is enabled'),
    ];
    $form['configure']['basiccart_price_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Price label'),
      '#default_value' => $config->get('price_label'),
      '#description' => $this->t('Please configure your text for price label,this will available only if price is enabled'),
    ];
    $form['configure']['basiccart_total_price_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Total price label'),
      '#default_value' => $config->get('total_price_label'),
      '#description' => $this->t('Please configure your text for total price label,this will available only if total price is enabled'),
    ];
    $form['configure']['basiccart_add_to_cart_button'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Add to Cart'),
      '#default_value' => $config->get('add_to_cart_button'),
      '#description' => $this->t('Please configure your text on update cart button'),
    ];
    $form['configure']['basiccart_added_to_cart_message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Added to Cart'),
      '#default_value' => $config->get('added_to_cart_message'),
      '#description' => $this->t('Please configure your text on to appear after the entity is added to cart'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $content_types = $this->config('basiccart.settings')->get('content_type');
    $this->config('basiccart.settings')
      ->set('cart_page_title', $form_state->getValue('basiccart_cart_page_title'))
      ->set('empty_cart', $form_state->getValue('basiccart_empty_cart'))
      ->set('cart_block_title', $form_state->getValue('basiccart_cart_block_title'))
      ->set('view_cart_button', $form_state->getValue('basiccart_view_cart_button'))
      ->set('cart_update_button', $form_state->getValue('basiccart_cart_update_button'))
      ->set('cart_updated_message', $form_state->getValue('basiccart_cart_updated_message'))
      ->set('quantity_status', $form_state->getValue('basiccart_quantity_status'))
      ->set('quantity_label', $form_state->getValue('basiccart_quantity_label'))
      ->set('price_status', $form_state->getValue('basiccart_price_status'))
      ->set('price_label', $form_state->getValue('basiccart_price_label'))
      ->set('price_format', $form_state->getValue('basiccart_price_format'))
      ->set('total_price_status', $form_state->getValue('basiccart_total_price_status'))
      ->set('total_price_label', $form_state->getValue('basiccart_total_price_label'))
      ->set('currency_status', $form_state->getValue('basiccart_currency_status'))
      ->set('currency', $form_state->getValue('basiccart_currency'))
      ->set('vat_state', $form_state->getValue('basiccart_vat_state'))
      ->set('vat_value', $form_state->getValue('basiccart_vat_value'))
      ->set('add_to_cart_button', $form_state->getValue('basiccart_add_to_cart_button'))
      ->set('added_to_cart_message', $form_state->getValue('basiccart_added_to_cart_message'))
      ->set('add_to_cart_redirect', $form_state->getValue('basiccart_add_to_cart_redirect'))
      ->set('content_type', $form_state->getValue('basiccart_content_types'))
      ->set('order_status', $form_state->getValue('basiccart_order_status'))
      ->save();
    Utility::create_fields();

    foreach ($form_state->getValue('basiccart_content_types') as $key => $value) {
      $content_types[$key] = $value ? $value : $content_types[$key];
    }

    $this->config('basiccart.settings')->set('content_type', $content_types)->save();
    parent::submitForm($form, $form_state);
  }

}

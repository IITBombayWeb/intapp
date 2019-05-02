<?php

namespace Drupal\basiccart\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'addtocartwithquantity' formatter.
 *
 * @FieldFormatter(
 *   id = "addtocartwithquantity",
 *   module = "basiccart",
 *   label = @Translation("Add to cart with quantity"),
 *   field_types = {
 *     "addtocart"
 *   }
 * )
 */
class AddtoCartWithQuantityFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $config = \Drupal::config('basiccart.settings');
    $entity = $items->getEntity();
    $config = \Drupal::config('basiccart.settings');
    $elements = [];

    $option = [
      'query' => ['entitytype' => $entity->getEntityTypeId(), 'quantity' => ''],
      'absolute' => TRUE,
    ];
    $url = Url::fromRoute('basiccart.cartadd', ["nid" => $entity->id()], $option);
    $link = '<a id="forquantitydynamictext_' . $entity->id() . '" class="basiccart-get-quantity button use-basiccart-ajax" href="' . $url->toString() . '">' . $this->t("@value", ['@value' => $config->get('add_to_cart_button')]) . '</a>';
    $link_options = [
      'attributes' => [
        'class' => [
          'basiccart-get-quantity',
          'use-basiccart-ajax',
          'button',
        ],
      ],
    ];
    $url->setOptions($link_options);

    $quantity_content = $config->get('quantity_status') ? '<div id="quantity-wrapper_' . $entity->id() . '" class="addtocart-quantity-wrapper-container"></div>' : '';
    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#type' => 'container',
        '#attributes' => ['class' => 'ajax-addtocart-wrapper' , 'id' => 'ajax-addtocart-message-' . $entity->id()],
        '#prefix' => '<div class="addtocart-wrapper-container">' . $quantity_content . '<div class="addtocart-link-class">' . $link . "</div>",
        '#suffix' => '</div>',
      ];
    }

    $elements['#attached']['library'][] = 'core/drupal.ajax';
    return $elements;

  }

}

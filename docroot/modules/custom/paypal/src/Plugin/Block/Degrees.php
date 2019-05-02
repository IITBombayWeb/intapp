<?php

namespace Drupal\paypal\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Provides a 'Degress' block.
 *
 * @Block(
 *   id = "degrees",
 *   admin_label = @Translation("List of Degress")
 * )
 */
class Degrees extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    return [
      '#type' => 'markup',
      '#title' => 'Degress',
      '#markup' => $this->List_of_Degress(),
      '#cache' => ['max-age' => 0],
    ];
  }

  /**
   * Implements List_of_Degress().
   */
  public function List_of_Degress() {
    $entity_type_id = 'node';
    $field_name = 'field_degree';
    $degree = FieldStorageConfig::loadByName($entity_type_id, $field_name);
    $degree_count = count($degree->get('settings')[allowed_values]);
    $pgm = '<div class="ins-cnt"><span>' . ($degree_count) . '</span></div><h3> Degrees </h3>';
    $pgm .= '<div class="vw-lik"> <a href="/search" title="Instuties"> see complete list </a></div>';
    return $pgm;
  }

}

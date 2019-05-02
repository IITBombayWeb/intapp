<?php

namespace Drupal\paypal\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\taxonomy\Entity\Term;

/**
 * Provides a 'Departments' block.
 *
 * @Block(
 *   id = "departments",
 *   admin_label = @Translation("List of Departments")
 * )
 */
class Departments extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#type' => 'markup',
      '#title' => 'Departments',
      '#markup' => $this->List_of_depart(),
      '#cache' => ['max-age' => 0],
    ];
  }

  /**
   * Implements List_of_depart().
   */
  public function List_of_depart() {
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', "departments");
    $tids = $query->execute();
    $terms = Term::loadMultiple($tids);
    $institute = [];
    foreach ($terms as $tid => $term) {
      $institute[$tid] = $term->get('name')->value;
    }
    $pgm = '<div class="ins-cnt"><span>' . (count($institute)) . '</span></div><h3> Departments </h3>';
    $pgm .= '<div class="vw-lik"> <a href="/search" title="Instuties"> see complete list </a></div>';
    return $pgm;
  }

}

<?php

namespace Drupal\paypal\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\taxonomy\Entity\Term;

/**
 * Provides a 'IITs List' block.
 *
 * @Block(
 *   id = "iits",
 *   admin_label = @Translation("List of IITs")
 * )
 */
class IIts extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    
     return array(
      '#type' => 'markup',
      '#title' => 'IITs List',
      '#markup' => $this->List_of_iits(),
      '#cache' => array('max-age' => 0),
    );
}
  function List_of_iits() {
      $query = \Drupal::entityQuery('taxonomy_term');
      $query->condition('vid', "iit_institute");
      $tids = $query->execute();
      $terms = Term::loadMultiple($tids);
      $institute = array();
      foreach ($terms as $tid => $term) {
        $institute[$tid] = $term->get('name')->value;
      }
       $pgm='<div class="ins-cnt"><span>'.(count($institute)).'</span></div><h3> Institutes </h3>';
       $pgm.= '<div class="vw-lik"> <a href="/search" title="Instuties"> see complete list </a></div>';
      return $pgm;
   }  
}

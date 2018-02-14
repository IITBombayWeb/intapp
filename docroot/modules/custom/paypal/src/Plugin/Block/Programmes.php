<?php

namespace Drupal\paypal\Plugin\Block;

use Drupal\Core\Block\BlockBase;
/**
 * Provides a 'Programmes' block.
 *
 * @Block(
 *   id = "programmes",
 *   admin_label = @Translation("Programmes Block")
 * )
 */
class Programmes extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    
     return array(
      '#type' => 'markup',
      '#title' => 'programmes',
      '#markup' => $this->List_of_Prgm(),
      '#cache' => array('max-age' => 0),
    );
}
  function List_of_Prgm() {
         $query = \Drupal::database()->select('node', 'n');
	 $query->fields('n', ['nid']);
	 $query->condition('type', 'programme');
         $num_rows = $query->countQuery()->execute()->fetchField();
         $pgm='<div class="ins-cnt"><span>'.$num_rows.'</span></div><h3> Programmes </h3>';
         $pgm.= '<div class="vw-lik"> <a href="/search" title="Instuties"> see complete list </a></div>';
      return $pgm;
   }

}

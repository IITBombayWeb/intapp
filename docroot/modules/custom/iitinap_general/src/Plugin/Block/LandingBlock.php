<?php

namespace Drupal\iitinap_general\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Homelanding' Block
 *
 * @Block(
 *   id = "landing_block",
 *   admin_label = @Translation("Find, Apply, Accept"),
 * )
 */
class LandingBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $landingConfig = \Drupal::config('iitinap_general.settings');
    $body_content = $landingConfig->get('instruction');
    return array(
      '#markup' => $body_content,
    );
  }
}
?>
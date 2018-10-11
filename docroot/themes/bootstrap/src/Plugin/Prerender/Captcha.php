<?php

namespace Drupal\bootstrap\Plugin\Prerender;

<<<<<<< HEAD
use Drupal\bootstrap\Annotation\BootstrapPrerender;
=======
>>>>>>> origin/development
use Drupal\bootstrap\Utility\Element;

/**
 * Pre-render callback for the "captcha" element type.
 *
 * @ingroup plugins_prerender
 *
 * @BootstrapPrerender("captcha",
 *   action = @BootstrapConstant(
 *     "\Drupal\bootstrap\Bootstrap::CALLBACK_PREPEND"
 *   )
 * )
 */
class Captcha extends PrerenderBase {

  /**
   * {@inheritdoc}
   */
  public static function preRenderElement(Element $element) {
    parent::preRenderElement($element);
    $element->setProperty('smart_description', FALSE, TRUE);
  }

}

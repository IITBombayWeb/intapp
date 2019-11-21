<?php

namespace Drupal\sitemap\Plugin\Sitemap;

use Drupal\Core\Form\FormStateInterface;
use Drupal\sitemap\SitemapBase;

/**
 * Provides a sitemap for a book.
 *
 * @Sitemap(
 *   id = "book",
 *   title = @Translation("Book name"),
 *   description = @Translation("Book type"),
 *   settings = {
 *     "title" = "",
 *   },
 *   deriver = "Drupal\sitemap\Plugin\Derivative\BookSitemapDeriver",
 *   enabled = FALSE,
 *   book = "",
 * )
 */
class Book extends SitemapBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    // @TODO: Provide the book name as the default title.
    $form['title']['#default_value'] = $this->settings['title'] ?: '';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function view() {
    /** @var \Drupal\book\BookManagerInterface $book_manager */
    $book_manager = \Drupal::service('book.manager');
    $book_id = $this->pluginDefinition['book'];

    $tree = $book_manager->bookTreeAllData($book_id);
    $content = $book_manager->bookTreeOutput($tree);

    return [
      '#theme' => 'sitemap_item',
      '#title' => $this->settings['title'],
      '#content' => $content,
      '#sitemap' => $this->getPluginDefinition(),
    ];
  }

}

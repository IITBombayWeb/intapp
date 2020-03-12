<?php

namespace Drupal\sitemap\Plugin\Sitemap;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\sitemap\SitemapBase;
use Drupal\Core\Template\Attribute;

/**
 * Provides a sitemap for an taxonomy vocabulary.
 *
 * @Sitemap(
 *   id = "vocabulary",
 *   title = @Translation("Vocabulary"),
 *   description = @Translation("Vocabulary description"),
 *   settings = {
 *     "title" = "",
 *     "show_description" = FALSE,
 *     "show_count" = FALSE,
 *     "term_depth" = -1,
 *     "term_count_threshold" = -1,
 *   },
 *   deriver = "Drupal\sitemap\Plugin\Derivative\VocabularySitemapDeriver",
 *   enabled = FALSE,
 *   vocabulary = "",
 * )
 */
class Vocabulary extends SitemapBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    // @TODO: Provide vocabulary name as the default title.
    $form['title']['#default_value'] = $this->settings['title'] ?: '';

    $form['show_description'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show vocabulary description'),
      '#default_value' => $this->settings['show_description'],
      '#description' => $this->t('When enabled, this option will show the vocabulary description.'),
    ];

    $form['show_count'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show node counts by taxonomy terms'),
      '#default_value' => $this->settings['show_count'],
      '#description' => $this->t('When enabled, this option will show the number of nodes in each taxonomy term.'),
    ];

    $form['term_depth'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Vocabulary depth'),
      '#default_value' => $this->settings['term_depth'],
      '#size' => 3,
      '#maxlength' => 10,
      '#description' => $this->t('Specify how many levels taxonomy terms should be included. Enter "-1" to include all terms, "0" not to include terms at all, or "1" to only include top-level terms.'),
    ];

    $form['term_count_threshold'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Term count threshold'),
      '#default_value' => $this->settings['term_count_threshold'],
      '#size' => 3,
      '#description' => $this->t('Only show taxonomy terms whose node counts are greater than this threshold. Set to -1 to disable.'),
    ];

    // @TODO
    $form['rss_threshold'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('RSS threshold'),
      '#default_value' => $this->settings['rss_threshold'],
      '#size' => 3,
      '#maxlength' => 10,
      '#description' => $this->t('Specify how many RSS feed links should be displayed with taxonomy terms. Enter "-1" to include with all terms, "0" not to include with any terms, or "1" to show only for top-level taxonomy terms.'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function view() {
    $title = $this->settings['title'];

    $vid = $this->pluginDefinition['vocabulary'];
    /** @var \Drupal\taxonomy\Entity\Vocabulary $vocabulary */
    $vocabulary = \Drupal::entityTypeManager()->getStorage('taxonomy_vocabulary')->load($vid);
    $content = [];

    if ($this->settings['show_description']) {
      $content[] = ['#markup' => $vocabulary->getDescription()];
    }
    $last_depth = -1;
    // @TODO: How to deal with children of terms that don't make this threshold?
    // Currently merges with earlier list.
    $threshold = $this->settings['term_count_threshold'];
    $depth = $this->settings['term_depth'];
    if (!$depth || $depth <= -1) {
      $depth = NULL;
    }
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid, 0, $depth);

    // @TODO: Handling for Forum vs Vocab
    if (\Drupal::service('module_handler')->moduleExists('forum') && $vid == \Drupal::config('forum.settings')->get('vocabulary')) {
      $title = Link::fromTextAndUrl($vocabulary->label(), Url::fromRoute('forum.index'))->toString();
      $forum_link = TRUE;
    }
    else {
      $title = $vocabulary->label();
      $forum_link = FALSE;
    }

    // @TODO: item_list?
    $output = '';
    foreach ($terms as $term) {
      $term->count = sitemap_taxonomy_term_count_nodes($term->tid);
      if ($term->count <= $threshold) {
        continue;
      }

      // Adjust the depth of the <ul> based on the change
      // in $term->depth since the $last_depth.
      if ($term->depth > $last_depth) {
        for ($i = 0; $i < ($term->depth - $last_depth); $i++) {
          $output .= "\n<ul>";
        }
      }
      elseif ($term->depth == $last_depth) {
        $output .= '</li>';
      }
      elseif ($term->depth < $last_depth) {
        for ($i = 0; $i < ($last_depth - $term->depth); $i++) {
          $output .= "</li>\n</ul>\n</li>";
        }
      }
      // Display the $term.
      $output .= "\n<li>";
      $term_item = '';
      if ($forum_link) {
        $link_options = [
          ['attributes' => ['title' => $term->description__value]],
        ];
        $term_item .= Link::fromTextAndUrl($term->name, Url::fromRoute('forum.page', ['taxonomy_term' => $term->tid], $link_options))->toString();
      }
      elseif ($term->count) {
        $link_options = [
          ['attributes' => ['title' => $term->description__value]],
        ];
        $term_item .= Link::fromTextAndUrl($term->name, Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $term->tid], $link_options))->toString();
      }
      else {
        $term_item .= $term->name;
      }
      if ($this->settings['show_count']) {
        $span_title = \Drupal::translation()->formatPlural($term->count, '1 item has this term', '@count items have this term');
        $term_item .= " <span title=\"" . $span_title . "\">(" . $term->count . ")</span>";
      }

      // @TODO: Document
      // Add an alter hook for modules to manipulate the taxonomy term output.
      \Drupal::moduleHandler()->alter(['sitemap_taxonomy_term', 'sitemap_taxonomy_term_' . $term->tid], $term_item, $term);

      $output .= $term_item;

      // Reset $last_depth in preparation for the next $term.
      $last_depth = $term->depth;
    }

    // Bring the depth back to where it began, -1.
    if ($last_depth > -1) {
      for ($i = 0; $i < ($last_depth + 1); $i++) {
        $output .= "</li>\n</ul>\n";
      }
    }

    $content[] = [
      '#markup' => $output,
    ];

    return [
      '#theme' => 'sitemap_item',
      '#title' => $title,
      '#content' => $content,
      '#sitemap' => $this->getPluginDefinition(),
    ];
  }

}

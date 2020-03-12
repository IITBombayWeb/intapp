<?php

namespace Drupal\sitemap\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Config\ConfigFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\sitemap\SitemapManager;

/**
 * Provides a configuration form for sitemap.
 */
class SitemapSettingsForm extends ConfigFormBase {

  /**
   * The SitemapMap plugin manager.
   *
   * @var \Drupal\sitemap\SitemapManager
   */
  protected $sitemapManager;

  /**
   * An array of Sitemap plugins.
   *
   * @var \Drupal\sitemap\SitemapInterface[]
   */
  protected $plugins = [];

  /**
   * Constructs a SitemapSettingsForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\sitemap\SitemapManager $sitemap_manager
   *   The Sitemap plugin manager.
   */
  public function __construct(ConfigFactory $config_factory, SitemapManager $sitemap_manager) {
    parent::__construct($config_factory);
    $this->sitemapManager = $sitemap_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $form = new static(
      $container->get('config.factory'),
      $container->get('plugin.manager.sitemap')
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sitemap_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->configFactory->get('sitemap.settings');

    $form['page_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Page title'),
      '#default_value' => $config->get('page_title'),
      '#description' => $this->t('Page title that will be used on the @sitemap_page.', ['@sitemap_page' => $this->l($this->t('sitemap page'), Url::fromRoute('sitemap.page'))]),
    ];

    $sitemap_message = $config->get('message');
    $form['message'] = [
      '#type' => 'text_format',
      '#format' => isset($sitemap_message['format']) ? $sitemap_message['format'] : NULL,
      '#title' => $this->t('Sitemap message'),
      '#default_value' => $sitemap_message['value'],
      '#description' => $this->t('Define a message to be displayed above the sitemap.'),
    ];

    // Retrieve stored configuration for the plugins.
    $plugins = $config->get('plugins');
    $plugin_config = [];

    // Create plugin instances for all available Sitemap plugins, including both
    // enabled/configured ones as well as new and not yet configured ones.
    $definitions = $this->sitemapManager->getDefinitions();
    foreach ($definitions as $id => $definition) {
      if ($this->sitemapManager->hasDefinition($id)) {
        if (!empty($plugins[$id])) {
          $plugin_config = $plugins[$id];
        }
        $this->plugins[$id] = $this->sitemapManager->createInstance($id, $plugin_config);
      }
    }

    // Plugin status.
    $form['plugins']['enabled'] = [
      '#type' => 'item',
      '#title' => $this->t('Available plugins'),
      '#prefix' => '<div id="plugins-enabled-wrapper">',
      '#suffix' => '</div>',
      // This item is used as a pure wrapping container with heading. Ignore its
      // value, since 'plugins' should only contain plugin definitions.
      // See https://www.drupal.org/node/1829202.
      '#input' => FALSE,
    ];
    // SitemapMap order (tabledrag).
    $form['plugins']['order'] = [
      '#type' => 'table',
      // For sitemap.admin.js.
      '#attributes' => ['id' => 'sitemap-order'],
      '#title' => $this->t('Plugin order'),
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'sitemap-plugin-order-weight',
        ],
      ],
      '#tree' => FALSE,
      '#input' => FALSE,
      '#theme_wrappers' => ['form_element'],
    ];
    // Map settings.
    $form['plugin_settings'] = [
      '#type' => 'vertical_tabs',
      '#title' => $this->t('Plugin settings'),
    ];

    // Provide a default weight value.
    $i = -50;

    foreach ($this->plugins as $id => $plugin) {
      /* @var $plugin \Drupal\sitemap\SitemapBase */

      if (!empty($plugin->weight)) {
        $weight = $plugin->weight;
        $i = $weight;
      }
      else {
        $weight = $i;
      }

      $form['plugins']['enabled'][$id] = [
        '#type' => 'checkbox',
        '#title' => $plugin->getLabel(),
        '#default_value' => $plugin->enabled,
        '#parents' => ['plugins', $id, 'enabled'],
        '#description' => $plugin->getDescription(),
        '#weight' => $weight,
      ];

      $form['plugins']['order'][$id]['#attributes']['class'][] = 'draggable';
      $form['plugins']['order'][$id]['#weight'] = $weight;
      $form['plugins']['order'][$id]['filter'] = [
        '#markup' => $plugin->getLabel(),
      ];
      $form['plugins']['order'][$id]['weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Weight for @title', ['@title' => $plugin->getLabel()]),
        '#title_display' => 'invisible',
        '#delta' => 50,
        '#default_value' => $weight,
        '#parents' => ['plugins', $id, 'weight'],
        '#attributes' => ['class' => ['plugin-order-weight']],
      ];

      // Retrieve the settings form of the SitemapMap plugins.
      $settings_form = [
        '#parents' => ['plugins', $id, 'settings'],
        '#tree' => TRUE,
      ];
      $settings_form = $plugin->settingsForm($settings_form, $form_state);
      if (!empty($settings_form)) {
        $form['plugins']['settings'][$id] = [
          '#type' => 'details',
          '#title' => $plugin->getLabel(),
          '#open' => TRUE,
          '#weight' => $weight,
          '#parents' => ['plugins', $id, 'settings'],
          '#group' => 'plugin_settings',
        ];
        $form['plugins']['settings'][$id] += $settings_form;

        if (isset($plugins[$id]['settings'])) {
          foreach ($plugins[$id]['settings'] as $key => $value) {
            $form['plugins']['settings'][$id][$key]['#default_value'] = $value;
          }
        }
      }

      // Increment the default weight value.
      $i++;
    }
    $form['#attached']['library'][] = 'sitemap/sitemap.admin';

    // Sitemap RSS settings.
    $form['rss'] = [
      '#type' => 'details',
      '#title' => $this->t('RSS settings'),
    ];
    $form['rss']['rss_display'] = [
      '#type' => 'select',
      '#title' => $this->t('Display RSS links:'),
      '#default_value' => $config->get('rss_display'),
      '#options' => [
        'left' => $this->t('Left of the text'),
        'right' => $this->t('Right of the text'),
      ],
      '#description' => $this->t('When enabled, this option will show links to the RSS feeds for the front page and taxonomy terms, if enabled.'),
    ];

    // Sitemap CSS settings.
    $form['css'] = [
      '#type' => 'details',
      '#title' => $this->t('CSS settings'),
    ];
    $form['css']['include_css'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Include sitemap CSS file'),
      '#default_value' => $config->get('include_css'),
      '#description' => $this->t("Select this box if you wish to load the CSS file included with the module. To learn how to override or specify the CSS at the theme level, visit the @documentation_page.", ['@documentation_page' => $this->l($this->t("documentation page"), Url::fromUri('https://www.drupal.org/node/2615568'))]),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->configFactory->getEditable('sitemap.settings');

    // Save config.
    foreach ($form_state->getValues() as $key => $value) {
      if ($key == 'plugins') {
        foreach ($value as $instance_id => $plugin_config) {
          // Update the plugin configurations.
          $this->plugins[$instance_id]->setConfiguration($plugin_config);
        }
        // Save in sitemap.settings.
        $config->set($key, $value);
      }
      else {
        $config->set($key, $value);
      }
    }
    $config->save();

    drupal_flush_all_caches();

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['sitemap.settings'];
  }

}

<?php

namespace Drupal\sitemap\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\sitemap\SitemapManager;

/**
 * Controller routines for update routes.
 */
class SitemapController implements ContainerInjectionInterface {

  /**
   * Module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The SitemapMap plugin manager.
   *
   * @var \Drupal\sitemap\SitemapManager
   */
  protected $sitemapManager;

  /**
   * Constructs update status data.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   * @param \Drupal\sitemap\SitemapManager
   *   The SitemapMap plugin manager.
   */
  public function __construct(ModuleHandlerInterface $module_handler, SitemapManager $sitemap_manager) {
    $this->moduleHandler = $module_handler;
    $this->sitemapManager = $sitemap_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('module_handler'),
      $container->get('plugin.manager.sitemap')
    );
  }

  /**
   * Controller for /sitemap.
   *
   * @return array
   *   Renderable array.
   */
  public function buildSitemap() {
    $config = \Drupal::config('sitemap.settings');

    // Build the Sitemap message
    $message = '';
    if (!empty($config->get('message')) && !empty($config->get('message')['value'])) {
      $message = check_markup($config->get('message')['value'], $config->get('message')['format']);
    }

    // Build the plugin content
    $plugins_config = $config->get('plugins');
    $plugins = [];
    $plugin_config = [];
    $definitions = $this->sitemapManager->getDefinitions();
    foreach ($definitions as $id => $definition) {
      if ($this->sitemapManager->hasDefinition($id)) {
        if (!empty($plugins_config[$id])) {
          $plugin_config = $plugins_config[$id];
        }
        $instance = $this->sitemapManager->createInstance($id, $plugin_config);
        if ($instance->enabled) {
          $plugins[$instance->weight] = $instance->view();
        }
      }
    }

    // Build the render array
    $sitemap = [
      '#theme' => 'sitemap',
      '#message' => $message,
      '#sitemap_items' => $plugins,
    ];

    // Check whether to include the default CSS.
    if ($config->get('include_css') == 1) {
      $sitemap['#attached']['library'] = [
        'sitemap/sitemap.theme',
      ];
    }

    return $sitemap;
  }

  /**
   * Returns sitemap page's title.
   *
   * @return string
   *   Sitemap page title.
   */
  public function getTitle() {
    $config = \Drupal::config('sitemap.settings');
    return $config->get('page_title');
  }

}

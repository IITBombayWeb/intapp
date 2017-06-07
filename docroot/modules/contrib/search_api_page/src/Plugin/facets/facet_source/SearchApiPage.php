<?php

namespace Drupal\search_api_page\Plugin\search_api\display;

use Drupal\search_api\Display\DisplayPluginBase;

/**
 * Represents a Views block display.
 *
 * @SearchApiDisplay(
 *   id = "search_api_page",
 *   deriver = "Drupal\search_api_page\Plugin\search_api\display\SearchApiPageDeriver"
 * )
 */
class SearchApiPage extends DisplayPluginBase {

}
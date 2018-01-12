<?php

namespace Drupal\Tests\search_api_page\Functional;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Tests\BrowserTestBase;
use Drupal\search_api\Entity\Index;
use Drupal\search_api\Entity\Server;
use Drupal\Tests\search_api\Functional\ExampleContentTrait;

/**
 * Class FunctionalTestBase.
 */
abstract class FunctionalTestBase extends BrowserTestBase {

  protected $strictConfigSchema = FALSE;

  use StringTranslationTrait;
  use ExampleContentTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'search_api_page',
    'node',
    'search_api',
    'search_api_db',
    'block',
  ];

  /**
   * An admin user used for this test.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $adminUser;

  /**
   * A user without any permission..
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $unauthorizedUser;

  /**
   * The anonymous user used for this test.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $anonymousUser;

  /**
   * A search database server.
   *
   * @var \Drupal\search_api\Entity\Server
   */
  protected $server = NULL;

  /**
   * A search index.
   *
   * @var \Drupal\search_api\Entity\Index
   */
  protected $index = NULL;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    // Create the users used for the tests.
    $this->adminUser = $this->drupalCreateUser([
      'administer search_api',
      'administer search_api_page',
      'access administration pages',
      'administer nodes',
      'access content overview',
      'administer content types',
      'administer blocks',
      'view search api pages',
    ]);
    $this->unauthorizedUser = $this->drupalCreateUser();
    $this->anonymousUser = $this->drupalCreateUser(['view search api pages']);

    // Create article content type and content.
    $this->drupalCreateContentType(['type' => 'article']);
    for ($i = 1; $i < 50; $i++) {
      $this->drupalCreateNode([
        'title' => 'Item number' . $i,
        'type' => 'article',
        'body' => [['value' => 'Body number' . $i]],
      ]);
    }
  }

  /**
   * Set up Search API database and server.
   */
  protected function setupSearchAPI() {
    $this->server = Server::create([
      'name' => 'Server',
      'id' => 'server_1',
      'backend' => 'search_api_db',
      'backend_config' => [
        'database' => 'default:default',
      ],
    ]);
    $this->server->save();

    $this->index = Index::create([
      'id' => 'Index',
      'name' => 'index_1',
      'description' => 'Description for the index.',
      'server' => $this->server->id(),
      'datasource_settings' => [
        'entity:node' => [
          'plugin_id' => 'entity:node',
          'settings' => [],
        ],
      ],
      'field_settings' => [
        'rendered_item' => [
          'label' => 'Rendered HTML output',
          'property_path' => 'rendered_item',
          'type' => 'text',
          'configuration' => [
            'roles' => [
              'anonymous' => 'anonymous',
            ],
            'view_mode' => [
              'entity:node' => [
                'article' => 'default',
                'page' => '',
              ],
            ],
          ],
        ],
      ],
    ]);
    $this->index->save();

    $task_manager = \Drupal::getContainer()->get('search_api.index_task_manager');
    $task_manager->addItemsAll(Index::load($this->index->id()));
    $this->indexItems($this->index->id());
  }

}
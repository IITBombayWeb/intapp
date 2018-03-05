<?php

namespace Drupal\sitemap\Tests;

use Drupal\filter\Entity\FilterFormat;

/**
 * Test configurable content on the Sitemap page.
 *
 * @group sitemap
 */
class SitemapContentTest extends SitemapTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['sitemap', 'block', 'filter'];

  /**
   * Content editor user
   */
  public $user_editor;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Place page title block.
    $this->drupalPlaceBlock('page_title_block');

    // Create filter format.
    $restricted_html_format = FilterFormat::create([
      'format' => 'restricted_html',
      'name' => 'Restricted HTML',
      'filters' => [
        'filter_html' => [
          'status' => TRUE,
          'weight' => -10,
          'settings' => [
            'allowed_html' => '<p> <br /> <strong> <a> <em> <h4>',
          ],
        ],
        'filter_autop' => [
          'status' => TRUE,
          'weight' => 0,
        ],
        'filter_url' => [
          'status' => TRUE,
          'weight' => 0,
        ],
        'filter_htmlcorrector' => [
          'status' => TRUE,
          'weight' => 10,
        ],
      ],
    ]);
    $restricted_html_format->save();

    // Create user then login.
    $this->user_editor = $this->drupalCreateUser([
      'administer sitemap',
      'access sitemap',
      $restricted_html_format->getPermissionName(),
    ]);
    $this->drupalLogin($this->user_editor);
  }

  /**
   * Tests configurable page title.
   */
  public function testPageTitle() {
    // Assert default page title.
    $this->drupalGet('/sitemap');
    $this->assertTitle('Sitemap | Drupal', 'The title on the sitemap page is "Sitemap | Drupal".');

    // Change page title.
    $new_title = $this->randomMachineName();
    $edit = array(
      'page_title' => $new_title,
    );
    $this->drupalPostForm('admin/config/search/sitemap', $edit, t('Save configuration'));
    drupal_flush_all_caches();

    // Assert that page title is changed.
    $this->drupalGet('/sitemap');
    $this->assertTitle("$new_title | Drupal", 'The title on the sitemap page is "' . "$new_title | Drupal" . '".');
  }

  /**
   * Tests sitemap message.
   */
  public function testSitemapMessage() {
    // Assert that sitemap message is not included in the sitemap by default.
    $this->drupalGet('/sitemap');
    $elements = $this->cssSelect('.sitemap-message');
    $this->assertEqual(count($elements), 0, 'Sitemap message is not included.');

    // Change sitemap message.
    $new_message = $this->randomMachineName(16);
    $edit = array(
      'message[value]' => $new_message,
    );
    $this->drupalPostForm('admin/config/search/sitemap', $edit, t('Save configuration'));
    drupal_flush_all_caches();

    // Assert sitemap message is included in the sitemap.
    $this->drupalGet('/sitemap');
    $elements = $this->cssSelect(".sitemap-message:contains('" . $new_message . "')");
    $this->assertEqual(count($elements), 1, 'Sitemap message is included.');
  }

}

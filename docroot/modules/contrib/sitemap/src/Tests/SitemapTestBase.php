<?php

namespace Drupal\sitemap\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the landing and admin pages of the sitemap.
 *
 * @group sitemap
 */
class SitemapTestBase extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('sitemap');

  /**
   * User accounts
   */
  public $user_admin;
  public $user_view;
  public $user_noaccess;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Create user with admin permissions.
    $this->user_admin = $this->drupalCreateUser(array(
      'administer sitemap',
      'access sitemap',
    ));

    // Create user with view permissions.
    $this->user_view = $this->drupalCreateUser(array(
      'access sitemap',
    ));

    // Create user without any sitemap permissions.
    $this->user_noaccess = $this->drupalCreateUser();
  }

}

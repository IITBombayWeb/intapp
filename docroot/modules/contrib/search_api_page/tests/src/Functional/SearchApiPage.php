<?php

namespace Drupal\Tests\search_api_page\Functional;

/**
 * Provides web tests for Search API Pages.
 *
 * @group search_api_page
 */
class SearchApiPage extends FunctionalTestBase {

  /**
   * Test search api pages.
   */
  public function testSearchApiPage() {
    $this->drupalLogin($this->adminUser);

    // Setup search api server and index.
    $this->setupSearchAPI();

    $this->drupalGet('admin/config/search/search-api-pages');
    $this->assertResponse(200);

    $step1 = array(
      'label' => 'Search',
      'id' => 'search',
      'index' => $this->index->id(),
    );
    $this->drupalPostForm('admin/config/search/search-api-pages/add', $step1, 'Next');

    $step2 = array(
      'path' => 'search',
    );
    $this->drupalPostForm(NULL, $step2, 'Save');

    $this->drupalGet('search');
    $this->assertRaw('Enter the terms you wish to search for.');
    $this->assertNoRaw('Your search yielded no results.');
    $this->assertResponse(200);

    $this->drupalLogout();
    $this->drupalLogin($this->unauthorizedUser);
    $this->drupalGet('search');
    $this->assertResponse(403);

    $this->drupalLogout();
    $this->drupalLogin($this->anonymousUser);
    $this->drupalGet('search');
    $this->assertResponse(200);

    $this->drupalLogout();
    $this->drupalLogin($this->adminUser);

    $this->drupalGet('search/nothing-found');
    $this->assertRaw('Enter the terms you wish to search for.');
    $this->assertRaw('Your search yielded no results.');
    $this->drupalGet('search');
    $this->assertNoRaw('Your search yielded no results.');

    $this->drupalPostForm('admin/config/search/search-api-pages/search', array('show_all_when_no_keys' => TRUE, 'show_search_form' => FALSE), 'Save');
    $this->drupalGet('search');
    $this->assertNoRaw('Your search yielded no results.');
    $this->assertNoRaw('Enter the terms you wish to search for.');
    $this->assertText('49 results found');

    $this->drupalGet('search/number10');
    $this->assertText('1 result found');

    $this->drupalPostForm('admin/config/search/search-api-pages/search', array('show_search_form' => TRUE), 'Save');

    $this->drupalGet('search/number11');
    $this->assertText('1 result found');
    $this->assertRaw('name="keys" value="number11"');

    // Cache should be cleared after the save.
    //$this->drupalGet('search/number10');
    //$this->assertText('1 result found');
    //$this->assertRaw('name="keys" value="number10"');
  }

}

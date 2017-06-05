<?php

namespace Drupal\system\Tests\Cache;

use Drupal\Core\Cache\PhpBackend;

/**
 * Unit test of the PHP cache backend using the generic cache unit test base.
 *
 * @group Cache
 */
class PhpBackendUnitTest extends GenericCacheBackendUnitTestBase {

  /**
   * Creates a new instance of MemoryBackend.
   *
   * @return
   *   A new MemoryBackend object.
   */
  protected function createCacheBackend($bin) {
    $backend = new PhpBackend($bin, \Drupal::service('cache_tags.invalidator.checksum'));
    return $backend;
  }

}

<?php

namespace Drupal\Tests\migrate_plus\Kernel\Plugin\migrate_plus\data_fetcher;

<<<<<<< HEAD
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\KernelTests\KernelTestBase;
use Drupal\migrate_plus\Plugin\migrate_plus\data_fetcher\Http;
use Drupal\Tests\Core\Test\KernelTestBaseTest;
use Drupal\Tests\UnitTestCase;

/**
 * Class HttpTest
=======
use Drupal\KernelTests\KernelTestBase;
use Drupal\migrate_plus\Plugin\migrate_plus\data_fetcher\Http;

/**
 * Class HttpTest.
>>>>>>> origin/development
 *
 * @group migrate_plus
 * @package Drupal\Tests\migrate_plus\Unit\migrate_plus\data_fetcher
 */
class HttpTest extends KernelTestBase {

  /**
   * Test http headers option.
<<<<<<< HEAD
   */
  function testHttpHeaders() {
    $expected = [
      'Accept' => 'application/json',
      'User-Agent' => 'Internet Explorer 6',
      'Authorization-Key' => 'secret',
      'Arbitrary-Header' => 'foobarbaz'
    ];

    $configuration = [
      'headers' => [
        'Accept' => 'application/json',
        'User-Agent' => 'Internet Explorer 6',
        'Authorization-Key' => 'secret',
        'Arbitrary-Header' => 'foobarbaz'
      ]
    ];

    $http = new Http($configuration, 'http', []);

    $this->assertEquals($expected, $http->getRequestHeaders());
  }
=======
   *
   * @dataProvider headerDataProvider
   */
  public function testHttpHeaders(array $definition, array $expected, array $preSeed = []) {
    $http = new Http($definition, 'http', []);
    $this->assertEquals($expected, $http->getRequestHeaders());
  }

  /**
   * Provides multiple test cases for the testHttpHeaders method.
   *
   * @return array
   *   The test cases
   */
  public function headerDataProvider() {
    return [
      'dummy headers specified' => [
        'definition' => [
          'headers' => [
            'Accept' => 'application/json',
            'User-Agent' => 'Internet Explorer 6',
            'Authorization-Key' => 'secret',
            'Arbitrary-Header' => 'foobarbaz',
          ],
        ],
        'expected' => [
          'Accept' => 'application/json',
          'User-Agent' => 'Internet Explorer 6',
          'Authorization-Key' => 'secret',
          'Arbitrary-Header' => 'foobarbaz',
        ],
      ],
      'no headers specified' => [
        'definition' => [
          'no_headers_here' => 'foo',
        ],
        'expected' => [],
      ],
    ];
  }

>>>>>>> origin/development
}

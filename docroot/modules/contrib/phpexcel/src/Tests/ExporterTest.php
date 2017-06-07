<?php

/**
 * @file
 * Contains \Drupal\phpexcel\Tests\ExporterTest.
 */

namespace Drupal\phpexcel\Tests;

use Drupal\phpexcel\Exporter;
use Drupal\Tests\UnitTestCase;

/**
 * @group phpexcel
 */
class ExporterTest extends UnitTestCase {

  protected $single_worksheet_file;
  protected $issue_1988868_file;
  protected $multiple_worksheet_file;
  protected $no_headers_file;
  protected $db_result_file;
  protected $template_file;
  protected $directory;

  public function setUp() {
    parent::setUp('libraries', 'phpexcel');

    $this->directory = variable_get('file_' . file_default_scheme() . '_path', conf_path() . '/files');

    // Make sure the directory is writable
    $this->assertTrue(
      is_writable($this->directory),
      t("The !dir directory exists and is writable.", array('!dir' => $this->directory))
    );

    require_once dirname(__FILE__) . '/../phpexcel.inc';
  }

  public function tearDown() {
    @unlink($this->single_worksheet_file);
    @unlink($this->issue_1988868_file);
    @unlink($this->multiple_worksheet_file);
    @unlink($this->no_headers_file);
    @unlink($this->db_result_file);
    @unlink($this->template_file);

    parent::tearDown();
  }

  /**
   * Test a simple, single worksheet Excel export.
   */
  public function testSingleWorksheetExport() {
    /**
     * Export
     */
    // Prepare the data
    $headers = array('Header 1', 'Header 2');

    $data = array(
      array('Data 1.1', 'Data 1.2'),
      array('Data 2.1', 'Data 2.2'),
      array('Data 3.1', 'Data 3.2')
    );

    // Create a file path
    $correct_path = file_create_filename('phpexcel_test1.xls', $this->directory);

    // The filename will be munged by the export function, so:
    $this->single_worksheet_file = phpexcel_munge_filename($correct_path);

    // Create a wrong path. Should not be able to export.
    $wrong_path = 'path/to/nowhere/file.xls';

    // Should fail
    $this->assertEqual(
      PHPEXCEL_ERROR_PATH_NOT_WRITABLE,
      phpexcel_export($headers, $data, $wrong_path),
      'Passed an incorrect path'
    );

    // Should pass
    $this->assertEqual(
      PHPEXCEL_SUCCESS,
      phpexcel_export($headers, $data, $correct_path),
      t('Exported data to !path', array('!path' => $this->single_worksheet_file))
    );

    // Should pass
    $this->assertTrue(filesize($this->single_worksheet_file) > 0, 'Filesize should be bigger than 0');

    /**
     * Import and check.
     * Import, not keyed by headers
     */
    $data = phpexcel_import($this->single_worksheet_file, FALSE);

    // Should pass
    $this->assertTrue(!!$data, 'Import succeeded');

    // Should have 4 rows (3 rows + headers)
    $count = !empty($data[0]) ? count($data[0]) : 0;
    $this->assertTrue($count === 4, t('!count rows, expect 4.', array('!count' => $count)));

    // Should only have 2 cells
    $count = !empty($data[0][0]) ? count($data[0][0]) : 0;
    $this->assertTrue($count === 2, t('!count cells, expect 2.', array('!count' => $count)));

    // First row, 1st cell should be 'Header 1'
    $header = !empty($data[0][0][0]) ? $data[0][0][0] : 'no';
    $this->assertTrue($header === 'Header 1', 'First row, 1st cell data should be "Header 1"');

    // Second row, 1st cell should be 'Data 1.1'
    $header = !empty($data[0][1][0]) ? $data[0][1][0] : 'no';
    $this->assertTrue($header === 'Data 1.1', 'Second row, 1st cell data should be "Data 1.1"');
  }

  /**
   * A cell with a value of '0' must get exported as such.
   *
   * @see http://drupal.org/node/1988868
   */
  public function testIssue1988868() {
    /**
     * Export
     */
    // Prepare the data
    $headers = array('Header 1', 'Header 2');

    $data = array(
      array('0', 'Data 1.2'),
      array('Data 2.1', '0'),
      array('0', '0')
    );

    // Create a file path
    $correct_path = file_create_filename('phpexcel_test1.xls', $this->directory);

    // The filename will be munged by the export function, so:
    $this->issue_1988868_file = phpexcel_munge_filename($correct_path);

    // Should pass
    $this->assertEqual(
      PHPEXCEL_SUCCESS,
      phpexcel_export($headers, $data, $correct_path),
      t('Exported data to !path', array('!path' => $this->issue_1988868_file))
    );

    // Should pass
    $this->assertTrue(filesize($this->issue_1988868_file) > 0, 'Filesize should be bigger than 0');

    /**
     * Import and check.
     * Import, not keyed by headers
     */
    $data = phpexcel_import($this->issue_1988868_file, FALSE);

    // Should pass
    $this->assertTrue(!!$data, 'Import succeeded');

    // Second row, 1st cell should be 'Data 1.1'
    $cell = $data[0][1][0];
    $this->assertTrue($cell === '0', 'Second row, 1st cell data should be "0"');

    // Second row, 2nd cell should be 'Data 1.1'
    $cell = $data[0][1][1];
    $this->assertTrue($cell === 'Data 1.2', 'Second row, 2nd cell data should be "Data 1.2"');

    // Third row, 2nd cell should be '0'
    $cell = $data[0][2][1];
    $this->assertTrue($cell === '0', 'Third row, 2nd cell data should be "0"');

    // Fourth row, 1st cell should be '0'
    $cell = $data[0][3][0];
    $this->assertTrue($cell === '0', 'Fourth row, 1st cell data should be "0"');

    // Fourth row, 2nd cell should be '0'
    $cell = $data[0][3][1];
    $this->assertTrue($cell === '0', 'Fourth row, 2nd cell data should be "0"');
  }

  /**
   * Test multiple worksheet Excel export.
   */
  public function testMultipleWorksheetExport() {
    /**
     * Export
     */
    // Prepare data
    $headers = array(
      'Sheet 1' => array('Header 1.1', 'Header 1.2'),
      'Sheet 2' => array('Header 2.1', 'Header 2.2')
    );

    $data = array(
      'Sheet 1' => array(
        array('Data 1.1.1', 'Data 1.1.2'),
        array('Data 1.2.1', 'Data 1.2.2'),
        array('Data 1.3.1', 'Data 1.3.2')
      ),
      'Sheet 2' => array(
        array('Data 2.1.1', 'Data 2.1.2'),
        array('Data 2.2.1', 'Data 2.2.2'),
        array('Data 2.3.1', 'Data 2.3.2')
      ),
    );

    // Create a file path
    $correct_path = file_create_filename('phpexcel_test2.xls', $this->directory);

    // The filename will be munged by the export function, so:
    $this->multiple_worksheet_file = phpexcel_munge_filename($correct_path);

    // Should pass
    $this->assertEqual(
      PHPEXCEL_SUCCESS,
      phpexcel_export($headers, $data, $correct_path),
      t('Exported data to !path', array('!path' => $this->multiple_worksheet_file))
    );

    // Should pass
    $this->assertTrue(filesize($this->multiple_worksheet_file) > 0, 'Filesize should be bigger than 0');

    /**
     * Import and check.
     * Import, keyed by headers
     */
    $data = phpexcel_import($this->multiple_worksheet_file);

    // Should pass
    $this->assertTrue(!!$data, 'Import succeeded');

    // Should have 3 rows
    $count = !empty($data[0]) ? count($data[0]) : 0;
    $this->assertTrue($count === 3, t('!count rows, expect 3', array('!count' => $count)));

    // Should only have 2 cells
    $count = !empty($data[0][0]) ? count($data[0][0]) : 0;
    $this->assertTrue($count === 2, t('!count cells, expect 2', array('!count' => $count)));

    // Should be keyed by headers
    $this->assertTrue(isset($data[0][0]['Header 1.1']), 'Keyed by header ("Header 1.1")');
    $this->assertTrue(isset($data[1][0]['Header 2.2']), 'Keyed by header ("Header 2.2")');

    $header = !empty($data[0][0]['Header 1.1']) ? $data[0][0]['Header 1.1'] : 'no';
    $this->assertTrue($header === 'Data 1.1.1', 'Should be "Data 1.1.1"');

    $header = !empty($data[1][1]['Header 2.2']) ? $data[1][1]['Header 2.2'] : 'no';
    $this->assertTrue($header === 'Data 2.2.2', 'Should be "Data 2.2.2"');

    /**
     * Import and check.
     * Import with worksheet names.
     */
    $data = phpexcel_import($this->multiple_worksheet_file, TRUE, TRUE);

    // Should pass
    $this->assertTrue(!!$data, 'Import succeeded');

    // Should have 3 rows
    $count = !empty($data['Sheet 1']) ? count($data['Sheet 1']) : 0;
    $this->assertTrue($count === 3, t('!count rows, expect 3', array('!count' => $count)));

    // Should be keyed by Worksheet name.
    $this->assertTrue(isset($data['Sheet 1']), t('Imported with Worksheet names.'));
    $this->assertTrue(isset($data['Sheet 2']), t('Imported with Worksheet names.'));

    // Should only have 2 cells
    $count = !empty($data['Sheet 1'][0]) ? count($data['Sheet 1'][0]) : 0;
    $this->assertTrue($count === 2, t('!count cells, expect 2', array('!count' => $count)));

    // Should be keyed by headers
    $this->assertTrue(isset($data['Sheet 1'][0]['Header 1.1']), 'Keyed by header ("Header 1.1")');
    $this->assertTrue(isset($data['Sheet 2'][0]['Header 2.2']), 'Keyed by header ("Header 2.2")');

    $header = !empty($data['Sheet 1'][0]['Header 1.1']) ? $data['Sheet 1'][0]['Header 1.1'] : 'no';
    $this->assertTrue($header === 'Data 1.1.1', 'Should be "Data 1.1.1"');

    $header = !empty($data['Sheet 2'][1]['Header 2.2']) ? $data['Sheet 2'][1]['Header 2.2'] : 'no';
    $this->assertTrue($header === 'Data 2.2.2', 'Should be "Data 2.2.2"');
  }

  /**
   * Test "ignore_headers" option.
   */
  public function testIgnoreHeaders() {
    /**
     * Export
     */
    // Prepare data
    $data = array(
      0 => array(
        array('Data 1.1.1', 'Data 1.1.2'),
        array('Data 1.2.1', 'Data 1.2.2'),
        array('Data 1.3.1', 'Data 1.3.2')
      ),
      'Sheet 2' => array(
        array('Data 2.1.1', 'Data 2.1.2'),
        array('Data 2.2.1', 'Data 2.2.2'),
        array('Data 2.3.1', 'Data 2.3.2')
      ),
    );

    // Create a file path
    $correct_path = file_create_filename('phpexcel_test3.xls', $this->directory);

    // The filename will be munged by the export function, so:
    $this->no_headers_file = phpexcel_munge_filename($correct_path);

    // Should pass
    $this->assertEqual(
      PHPEXCEL_SUCCESS,
      phpexcel_export(NULL, $data, $correct_path, array('ignore_headers' => TRUE)),
      t('Exported data to !path', array('!path' => $this->no_headers_file))
    );

    // Should pass
    $this->assertTrue(filesize($this->no_headers_file) > 0, 'Filesize should be bigger than 0');

    /**
     * Import and check.
     * Import, not keyed by headers
     */
    $data = phpexcel_import($this->no_headers_file, FALSE);

    // Should pass
    $this->assertTrue(!!$data, 'Import succeeded');

    // Should have 3 rows
    $count = !empty($data[0]) ? count($data[0]) : 0;
    $this->assertTrue($count === 3, t('!count rows, expect 3', array('!count' => $count)));

    // Should only have 2 cells
    $count = !empty($data[0][0]) ? count($data[0][0]) : 0;
    $this->assertTrue($count === 2, t('!count cells, expect 2', array('!count' => $count)));

    $header = !empty($data[0][0][0]) ? $data[0][0][0] : 'no';
    $this->assertTrue($header === 'Data 1.1.1', 'Should be "Data 1.1.1"');

    $header = !empty($data[1][1][1]) ? $data[1][1][1] : 'no';
    $this->assertTrue($header === 'Data 2.2.2', 'Should be "Data 2.2.2"');
  }

  /**
   * Test a simple, single worksheet Excel export.
   */
  public function testTemplateExport() {
    /**
     * Export
     */
    // Prepare the data
    $data = array(
      array('Data 1.1', 'Data 1.2'),
      array('Data 2.1', 'Data 2.2'),
      array('Data 3.1', 'Data 3.2')
    );

    // Options
    $options = array(
      'format' => 'xlsx',
      'template' => drupal_get_path('module', 'phpexcel') . '/tests/data/phpexcel.test.template.xlsx',
      'ignore_headers' => TRUE
    );

    // Create a file path
    $correct_path = file_create_filename('phpexcel_test4.xlsx', $this->directory);

    // The filename will be munged by the export function, so:
    $this->template_file = phpexcel_munge_filename($correct_path);

    // Should pass
    $this->assertEqual(
      PHPEXCEL_SUCCESS,
      phpexcel_export(array(), $data, $correct_path, $options),
      t('Exported data to !path', array('!path' => $this->template_file))
    );

    // Should pass
    $this->assertTrue(filesize($this->template_file) > 0, 'Filesize should be bigger than 0');

    /**
     * Import and check.
     */
    $data = phpexcel_import($this->template_file, FALSE);

    // Should pass
    $this->assertTrue(!!$data, 'Import succeeded');

    // Should have 3 rows (3 rows and no headers)
    $count = !empty($data[0]) ? count($data[0]) : 0;
    $this->assertTrue($count === 3, t('!count rows, expect 3', array('!count' => $count)));

    // First row, 1st cell should be 'Data 1.1'
    $header = !empty($data[0][0][0]) ? $data[0][0][0] : 'no';
    $this->assertTrue($header === 'Data 1.1', 'First row, 1st cell data should be "Data 1.1", and is ' . $header);

    // Second row, 1st cell should be 'Data 2.1'
    $header = !empty($data[0][1][0]) ? $data[0][1][0] : 'no';
    $this->assertTrue($header === 'Data 2.1', 'Second row, 1st cell data should be "Data 2.1", and is ' . $header);
  }

  /**
   * Test db_result export.
   */
  public function testDBResultExport() {
    /**
     * Export
     */
    // Create 10 nodes
    for ($i = 10; $i > 0; $i--) {
      $this->_createNode();
    }

    // Get the db query result
    $result = db_select('node', 'n')
      ->fields('n', array('nid', 'title'))
      ->execute();

    // Create a path
    $correct_path = file_create_filename('phpexcel_test4.xlsx', $this->directory);

    // The filename will be munged by the export function, so:
    $this->db_result_file = phpexcel_munge_filename($correct_path);

    // Try exporting to Excel 2007
    $options = array(
      'format' => 'xlsx',
      'creator' => 'SimpleTest',
      'title' => 'DBResult',
      'subject' => 'test',
      'description' => 'my description'
    );

    // Should pass
    $this->assertEqual(
      PHPEXCEL_SUCCESS,
      phpexcel_export_db_result($result, $correct_path),
      t('Exported data to !path', array('!path' => $this->db_result_file))
    );

    // Should pass
    $this->assertTrue(filesize($this->db_result_file) > 0, 'Filesize should be bigger than 0');

    /**
     * Import and check.
     * Import, cells keyed by headers.
     */
    $data = phpexcel_import($this->db_result_file);

    // Should pass
    $this->assertTrue(!!$data, 'Import succeeded');

    // Should have 10 rows
    $count = count($data[0]);
    $this->assertTrue($count === 10, t('!count rows, expect 10', array('!count' => $count)));

    // Should only have 2 cells
    $count = count($data[0][0]);
    $this->assertTrue($count === 2, t('!count cells, expect 2', array('!count' => $count)));

    // Should be keyed by headers (nid & title)
    $this->assertTrue(isset($data[0][0]['nid']), 'Keyed by header (nid)');
    $this->assertTrue(isset($data[0][1]['title']), 'Keyed by header (title)');
  }

  /**
   * Test the ability to pass cutom methods and arguments on import to the Reader.
   */
  public function testCustomMethodCalls() {
    /**
     * Export
     */
    // Prepare data
    $headers = array(
      'Sheet 1' => array('Header 1.1', 'Header 1.2'),
      'Sheet 2' => array('Header 2.1', 'Header 2.2')
    );

    $data = array(
      'Sheet 1' => array(
        array('Data 1.1.1', 'Data 1.1.2'),
        array('Data 1.2.1', 'Data 1.2.2'),
        array('Data 1.3.1', 'Data 1.3.2')
      ),
      'Sheet 2' => array(
        array('Data 2.1.1', 'Data 2.1.2'),
        array('Data 2.2.1', 'Data 2.2.2'),
        array('Data 2.3.1', 'Data 2.3.2')
      ),
    );

    // Create a file path
    $correct_path = file_create_filename('phpexcel_test2.xls', $this->directory);

    // The filename will be munged by the export function, so:
    $this->multiple_worksheet_file = phpexcel_munge_filename($correct_path);

    // Should pass
    $this->assertEqual(
      PHPEXCEL_SUCCESS,
      phpexcel_export($headers, $data, $correct_path),
      t('Exported data to !path', array('!path' => $this->multiple_worksheet_file))
    );

    // Should pass
    $this->assertTrue(filesize($this->multiple_worksheet_file) > 0, 'Filesize should be bigger than 0');

    /**
     * Import and check.
     * Pass a method call to only import a specific worksheet.
     */
    $data = phpexcel_import($this->multiple_worksheet_file, TRUE, TRUE, array('setLoadSheetsOnly' => array('Sheet 1')));

    // Should pass
    $this->assertTrue(!!$data, 'Import succeeded');

    // Should have 3 rows
    $count = !empty($data['Sheet 1']) ? count($data['Sheet 1']) : 0;
    $this->assertTrue($count === 3, t('!count rows, expect 3', array('!count' => $count)));

    // Should be empty.
    $count = !empty($data['Sheet 2']) ? count($data['Sheet 2']) : 0;
    $this->assertTrue($count === 0, t('!count rows, expect 0', array('!count' => $count)));
  }


  protected function _createNode() {
    $node = (object) array(
      'type' => 'page',
      'title' => $this->randomName(8),
      'body' => $this->randomName(20),
    );

    node_save($node);
  }
}


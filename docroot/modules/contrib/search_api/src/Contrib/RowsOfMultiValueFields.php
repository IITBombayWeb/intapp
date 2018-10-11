<?php

namespace Drupal\search_api\Contrib;

use Consolidation\OutputFormatters\Options\FormatterOptions;
use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Consolidation\OutputFormatters\StructuredData\RenderCellInterface;

/**
 * Outputs multi-valued data as comma-separated values.
 *
 * This is used in the Drush integration.
 */
class RowsOfMultiValueFields extends RowsOfFields implements RenderCellInterface {

  /**
   * {@inheritdoc}
   */
  public function renderCell($key, $cellData, FormatterOptions $options, $rowData) {
    if (is_array($cellData)) {
      return static::arrayToString($cellData);
    }
    return $cellData;
  }

  /**
   * Converts an array of string data into a comma separated string.
   *
   * @param array $array
   *   A multidimensional array of string data.
   *
   * @return string
   *   A comma separated string.
   */
  protected static function arrayToString(array $array) {
    $elements = [];
    foreach ($array as $element) {
<<<<<<< HEAD
      $elements[] = is_array($element) ? '"' . self::arrayToString($element) . '"' : $element;
=======
      $elements[] = is_array($element) ? '"' . static::arrayToString($element) . '"' : $element;
>>>>>>> origin/development
    }
    return implode(',', $elements);
  }

}

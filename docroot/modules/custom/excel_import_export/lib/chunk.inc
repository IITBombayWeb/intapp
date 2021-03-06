<?php

/**
 * @file
 * Contains \Drupal\excel_import_import\ExcelImportExportHelper.
 */

/**
 * Contains static helper functions for FAQ module.
 */
class ChunkReadFilter {
  protected $start = 0;
  protected $end = 0;

  public function setRows($start, $chunk_size) {
    $this->start = $start;
    $this->end   = $start + $chunk_size;
  }

  public function readCell($column, $row, $worksheetName = '') {
    // Only read the heading row, and the rows that are between
    // $this->start and $this->end.
    if (($row == 1) || ($row >= $this->start && $row < $this->end)) {
      return TRUE;
    }
    return FALSE;
  }
}
<?php

/**
 * @file
 * Contains custom table views data.
 */

/**
 * Implements hook_views_data().
 */
function excel_import_export_views_data() {
  $data = [];

  $data['excel_import_export_imports']['table']['group'] = t('Excel Import Export');
  $data['excel_import_export_imports']['table']['base'] = [
    'field' => 'sid',
    'title' => t('Excel Import Export'),
    'help' => t('Excel import export table data.'),
    'weight' => -10,
  ];
  // The SID field.
  $data['excel_import_export_imports']['sid'] = [
    'title' => t('ID'),
    'help' => t('Sequence ID'),
    'field' => [
      'id' => 'numeric',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  // Expose the uid.
  $data['excel_import_export_imports']['uid'] = [
    'title' => t('User ID'),
    'field' => [
      'id' => 'numeric',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'argument' => [
      'id' => 'numeric',
    ],
  ];

  // Example timestamp field.
  $data['excel_import_export_imports']['timestamp'] = [
    'title' => t('Imported on'),
    'help' => t('Imported Time.'),
    'field' => [
      'id' => 'date',
    ],
    'sort' => [
      'id' => 'date',
    ],
    'filter' => [
      'id' => 'date',
    ],
  ];

  // The Total rows in export.
  $data['excel_import_export_imports']['total_import'] = [
    'title' => t('Total imports'),
    'help' => t('Total rows in import excel.'),
    'field' => [
      'id' => 'numeric',
    ],
  ];

  // The Total Invalid nodes field.
  $data['excel_import_export_imports']['total_invalid_nodes'] = [
    'title' => t('Total non-applications'),
    'help' => t('Number of non-applicvations in excel.'),
    'field' => [
      'id' => 'numeric',
    ],
  ];

  // The Successfull updates.
  $data['excel_import_export_imports']['total_valid_imports'] = [
    'title' => t('Total success imports'),
    'help' => t('Total valid successfull status update.'),
    'field' => [
      'id' => 'numeric',
    ],
  ];

  // The unsuccessfull updates.
  $data['excel_import_export_imports']['total_invalid_imports'] = [
    'title' => t('Total failure imports'),
    'help' => t('Total invalid status failure updates.'),
    'field' => [
      'id' => 'numeric',
    ],
  ];

  return $data;
}

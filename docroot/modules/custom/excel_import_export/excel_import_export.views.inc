<?php

/**
 * @file
 * Contains custom table views data.
 */

/**
 * Implements hook_views_data().
 */
function excel_import_export_views_data() {
  $data = array();
    
  $data['excel_import_export_imports']['table']['group'] = t('Excel Import Export');
  $data['excel_import_export_imports']['table']['base'] = array(
    'field' => 'sid',
    'title' => t('Excel Import Export'),
    'help' => t('Excel import export table data.'),
    'weight' => -10,
  );
  // The SID field
  $data['excel_import_export_imports']['sid'] = array(
    'title' => t('ID'), 
    'help' => t('Sequence ID'), 
    'field' => array(
      'id' => 'numeric',
    ), 
    'filter' => array(
      'id' => 'numeric',
    ), 
    'sort' => array(
      'id' => 'standard',
    ),
  );
  
  // Expose the uid.
  $data['excel_import_export_imports']['uid'] = array(
    'title' => t('User ID'), 
    'field' => array(
      'id' => 'numeric',
    ),
    'sort' => array(
      'id' => 'standard',
    ),
    'filter' => array(
      'id' => 'numeric',
    ),
    'argument' => array(
      'id' => 'numeric',
    ),
  );

  // Example timestamp field.
  $data['excel_import_export_imports']['timestamp'] = array(
    'title' => t('Imported on'), 
    'help' => t('Imported Time.'), 
    'field' => array(
      'id' => 'date',
    ), 
    'sort' => array(
      'id' => 'date',
    ), 
    'filter' => array(
      'id' => 'date',
    ),
  );
  
  // The Total rows in export.
  $data['excel_import_export_imports']['total_import'] = array(
    'title' => t('Total imports'), 
    'help' => t('Total rows in import excel.'), 
    'field' => array(
      'id' => 'numeric',
    ),
  );
  
  // The Total Invalid nodes field.
  $data['excel_import_export_imports']['total_invalid_nodes'] = array(
    'title' => t('Total non-applications'), 
    'help' => t('Number of non-applicvations in excel.'), 
    'field' => array(
      'id' => 'numeric',
    ),
  );
  
  // The Successfull updates.
  $data['excel_import_export_imports']['total_valid_imports'] = array(
    'title' => t('Total success imports'), 
    'help' => t('Total valid successfull status update.'), 
    'field' => array(
      'id' => 'numeric',
    ),
  );
  
  // The unsuccessfull updates.
  $data['excel_import_export_imports']['total_invalid_imports'] = array(
    'title' => t('Total failure imports'), 
    'help' => t('Total invalid status failure updates.'), 
    'field' => array(
      'id' => 'numeric',
    ),
  );
  
  return $data;
}
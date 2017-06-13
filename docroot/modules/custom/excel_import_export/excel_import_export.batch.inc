<?php

  use Drupal\taxonomy\Entity\Term;
  use Drupal\workflow\Entity\WorkflowTransition;
  use Drupal\Core\Url;

function excel_import_export_batch_export($nids, $file_path = NULL, $zip_path = NULL, &$context) {
  // Zip folder lib file.
  module_load_include('inc', 'excel_import_export', '/lib/zip_min');
  // List of Departments.
  $query = \Drupal::entityQuery('taxonomy_term');
  $query->condition('vid', "departments");
  $tids = $query->execute();
  $terms = Term::loadMultiple($tids);
  $departments = array();
  foreach ($terms as $tid => $term) {
    $departments[$tid] = $term->get('name')->value;
  }
  
  // List of Departments.
  $query = \Drupal::entityQuery('taxonomy_term');
  $query->condition('vid', "iit_institute");
  $tids = $query->execute();
  $terms = Term::loadMultiple($tids);
  $institute = array();
  foreach ($terms as $tid => $term) {
    $institute[$tid] = $term->get('name')->value;
  }
  
  // Work flow status.
  $transition = WorkflowTransition::create(["apply_apply", 'field_name' => 'field_status']);
  $current_state = $transition->getFromState();
  $workFlowOption = $current_state->getOptions();
  
  // Valid statuses only added in export, After Download status are excluded from reports.
  $validWorkFlowOption = array('apply_apply', 'apply_need_more_info', 'apply_details_appended', 'apply_downloaded_for_department_processing');

  // Creating Headers for Export.
  $export['header'] = array(
    0 => 'Unique ID',
    1 => 'Application ID',
    2 => 'Programme',
    3 => 'Institute',
    4 => 'Department',
    5 => 'Degree',
    6 => 'Status',
    7 => 'First Name',
    8 => 'Surname',
    9 => 'DOB',
    10 => 'Gender',
    11 => 'Mobile No',
    12 => 'Telephone No',
    13 => 'Address',
    14 => 'City',
    15 => 'State',
    16 => 'Country',
    17 => 'Pincode',
    18 => 'Board/ Univ',
    19 => 'Last Degree',
    20 => 'Year of Passing',
  );
  
  // If document download enabled.
  if ($zip_path != NULL) {
    $export['header'][21] = 'Documents';
  }
  
  $export['rows'] = array();
  foreach ($nids as $nid) {
    $itemsArray = array(); // Array to hold Row wise data.
    // Export{0}: nid
    $itemsArray[0] = $nid;
    $application = node_load($nid);
    
    // Export{1}: application title.
    $itemsArray[1] = excelImportExport_getFieldValue($application, 'title', 'array');
    
    // Skip if Application not exist.
    if (!is_object($application)) {
      continue;
    }
    
    // programme values.
    $programme_id = $application->get('field_programme')->getValue();
    $programme = node_load($programme_id[0]['target_id']);
    
    // Skip if Programme not exist.
    if (!is_object($programme)) {
      continue;
    }
    
    /**
     * Start Programme details.
     */
    // Export{2}: programme_name
    $itemsArray[2] = excelImportExport_getFieldValue($programme, 'title', 'array');
    
    // Institue Details.
    // Export{3}: institute_name
    $itemsArray[3] = $institute[excelImportExport_getFieldValue($programme, 'field_institute', 'reference')];

    // Department Details.
    // Export{4}: department_name
    $itemsArray[4] = $departments[excelImportExport_getFieldValue($programme, 'field_departments', 'reference')];

    // Get Degree name.
    // Export{5}: field_degree.
    $itemsArray[5] = excelImportExport_getFieldValue($programme, 'field_degree', 'array');

    // Status is valid for export.
    $workFlowstatus = excelImportExport_getFieldValue($application, 'field_status', 'array');
    if (!in_array($workFlowstatus, $validWorkFlowOption)) {
      continue;
    }
    // Get Application workflow Status value.
    // Export{6}: field_status.
    $itemsArray[6] = $workFlowOption[$workFlowstatus];
    
    // Updating wrokflow status while downloading.
    $application->field_status->setValue('apply_downloaded_for_department_processing');
    $application->save();
    
    /**
     * End of Programme details.
     */
    /**
     * Content Author/ Applicant Details 
     */

    $user = $application->get('uid')->entity;
    
    /**
     * User Profile values
     */
    $profileArray = \Drupal::entityTypeManager()
      ->getStorage('profile')
      ->loadByProperties([
      'uid' => $user->id(),
    ]);
    $profile = $profileArray[key($profileArray)];
    // User Given Name / First name.
    // Export{7}: uname.
    $itemsArray[7] = excelImportExport_getFieldValue($profile, 'field_first_name', 'array');

    // User surname.
    // Export{8}: field_surname.
    $itemsArray[8] = excelImportExport_getFieldValue($profile, 'field_surname', 'array');

    // User Date of Birth.
    // Export{9}: field_date_of_birth.
    $itemsArray[9] = excelImportExport_getFieldValue($profile, 'field_date_of_birth', 'array');

    // User Gender.
    // Export{10}: field_gender.
    $itemsArray[10] = excelImportExport_getFieldValue($profile, 'field_gender', 'array');

    // User Mobile number.
    // Export{11}: field_mobile_num_com.
    $itemsArray[11] = excelImportExport_getFieldValue($profile, 'field_mobile_num_com', 'array') . " ";

    // User Telephone number.
    // Export{12}: field_telephone_num_com.
    $itemsArray[12] = excelImportExport_getFieldValue($profile, 'field_telephone_num_com', 'array') . " ";

    // User Permenant Address.
    // Export{13}: field_permanent_address.
    $itemsArray[13] = excelImportExport_getFieldValue($profile, 'field_permanent_address', 'array');

    // User City.
    // Export{14}: field_city.
    $itemsArray[14] = excelImportExport_getFieldValue($profile, 'field_city', 'array');

    // User State.
    // Export{15}: field_state.
    $itemsArray[15] = excelImportExport_getFieldValue($profile, 'field_state', 'array');

    // User Country.
    // Export{16}: field_country.
    $itemsArray[16] = excelImportExport_getFieldValue($profile, 'field_country', 'array');

    // User Pincode.
    // Export{17}: field_pin.
    $itemsArray[17] = excelImportExport_getFieldValue($profile, 'field_pin', 'array');

    // User Borad / University.
    // Export{18}: field_name_of_the_board_universi.
    $itemsArray[18] = excelImportExport_getFieldValue($profile, 'field_name_of_the_board_universi', 'array');

    // User Qualified Exam.
    // Export{19}: field_name_of_the_qualifying_exa.
    $itemsArray[19] = excelImportExport_getFieldValue($profile, 'field_name_of_the_qualifying_exa', 'array');

    // User Year of Passing.
    // Export{20}: field_year_of_passing.
    $itemsArray[20] = excelImportExport_getFieldValue($profile, 'field_year_of_passing', 'array');
    
    // User Supporting and General documents to Zipfolder
    if ($zip_path != NULL) {
      $wrapper = \Drupal::service('stream_wrapper_manager')->getViaUri($zip_path . '/' . $user->get('name')->value . '.zip');
      $zip_write_path = $wrapper->realpath();
      // Skip if documents already zipped.
      if (!file_exists($zip_write_path)) {
        $zipfile = new zipfile();
        $general_doc = $profile->get('field_general_documents')->getValue();
        $file_array = array();
        foreach ($general_doc as $doc_key => $file) {
          $fileObj = file_load($file['target_id']);
          $zipfile -> addFile(file_get_contents(\Drupal::service('stream_wrapper_manager')->getViaUri($fileObj->get('uri')->value)->realpath()), $fileObj->get('filename')->value);
        }
        $support_doc = $profile->get('field_supporting_d')->getValue();
        foreach ($support_doc as $doc_key => $file) {
          $fileObj = file_load($file['target_id']);
          $zipfile -> addFile(file_get_contents(\Drupal::service('stream_wrapper_manager')->getViaUri($fileObj->get('uri')->value)->realpath()), $fileObj->get('filename')->value);
        }
        if (file_prepare_directory($zip_path, FILE_CREATE_DIRECTORY)) {
          $contents = $zipfile -> file();
          file_put_contents($zip_write_path, $contents);
        }
      }
      // User documents download path.
      // Export{21}: zip file path.
      $itemsArray[21] = $zip_write_path;
    }
    
    /**
     * End of User data & Profile values.
     */
    // Appending Each Arrays.
    $export['rows'][] = $itemsArray;
  }
  module_load_include('inc', 'phpexcel');
  
  // Generate a file name. If it's unique, it's less likely to conflict with an
  // existing file. You could also put up some more checks on this, if it's likely
  // to conflict (like, when you have many export/download requests).
  if (!isset($context['sandbox']['progress'])) {
    $uri = 'temporary://';
    if (isset($file_path)) {
      $uri = $file_path . '/';
      file_prepare_directory($file_path, FILE_CREATE_DIRECTORY);
    }
    
    $wrapper = \Drupal::service('stream_wrapper_manager')->getViaUri($uri);
    $context['sandbox']['filename'] = 'student-applications-' . uniqid() . '.xls';
    $context['sandbox']['file'] = $wrapper->realpath() . '/' . $context['sandbox']['filename'];
    // Prepare the Excel file.
    $result = phpexcel_export($export['header'], array(
      // Provide some empty data. We will append data later on.
      array(),
    ), $context['sandbox']['file']);

    if ($result !== PHPEXCEL_SUCCESS) {
      drupal_set_message("Something went wrong", 'error');
      $context['sandbox']['finished'] = 1;
      $context['success'] = FALSE;
      return;
    }

    $context['sandbox']['progress'] = 0;
    $context['sandbox']['max'] = 40;
    // Trick to pass the filepath to the finished callback.
    $finish_path = "temporary://{$context['sandbox']['filename']}";
    if (isset($file_path)) {
      $finish_path = $file_path . "/" . $context['sandbox']['filename'];
    }
    $context['results'] = $finish_path;
  }

  $limit = 10;
  while($limit) {
    $result = phpexcel_export($export['header'], $export['rows'], $context['sandbox']['file'], array(
      // Use our previously prepared file as a "template", which means we
      // will append data to it, instead of starting from scratch again.
      'template' => $context['sandbox']['file'],
    ));
    if ($result !== PHPEXCEL_SUCCESS) {
      drupal_set_message(t("Something went wrong on pass !pass", array(
        '!pass' => $context['sandbox']['progress'],
      )), 'error');
      $context['sandbox']['finished'] = 1;
      $context['success'] = FALSE;
      return;
    }

    $context['sandbox']['progress']++;
    $limit--;
  }

  if ($context['sandbox']['progress'] != $context['sandbox']['max']) {
    $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
  }
}

function excel_import_export_batch_export_finish($success, $filepath, $operations) {
  if ($success) {
    $url = URL::fromUserInput('/excel/export/download?download=' . $filepath)->toString();
    drupal_set_message(t('Please click <a href="!link">here</a> to download export file or export file will available on !file', array('!file' => $filepath, '!link' => $url)));
    \Drupal::logger('excel_import_export')->notice("Excel exported by user ID !uid", array('@uid' => \Drupal::currentUser()->id(),));
  } else {
    drupal_set_message("Error in Download please try later", ERROR);
  }
}

function excel_import_export_batch_import($filepath, &$context) {
  module_load_include('inc', 'phpexcel');
  module_load_include('inc', 'excel_import_export', '/lib/chunk');
  
  if (!isset($context['sandbox']['progress'])) {
    $context['sandbox']['progress'] = 0;
    // We have no idea how many lines we have to load. Provide some large
    // number, and we'll adapt as we go along.
    $context['sandbox']['max'] = 10000;
  }

  // We need to load the library before we can instantiate our
  // ChunkReaderFilter class.
  $library = libraries_load('PHPExcel');
  if (empty($library['loaded'])) {
    drupal_set_message(t("Couldn't load the PHPExcel library."), 'error');
    $context['sandbox']['finished'] = 1;
    $context['success'] = FALSE;
    return;
  }

  // Work flow status.
  $transition = WorkflowTransition::create(["apply_downloaded_for_department_processing", 'field_name' => 'field_status']);
  $current_state = $transition->getFromState();
  $workFlowOption = $current_state->getOptions();
  $limit = 10;
  // See our module's info file below.
  $chunk_filter = new ChunkReadFilter();
  $chunk_filter->setRows($context['sandbox']['progress'], $limit);
  $data = phpexcel_import($filepath, TRUE, FALSE);

  if (!is_array($data)) {
    drupal_set_message(t("Something went wrong on pass !pass", array(
      '!pass' => $context['sandbox']['progress'],
    )), 'error');
    $context['sandbox']['finished'] = 1;
    $context['success'] = FALSE;
    return;
  }

  // Get rid of the worksheet.
  $data = $data[0];

  $i = 0;
  while($i < $limit) {
    if (!empty($data[$i])) {
      // Do something with the data, like creating a node...
      
      $context['results']['totalNodeObj'][] = $data[$i]['Unique ID'];
      
      // Updating Workflow status.
      $application = node_load($data[$i]['Unique ID']);
      if (is_object($application) && ($application->getType() == 'application')) {
        $updateStatus = array_search(trim($data[$i]['Status']), $workFlowOption);
        //dpm($data[$i]['Status']);
        if (!empty($updateStatus)) {
          $application->field_status->setValue($updateStatus);
          $application->save();
          $context['results']['validStatus'][] = $data[$i]['Unique ID'];
        } else {
          $context['results']['wrongStatus'][] = $data[$i]['Unique ID'];
        }
      } else {
        $context['results']['notNodeObj'][] = $data[$i]['Unique ID'];
      }
      
      $context['sandbox']['progress']++;
      $i++;
    }
    else {
      // We have reached the end of our file. Finish now.
      $context['sandbox']['finished'] = 1;
      $context['success'] = TRUE;
      return;
    }
  }

  if ($context['sandbox']['progress'] != $context['sandbox']['max']) {
    $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
  }
}

function excel_import_export_batch_import_finish($success, $results, $operations) {
  if ($success) {
    $totalNodeObj = $results['totalNodeObj'];
    $notNodeObj = $results['notNodeObj'];
    $validStatus = $results['validStatus'];
    $wrongStatus = $results['wrongStatus'];
    
    $total_import = count($totalNodeObj);
    $total_invalid_nodes = count($notNodeObj);
    $total_valid_imports = count($validStatus);
    $total_invalid_imports = count($wrongStatus);
    
    // Logging information
    \Drupal::database()
      ->insert('excel_import_export_imports')
      ->fields( array(
        'sid' => NULL,
        'uid' => \Drupal::currentUser()->id(),
        'timestamp' => time(),
        'total_import' => $total_import,
        'total_invalid_nodes' => $total_invalid_nodes,
        'invalid_nodes' => excelImportExport_serialize_nids($notNodeObj),
        'valid_imports' => excelImportExport_serialize_nids($validStatus),
        'total_valid_imports' => $total_valid_imports,
        'invalid_imports' => excelImportExport_serialize_nids($wrongStatus),
        'total_invalid_imports' => $total_invalid_imports,
      ))
      ->execute();
    // Watchdog entries.
    \Drupal::logger('excel_import_export')->notice("Total import rows : '@total_valid_row'. Total valid status update : '@total_valid_status'.  Total invalid status : '@total_invalid_status'.  Total invalid node objects : '@total_invalid_row'.", array('@total_valid_row' => $total_import, '@total_invalid_row' => $total_invalid_nodes, '@total_valid_status' => $total_valid_imports, '@total_invalid_status' => $total_invalid_imports,));
    
    // Status message.
    drupal_set_message(t("Total rows in import is @total_import.", array('@total_import' => $total_import)));
    if ($total_invalid_nodes) {
      drupal_set_message(t("Total invalid IDs in import @total_invalid.", array('@total_invalid' => $total_invalid_nodes)), 'error');
    }
    if ($total_valid_imports) {
      drupal_set_message(t("Total valid Status updates @total_valid.", array('@total_valid' => $total_valid_imports)));
    }
    if ($total_invalid_imports) {
      drupal_set_message(t("Total invalid status in import @total_invalid.", array('@total_invalid' => $total_invalid_imports)), 'error');
    }
    drupal_set_message($message);
  }
}

/**
 * Helper Function for Get Field Value by object.
 * @param1 {object} : Object to get value.
 * @param2 {string} : Property String.
 * @param3 {string} : Type of parse field.
 * @return {string} : Return Value.
 */
function excelImportExport_getFieldValue($entityObject, $property, $type = FALSE) {
  $valueObject = $entityObject->get($property);
  if (is_object($valueObject)) {
    $return = $valueObject->getValue();
    if ($type) {
      switch ($type) {
        case 'array':
          $return = $return[0]['value'];
          break;
        case 'reference':
          $return = $return[0]['target_id'];
          break;
        case 'entity':
          $return = $return->entity;
          break;
      }
    }
    return $return;
  }
}
  
/**
 * Helper function to node ids.
 */
function excelImportExport_serialize_nids($nidsArray) {
  $nids = array();
  foreach ($nidsArray as $nid) {
    if ($nid) {
      $nids[] = $nid;
    }
  }
  $nids = serialize($nids);
  return $nids;
}
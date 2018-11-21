<?php

/**
 * @file
 * Contains \Drupal\excel_import_export\Controller\ExcelImportExportController.
 */

namespace Drupal\excel_import_export\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * Controller routines for FAQ Ask routes.
 */
class ExcelImportExportController extends ControllerBase {

  /**
   * Renders the form for the Content Import.
   *
   * @return
   *   The form code inside the $build array.
   */
  public function excelContentImportSettings() {
    $build = array();
    $build['excel_content_import_form'] = $this->formBuilder()->getForm('Drupal\excel_import_export\Form\ContentImportForm');
    return $build;
  }
  
  /**
   * Renders the form for the Content Export.
   *
   * @return
   *   The form code inside the $build array.
   */
  public function excelExportDownloadSettings() {
    $filepath = $_GET['download'];
    if (isset($filepath)) {
      if (file_exists($filepath)) {
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($filepath));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        ob_clean();
        flush();
        readfile($filepath);

       /*
        //Get file type and set it as Content Type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        header('Content-Type: ' . finfo_file($finfo, $filepath));
        finfo_close($finfo);
     
        //Use Content-Disposition: attachment to specify the filename
        header('Content-Disposition: attachment; filename=' . basename($filepath));
     
        //No cache
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
     
        //Define file size
        header('Content-Length: ' . filesize($filepath));
     
        ob_clean();
        flush();
        readfile($filepath);
        */
      }
      $markup = "<p>Please save excel file from pop!</p>";
      $markup .= "<p><b>Note</b>: If you not able to download please get file from " . $filepath . " in server.</p>";
    } else {
      $url = URL::fromUserInput('/view-application-lists')->toString();
      $markup .= "<p>No download file exist please click <a href=" . $url . "> here </a> to export again</p>";
    }
    return array(
      '#markup' => $markup,
    );
  }
  
  /**
   * Renders the form for the Content Import.
   *
   * @return
   *   The form code inside the $build array.
   */
  public function exportConfigureSettings() {
    $build = array();
    $build['excel_export_configure_form'] = $this->formBuilder()->getForm('Drupal\excel_import_export\Form\ExportConfigureForm');
    return $build;
  }
  
  /**
   * Excel export batch process page. 
   */
  public function excelContentExportSettings() {

    // Application Node Ids from  filter.
    $query = \Drupal::database()->select('node_field_data', 'n');
    $query->fields('n', ['nid']);
    $query->condition('n.type', 'application');
    $query->condition('n.status', 1, '=');
    $query->join('node__field_programme', 'nfp', 'n.nid = nfp.entity_id');
    $query->join('node__field_institute', 'nfi', 'nfp.field_programme_target_id = nfi.entity_id');
    $query->condition('nfi.field_institute_target_id', $_GET['tid'], '=');
    $query->condition('nfi.bundle', 'programme', '=');
    $query->innerjoin('user__roles', 'ur', 'n.uid = ur.entity_id');
    $query->condition('ur.roles_target_id', 'student', '=');
    $query->condition('n.uid', 1, '!=');
    if (!empty($_GET['min']) && !empty($_GET['max'])) {
      $query->condition('n.created', array(strtotime($_GET['min']), strtotime($_GET['max'])), 'BETWEEN');
    }
    $query->orderBy('n.nid', 'DESC');
    $result = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
    foreach ($result as $row) {
      $nids[] = $row->nid;
    }
    $export_settings = $this->config('excel_import_export.settings');
    $file_path = NULL;
    // Export file path.
    $file_path = $export_settings->get('export_file_location') . date('Y-m', time());
    
    // Export Documents as Zip.
    if ($export_settings->get('enable_zip')) {
      $zip_path = $export_settings->get('export_zip_locaton') . date('Y-m', time()) . '/' . time();
    }
    
    $batch = array(
      'title' => t('Exporting Applications'),
      'operations' => array(
        array('excel_import_export_batch_export', array($nids, $file_path, $zip_path)),
      ),
      'finished' => 'excel_import_export_batch_export_finish',
      'file' => drupal_get_path('module', 'excel_import_export') . '/excel_import_export.batch.inc',
    );
    batch_set($batch);
    return batch_process('/view-application-lists/' . $_GET['tid']);
  }
  
  /**
   * Excel Import details page. 
   */
  public function excelImportDetails($sid) {
    // Excel import details fetch.
    $query = \Drupal::database()->select('excel_import_export_imports', 'eiei');
    $query->fields('eiei', ['uid', 'timestamp', 'total_import', 'total_invalid_nodes', 'invalid_nodes', 'valid_imports', 'total_valid_imports', 'invalid_imports', 'total_invalid_imports']);
    $query->condition('eiei.sid', $sid);
    $result = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
    $markup = '';
    $markup .= '<table class="imp-det-tbl">';
    foreach ($result as $row) {
      $markup .= '<tr><th class="imp-tbl-th"> Imported By </th><td class="imp-tbl-td"> '.user_load($row->uid)->get('name')->value.'</td></tr>';
      $markup .= '<tr><th  class="imp-tbl-th" > Imported On </th><td class="imp-tbl-td"> ' . date('Y M d, H:i', $row->timestamp) .'</td></tr>' ;
      $markup .= '<tr><th  class="imp-tbl-th"> Total Records submitted </th><td class="imp-tbl-td" > ' . $row->total_import .'</td></tr>' ;
      $markup .= '<tr><th  class="imp-tbl-th"> Total valid applications submitted </th><td class="imp-tbl-td" > ' . ($row->total_import - $row->total_invalid_nodes) .'</td></tr>';
      $markup .= '<tr><th  class="imp-tbl-th"> Total invalid applications submitted </th><td class="imp-tbl-td" > ' . ($row->total_invalid_nodes) .'</td></tr>' ;
      $markup .= '<tr><th  class="imp-tbl-th"> Non Valid Application IDs </th><td class="imp-tbl-td">  ';
      // Invalid Applications details.
      $invalidApplications = excelImportExportUnserialize($row->invalid_nodes);
      foreach ($invalidApplications as $value) {
        $markup .= $value . '<br>';  
      }
      $markup .= '</td></tr>';
      // Valid Import details.
      $markup .= '<tr><th  class="imp-tbl-th"> Total valid status updates </th><td class="imp-tbl-td" >' . ($row->total_valid_imports) .'</td></tr>' ;
      $markup .= '<tr><th  class="imp-tbl-th"> Valid Import IDs </th>
      <td class="imp-tbl-td">';
      $validStatus = excelImportExportUnserialize($row->valid_imports);
      foreach ($validStatus as $value) {
        $markup .= $value . '<br>';  
      }
        $markup .= '</td></tr>';
      // Invalid Import details.
      $markup .= '<tr><th  class="imp-tbl-th">Total Invalid status updates </th><td class="imp-tbl-td"> ' . ($row->total_invalid_imports);
      $markup .= '<tr><th  class="imp-tbl-th">Invalid Import IDs </th><td class="imp-tbl-td" >';
      $invalidStatus = excelImportExportUnserialize($row->invalid_imports);
      foreach ($invalidStatus as $value) {
        $markup .= $value . '<br>';  
      }
      $markup .= '</td></tr>';
    }
    $markup .= '</table>';
    return array(
      '#markup' => $markup,
    );
  }
  
}

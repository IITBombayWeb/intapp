<?php

namespace Drupal\custom_zip_download\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;
use \Drupal\node\Entity\Node;

/**
 * Provides a 'ZipDownload' action.
 *
 * @Action(
 *  id = "zip_download",
 *  label = @Translation("Zip download"),
 *  type = "node",
 * )
 */
class ZipDownload extends ActionBase {
  /**
   * {@inheritdoc}
   */
  public function execute($object = NULL) {
    // Insert code here.
  }
  /**
   * {@inheritdoc}
   */
  public function executeMultiple(array $entities) {
     //global $base_url;
    # create new zip opbject
    $zip = new \ZipArchive();
    # create a temp file & open it
    $tmp_file = tempnam('.','');
    $zip->open($tmp_file, \ZipArchive::CREATE);
    # loop through each file
    foreach($entities as $entity){
        # download file
          $get_path = $entity->get('field_application_path')->getValue();
          $application_path = $get_path[0]['value'];
          $filename = explode('/', $application_path);
          $filename = end($filename);
          //$download_file    = file_get_contents($base_url . '/sites/default/files/applications/' . $filename);
          
    }
    $base_path  = 'sites/default/private/';
        //$content    = file_get_contents($host . '/' . $base_path . 'applications/' . $filename);
        $content    = file_get_contents($base_path . 'applications/' . $filename);
        #add it to the zip
        $zip->addFromString(basename($filename),$download_file);
    # close zip
    $zip->close();
    # send the file to the browser as a download
    header('Content-disposition: attachment; filename=Applications.zip');
    header('Content-type: application/zip');
    readfile($tmp_file);
    exit;
  }
  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $access = $object->status->access('edit', $account, TRUE) ->andIf($object->access('update', $account, TRUE));
    return $return_as_object ? $access : $access->isAllowed();
  }
}

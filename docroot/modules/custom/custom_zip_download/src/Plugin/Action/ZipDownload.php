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

    $zipTest = new zipfile();
    foreach ($entities as $entity) {
      $get_path = $entity->get('field_application_path')->getValue();
      $application_path = $get_path[0]['value'];
      $filename = explode('/', $application_path);
      $filename = end($filename);
      $zipTest->add_file($application_path, $filename);
    }
    Header("Content-type: application/octet-stream");
    Header ("Content-disposition: attachment; filename=Applications.zip");
    print $zipTest->file();
    exit;
  }
  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $access = $object->status->access('edit', $account, TRUE)
      ->andIf($object->access('update', $account, TRUE));

    return $return_as_object ? $access : $access->isAllowed();
  }

}

/**
 * Class zipfile for download zip formats.
 */
class zipfile {

  var $datasec = array();
  var $ctrl_dir = array();
  var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";
  var $old_offset = 0;
  function add_dir($name) {
    $name = str_replace("", "/", $name);
    $fr = "\x50\x4b\x03\x04";
    $fr .= "\x0a\x00";
    $fr .= "\x00\x00";
    $fr .= "\x00\x00";
    $fr .= "\x00\x00\x00\x00";
    $fr .= pack("V",0);
    $fr .= pack("V",0);
    $fr .= pack("V",0);
    $fr .= pack("v", strlen($name) );
    $fr .= pack("v", 0 );
    $fr .= $name;
    $fr .= pack("V", 0);
    $fr .= pack("V", 0);
    $fr .= pack("V", 0);
    $this -> datasec[] = $fr;
    $new_offset = strlen(implode("", $this->datasec));

    $cdrec = "\x50\x4b\x01\x02";
    $cdrec .="\x00\x00";
    $cdrec .="\x0a\x00";
    $cdrec .="\x00\x00";
    $cdrec .="\x00\x00";
    $cdrec .="\x00\x00\x00\x00";
    $cdrec .= pack("V",0);
    $cdrec .= pack("V",0);
    $cdrec .= pack("V",0);
    $cdrec .= pack("v", strlen($name) );
    $cdrec .= pack("v", 0 );
    $cdrec .= pack("v", 0 );
    $cdrec .= pack("v", 0 );
    $cdrec .= pack("v", 0 );
    $ext = "\x00\x00\x10\x00";
    $ext = "\xff\xff\xff\xff";
    $cdrec .= pack("V", 16 );
    $cdrec .= pack("V", $this -> old_offset );
    $cdrec .= $name;

    $this -> ctrl_dir[] = $cdrec;
    $this -> old_offset = $new_offset;
    return;
  }

  function add_file($data, $name) {
    $fp = fopen($data,"r");
    $data = fread($fp,filesize($data));
    fclose($fp);
    $name = str_replace("", "/", $name);
    $unc_len = strlen($data);
    $crc = crc32($data);
    $zdata = gzcompress($data);
    $zdata = substr ($zdata, 2, -4);
    $c_len = strlen($zdata);
    $fr = "\x50\x4b\x03\x04";
    $fr .= "\x14\x00";
    $fr .= "\x00\x00";
    $fr .= "\x08\x00";
    $fr .= "\x00\x00\x00\x00";
    $fr .= pack("V",$crc);
    $fr .= pack("V",$c_len);
    $fr .= pack("V",$unc_len);
    $fr .= pack("v", strlen($name) );
    $fr .= pack("v", 0 );
    $fr .= $name;
    $fr .= $zdata;
    $fr .= pack("V",$crc);
    $fr .= pack("V",$c_len);
    $fr .= pack("V",$unc_len);

    $this -> datasec[] = $fr;
    $new_offset = strlen(implode("", $this->datasec));

    $cdrec = "\x50\x4b\x01\x02";
    $cdrec .="\x00\x00";
    $cdrec .="\x14\x00";
    $cdrec .="\x00\x00";
    $cdrec .="\x08\x00";
    $cdrec .="\x00\x00\x00\x00";
    $cdrec .= pack("V",$crc);
    $cdrec .= pack("V",$c_len);
    $cdrec .= pack("V",$unc_len);
    $cdrec .= pack("v", strlen($name) );
    $cdrec .= pack("v", 0 );
    $cdrec .= pack("v", 0 );
    $cdrec .= pack("v", 0 );
    $cdrec .= pack("v", 0 );
    $cdrec .= pack("V", 32 );
    $cdrec .= pack("V", $this -> old_offset );

    $this -> old_offset = $new_offset;
    
    $cdrec .= $name;
    $this -> ctrl_dir[] = $cdrec;
  }

  function file() {
    $data = implode("", $this -> datasec);
    $ctrldir = implode("", $this -> ctrl_dir);

    return
    $data .
    $ctrldir .
    $this -> eof_ctrl_dir .
    pack("v", sizeof($this -> ctrl_dir)) .
    pack("v", sizeof($this -> ctrl_dir)) .
    pack("V", strlen($ctrldir)) .
    pack("V", strlen($data)) .
    "\x00\x00";
  }

}

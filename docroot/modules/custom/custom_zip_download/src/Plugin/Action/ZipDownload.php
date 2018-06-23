<?php

namespace Drupal\custom_zip_download\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;
use \Drupal\node\Entity\Node;
use Drupal\profile\Entity\Profile;
use Drupal\profile\Entity\ProfileType;
use Drupal\file\Entity\File;

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
  
    
    # create new zip opbject
    $zip = new \ZipArchive();
    # create a temp file & open it
    $tmp_file = tempnam('.','');
    $zip->open($tmp_file, \ZipArchive::CREATE);
    # loop through each file
     $ins = "IIT_Bombay3";
     $new_folder = 'private://temp_general_doc1/'.$ins;
     file_prepare_directory($new_folder, FILE_CREATE_DIRECTORY);
     echo "<pre>";
    foreach($entities as $entity){
      //print_r($entity);

        # download file
        $get_path = $entity->get('field_application_path')->getValue();
        $get_users = $entity->get('field_user_id')->getValue(); 
        $user_name = user_load($get_users[0]['value'])->get('name')->getValue();
        $application_path = $get_path[0]['value'];
      //   $filename = explode('/', $application_path);
      //   $filename = end($filename);
      //   $base_path  = \Drupal::service('file_system')->realpath("private://");
      //   $src = $application_path;
      //   $dest_1 = $new_folder.'/'.$user_name[0]['value'];
      //   $path = str_replace('private:/',$base_path,$dest_1);
      //   //$dest_1 = str_replace('private:/',$base_path,$dest_1);
      //   $zip->addEmptyDir($user_name[0]['value']);
      //   $zip->addFile($src, $user_name[0]['value'].'/'.$filename);
      //   $options = array('add_path' => $user_name[0]['value'].'/', 'remove_all_path' => TRUE);
      // $zip->addGlob('../private/student_documents/general_documents/'.$get_users[0]['value'].'/'.'*.{txt,doc,pdf,docx}', GLOB_BRACE, $options);
      $query = \Drupal::entityQuery('profile')
        ->condition('status', 1)
        ->condition('uid', $get_users[0]['value'])
        ->condition('type', 'office');
      $nids = $query->execute();
      $nids = array_values($nids);
      $profile = Profile::load($nids[0]);
      print_r($profile);
        //file_prepare_directory($dest_1, FILE_CREATE_DIRECTORY);
        //$path = str_replace('private:/',$base_path,$dest_1);
        //copy($src, $path.'/'.$filename);
        //$zip->addFromString(basename($filename),$content);
        $users[]=$get_users[0]['value'];  
    }
    exit;
  //print_r($users);
  //exit;
    //foreach ($users as $key => $user_id) {
    //  $query = \Drupal::entityQuery('profile')
    //            ->condition('status', 1)
    //            ->condition('uid', $user_id);
    //            $nids = $query->execute();
    //            foreach($nids as $key =>$value){
    //              $nid =$value;
    //            }
    //  $profile = Profile::load($value);
    //  foreach($profile->get('field_general_documents')->getValue() as $key => $value){   
    //    $file_ld = File::load($value['target_id']);
    //    $fle_path = $file_ld->get('uri')->getValue()[0]['value'];
    //    $filename2 = explode('/', $fle_path);
    //    $filename2 = end($filename2);
    //    $spl_doc1 =  str_replace("private:/","",$fle_path);
    //    $content2    = file_get_contents($base_path . $spl_doc1);
    //    $zip->addFromString(basename($filename2),$content2);
    //  }
    //  
    //}
    
    foreach($users as $key => $value){
      $new_folder = 'private://temp_general_doc/'.$value;
      file_prepare_directory($new_folder, FILE_CREATE_DIRECTORY);
    }
    
  

    //$new_folder = 'private://temp_general_doc/'.;
    //$welcome = "/var/www/html/newintapp/private/newfolder";
    //$all= new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($welcome));
    //print_r($all);
    //exit;
    //
    //file_prepare_directory($new_folder, FILE_CREATE_DIRECTORY);
    //copy(', '/var/www/html/newintapp/private/newfolder');
    
      //$src = "/var/www/html/newintapp/private/student_documents/IIScBang-2018-04-3350-681/IIScBang-2018-04-3350-681.pdf";  // source folder or file
      //$dest = "/var/www/html/newintapp/private/newfolder/nagarajiii.pdf";   // destination folder or file        
      //shell_exec("cp -r $src $dest");
     // copy($src, $dest);
      //exec("zip -r test.zip /var/www/html/newintapp/private/newfolder/");
     //$zip->add_directory($src);
     //$this->add_directory($directory . '/' . $file );
    //# close zip
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


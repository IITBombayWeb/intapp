<?php

/**
 * @file
 * Contains \Drupal\iitinap\Controller\CartController.
 */

namespace Drupal\iitinap\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\Query\QueryFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\profile\Entity\Profile;
use Drupal\profile\Entity\ProfileType;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Access;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Entity\Display\EntityFormDisplayInterface; 
use Drupal\workflow\Entity\WorkflowTransitionInterface;

/**
 * Contains the cart controller.
 */
class CustomController extends ControllerBase
{
 
  protected $entity_query;
 
  public function __construct(QueryFactory $entity_query) {
    $this->entity_query = $entity_query;
  }
 
  public static function create(ContainerInterface $container) {
    return new static(
     $container->get('entity.query')
    );
  }

  public function viewApplication() {
     $user = \Drupal::currentUser();
     $roles = $user->getRoles();
     if(in_array('office', $roles)) {
      $query = $this->entity_query->get('profile')
        ->condition('status', 1)
        ->condition('uid', $user->id())
        ->condition('type', 'office');
      $nids = $query->execute();
      $nids = array_values($nids);
      if(isset($nids[0])) {
        $profile = Profile::load($nids[0]);
        $langcode = $profile->language()->getId();
        $term = $profile->getTranslation($langcode)->get('field_institute')->getValue()[0]['target_id']; 
        return new RedirectResponse(URL::fromUserInput('/view-application-lists/'. $term)->toString());
      } 
     }else{
        drupal_set_message($this->t('No Access'),'warning');
        return $this->redirect('basiccart.cart');
     }
  }

  public function getProfile() {
    $user = \Drupal::currentUser();
     $roles = $user->getRoles();
     if(in_array('student', $roles)) {
     $url = new Url('entity.profile.type.student_application_.user_profile_form',array("user"=>$user->id(),"profile_type" => "student_application_"));    
      return new RedirectResponse($url->toString());
     }else{
      return new RedirectResponse(URL::fromUserInput('/search')->toString());
     } 
  }
  
  public function editProfile() {
    $node = \Drupal::routeMatch()->getParameter('node');
     $nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->load($node);
       $type = $nodes->getTranslation('en')->get('type')->getValue()[0]['target_id'];
    if($type == 'application') {
    $user = \Drupal::currentUser();
    $roles = $user->getRoles();
     $profile_type = ProfileType::load('student_application_');
      $query = $this->entity_query->get('profile')
        ->condition('status', 1)
        ->condition('uid', $user->id());
      $nids = $query->execute();
   	$result = db_select('workflow_transition_history', 't_alias')->fields('t_alias', array('hid', 'comment'))->condition('entity_id', $nodes->get('nid')->value )->condition('to_sid', 'apply_need_more_info')->execute()->fetchAll();
      $nids = array_values($nids);
      if(isset($nids[0])) {
        $profile = Profile::load($nids[0]);
      	$create_form = $this->entityFormBuilder()->getForm($profile); 
        return array(
            '#type' => 'markup',
            '#markup' => render($create_form),
            '#prefix' => '<div> Comment : '.$result[0]->comment.'</div>',
        );
      }
    }
  }
  
  public function accessProfile($node){
    $node_data = node_load($node);
    $node_type = $node_data->type->getValue()[0]['target_id'];
    if ($node_type == 'application') {
      $node_status = $node_data->getTranslation('en')->get('field_status')->getValue()[0]['value'];
      if( $node_status == 'apply_need_more_info'){
        return AccessResult::allowed();
      }
    } else {
        return AccessResult::forbidden();
    }
      return AccessResult::forbidden();
  }
  public function content(){
    $nids = \Drupal::entityQuery('node')
        ->condition('type', 'application')
        ->execute();
    $nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadMultiple($nids);
        $i =0;
      foreach($nids as $nid){
         $langcode = $nodes[$nid]->language()->getId();
        $value = $nodes[$nid]->getTranslation('en')->get('field_programme')->getValue()[0]['target_id'];
     
        $term1 = Node::load($value);
           if(isset($term1)){
        $data = array();
        $data .= $term1->getTranslation('en')->get('title')->getValue()[0]['value'];
        $programme = array();
        $programme .= $data;
            }
      }
      $row = array();
    $row = array(0=> array(
                           'a'=>'a',
                           'b'=>'b',
                    ),
                );
    return array(
                '#type' => 'table',
                '#header' => array('a','b','c','d'),
                '#rows' => $row,
              ); 
  }
  
}
  

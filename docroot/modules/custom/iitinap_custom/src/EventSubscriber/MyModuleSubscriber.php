<?php

/**
 * @file
 * Contains \Drupal\mymodule\EventSubscriber\MyModuleSubscriber.
 */

// Declare the namespace for our own event subscriber.
namespace Drupal\iitinap_custom\EventSubscriber;

// This is the interface that we are implementing.
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

// This is a generic event class that Simple FB Connect will dispatch
use Symfony\Component\EventDispatcher\GenericEvent;

// We need these classes to interact with SimpleFbConnect and FB SDK.
use Drupal\simple_fb_connect\SimpleFbConnectFbFactory;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Exceptions\FacebookResponseException;

/**
 * Event subscriptions for events dispatched by SimpleFbConnect.
 */
class MyModuleSubscriber implements EventSubscriberInterface {

  protected $facebook;
  protected $persistentDataHandler;

  /**
   * Constructor.
   *
   * We use dependency injection get SimpleFbConnectFbFactory.
   *
   * @param SimpleFbConnectFbFactory $fb_factory
   *   For getting Facebook and SimpleFbConnectPersistentDataHandler services.
   */
  public function __construct(SimpleFbConnectFbFactory $fb_factory) {
    $this->facebook = $fb_factory->getFbService();
    $this->persistentDataHandler = $fb_factory->getPersistentDataHandler();
  }

  /**
   * Returns an array of event names this subscriber wants to listen to.
   *
   * @return array
   *   The event names to listen to
   */
  static function getSubscribedEvents() {
    $events = array();
    $events['simple_fb_connect.scope'][] = array('modifyPermissionScope');
    $events['simple_fb_connect.user_created'][] = array('userCreated');
    $events['simple_fb_connect.user_login'][] = array('userLogin');
    return $events;
  }

  /**
   * Adds Facebook permissions to the scope array.
   *
   * Facebook permissions can be found at
   * https://developers.facebook.com/docs/facebook-login/permissions
   *
   * Note that most permissions require that Facebook will review your app
   * so only add those permissions that you really need. In this example we
   * add 'public_profile' which you don't have to do since this permission
   * is always granted.
   */
  public function modifyPermissionScope(GenericEvent $event) {
    $scope = $event->getArgument('scope');
    // Add the permission here. In this example we add 'public_profile'.
    $scope[] = 'public_profile';
    $event->setArgument('scope', $scope);
  }

  /**
   * Reacts to the event when new user is created via Simple FB Connect.
   *
   */
  public function userCreated(GenericEvent $event) {
    $user = $event->getArgument('account');

    // Enter your own code here. Remember to save the user with $user->save()
    // if you modify the user object.
    //drupal_set_message("Debug: user created. This message is from mymodule!");
  }
  /**
   * Reacts to the event when user logs in via Simple FB Connect.
   *
   * This example adds role 'facebook' to the user if the user
   * didn't have that role already. You have to create the role manually.
   *
   * This function also demonstrates how you can get the access token for the
   * current user and how to make your own API calls using Facebook service.
   */
  public function userLogin(GenericEvent $event) {
    $user = $event->getArgument('account');

    // Enter your code here. Remember to save the user with $user->save()
    // if you modify the user object.
    //drupal_set_message("Debug: user logged in. This message is from mymodule!");

    // Let's add a role 'facebook' for the user if she didn't have it already.
    // The role itself must obviously be first created manually.
    $user->addRole('student');
    $user->save();

    // Let's see how we can get make our own API calls to Facebook. We need
    // user's Facebook access token, which SimpleFbConnect has stored to session
    // for other modules.
    $access_token = $this->persistentDataHandler->get('access_token');

    if ($access_token) {
      try {
        $graph_node = $this->facebook->get('/me?fields=name', $access_token)->getGraphNode();
        $name = $graph_node->getField('name');
      //  drupal_set_message("You probably knew this: user's name on Facebook is " . $name);
      }
      catch (FacebookRequestException $ex) {
        // Add exception handling here for FacebookRequestExceptions.
      }
      catch (FacebookSDKException $ex) {
        // Add exception handling here for all other exceptions.
      }
    }
    else {
      drupal_set_message("No FB access token found for current user!");
    }
  }

}
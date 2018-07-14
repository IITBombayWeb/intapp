<?php

namespace Drupal\iitinap_google\EventSubscriber;

use Drupal\social_auth\Event\SocialAuthUserEvent;
use Drupal\social_auth\Event\SocialAuthEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Reacts on Social Auth events.
 */
class SocialAuthSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   *
   * Returns an array of event names this subscriber wants to listen to.
   * For this case, we are going to subscribe for user creation and login
   * events and call the methods to react on these events.
   */
  public static function getSubscribedEvents() {
    $events[SocialAuthEvents::USER_CREATED] = ['onUserCreated'];
    $events[SocialAuthEvents::USER_LOGIN] = ['onUserLogin'];

    return $events;
  }
  
  /**
   * Alters the user name.
   *
   * @param \Drupal\social_auth\Event\SocialAuthUserEvent $event
   *   The Social Auth user event object.
   */
  public function onUserCreated(SocialAuthUserEvent $event) {
    /**
     * @var Drupal\user\UserInterface $user
     *
     * For all available methods, see User class
     * @see https://api.drupal.org/api/drupal/core!modules!user!src!Entity!User.php/class/User
     */
    $user = $event->getUser();
    $user->addRole('student');
    $user->save();


    // Adds a prefix with the implementer id on username.
   // $user->setUsername($event->getPluginId()) . ' ' . $user->getDisplayName())->save();
  }
  
  /**
   * Sets a drupal message when a user logs in.
   *
   * @param \Drupal\social_auth\Event\SocialAuthUserEvent $event
   *   The Social Auth user event object.
   */
  public function onUserLogin(SocialAuthUserEvent $event) {
    drupal_set_message('User has logged in with a Social Auth implementer. Implementer for ' . $event->getPluginId());
  }

}
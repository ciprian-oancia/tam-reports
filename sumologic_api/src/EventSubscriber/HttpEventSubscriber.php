<?php

/**
 * @file
 * Contains \Drupal\sumologic_api\EventSubscriber\HttpEventSubscriber.
 */

namespace Drupal\sumologic_api\EventSubscriber;

use Drupal\http_client_manager\HttpClientInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Guzzle\Common\Event;
use Guzzle\Plugin\Cookie\CookiePlugin;

use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;


/**
 * Event Subscriber HttpEventSubscriber.
 */
/**
 * Class HttpEventSubscriber.
 *
 * @package Drupal\sumologic_api
 */
class HttpEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
      $events['request.before_send'] = array('onRequestBeforeSend', -1000);
      return $events;
  }


  public function onRequestBeforeSend(Event $event) {
    $request = $event['request'];

    //if (!($request instanceof Request)) {
      //return;
    //}

    $api = reset($request->getHeader(HttpClientInterface::HEADER_API)->toArray());
    //$command = reset($request->getHeader(HttpClientInterface::HEADER_COMMAND)->toArray());
    if ($api != 'Sumologic Client') {
      return;
    }

    $cookiePlugin = new CookiePlugin(new ArrayCookieJar());
    $request->addSubscriber($cookiePlugin);
      $request->setAuth("sudXIMQtPyQeeI","QgS9ZapOrWO8wS2MDUY2SgZi8f8JfNrRRLOuoKOXYdbpt8pTO1bITySGQZRzZXDG");
      drupal_set_message($request);
      $request->getEventDispatcher()->addListener('request.error', function(Event $event) {
          $event->stopPropagation();
          if ($event['response']->getStatusCode() == 404) {

          }
      });
  }
}

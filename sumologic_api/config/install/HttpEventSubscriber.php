<?php

/**
 * @file
 * Contains \Drupal\sumologic_api\EventSubscriber\HttpEventSubscriber.
 */

namespace Drupal\sumologic_api\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\http_client_manager\HttpClientInterface;


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
    return array(
      'request.before_send' => array('onRequestBeforeSend', -1000)
    );
  }

  /**
   * Request before-send event handler
   *
   * @param Event $event
   *   Event received
   */
  public function onRequestBeforeSend(Event $event) {
    $request = $event['request'];
    if (!($request instanceof Request)) {
      return;
    }

    $api = reset($request->getHeader(HttpClientInterface::HEADER_API)->toArray());
    //$command = reset($request->getHeader(HttpClientInterface::HEADER_COMMAND)->toArray());
    if ($api != 'sumologic_api') {
      return;
    }
	$http_headers = array(
	  'Authorization: Basic '. base64_encode("sudXIMQtPyQeeI:QgS9ZapOrWO8wS2MDUY2SgZi8f8JfNrRRLOuoKOXYdbpt8pTO1bITySGQZRzZXDG"),
	  'Accept: application/json',
	  'Content-Type: application/json',
	);
    $request->setHeader($http_headers);

    print_r($request);
    exit();
  }
}

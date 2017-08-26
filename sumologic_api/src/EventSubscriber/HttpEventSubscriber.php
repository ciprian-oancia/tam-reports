<?php

/**
 * @file
 * Contains \Drupal\sumologic_api\EventSubscriber\HttpEventSubscriber.
 */

namespace Drupal\sumologic_api\EventSubscriber;
use GuzzleHttp\Cookie\SetCookie as CookieParser;
use Drupal\http_client_manager\HttpClientInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Guzzle\Common\Event;
use GuzzleHttp\Cookie\SessionCookieJar;
use Guzzle\Plugin\Cookie\CookiePlugin;



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


    /**
     * @param Event $event
     */
    public function onRequestBeforeSend(Event $event) {

        $request = $event['request'];

        $api = reset($request->getHeader(HttpClientInterface::HEADER_API)->toArray());

        if ($api != 'Sumologic Client') {
            return;
        }
        $cookiePlugin = new CookiePlugin(new SessionCookieJar('SESSION_STORAGE', true));
        $request->addSubscriber($cookiePlugin);
        //TODO: meh mih moh muh auth based on user key
        $request->setAuth("sudXIMQtPyQeeI","QgS9ZapOrWO8wS2MDUY2SgZi8f8JfNrRRLOuoKOXYdbpt8pTO1bITySGQZRzZXDG");
        $request->getEventDispatcher()->addListener('request.error', function(Event $event) {
            $event->stopPropagation();
            if ($event['response']->getStatusCode() == 404) {

            }
        });
    }

}

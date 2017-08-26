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
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\SessionCookieJar;
use Guzzle\Parser\Cookie\CookieParser;
use Guzzle\Plugin\Cookie\Cookie;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Guzzle\Plugin\Cookie\CookieJar\FileCookieJar;
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
        $events['request.sent']        = array('onRequestSent', -100);
        $events['request.before_send'] = array('onRequestBeforeSend', -1000);
        return $events;
    }

    /**
     * @param Event $event
     */
    public function onRequestSent(Event $event) {

        $request  = $event['request'];
        $response = $event['response'];
        $api      = reset($request->getHeader(HttpClientInterface::HEADER_API)->toArray());
        $command  = reset($request->getHeader(HttpClientInterface::HEADER_COMMAND)->toArray());

        $cookieParser = new CookieParser;
        $set_cookies=$response->getHeader('Set-Cookie');
        $response_cookies=$response->getHeaders();
        foreach ($set_cookies as $cookie)  {
            $cookie = $cookieParser->fromString($cookie);
            $cookies[$cookie->getName()] = $cookie->getValue();
        }
        drupal_set_message(serialize(array('set-response'=>array($Cookies),"cookies"=>array($response_cookies),"request"=>$request->getCookies())));

        if (($api === 'Sumologic Client') || ($command === 'addjob')) {
            $cid = 'sumologic_api_uid:' . \Drupal::currentUser()->id();
            \Drupal::cache()->set($cid,serialize($cookies));

        }


    }



    public function onRequestBeforeSend(Event $event) {

        // $cookiePlugin = new CookiePlugin(new FileCookieJar(file_directory_temp() . '/sumologic_api.txt'));
        $request = $event['request'];

        $api = reset($request->getHeader(HttpClientInterface::HEADER_API)->toArray());
        $command = reset($request->getHeader(HttpClientInterface::HEADER_COMMAND)->toArray());

        if ($api != 'Sumologic Client') {
            return;
        }
        $cookiePlugin = new CookiePlugin(new SessionCookieJar('SESSION_STORAGE', true));
        $request->addSubscriber($cookiePlugin);


        if ('addjob' != $command) {
            $cid = 'sumologic_api_cookie:' . \Drupal::currentUser()->id();
            if ($cache = \Drupal::cache()->get($cid)) {
                print_r(unserialize($cache->data));
                $cookies = unserialize($cache->data);
                foreach ($cookies as $name => $value)  {
                    $request->addCookie($name, $value);
                    print_r($name);
                }
            }
            drupal_set_message('dbf'. print_r($request->getCookies(),true));
        }
        $request->setAuth("sudXIMQtPyQeeI","QgS9ZapOrWO8wS2MDUY2SgZi8f8JfNrRRLOuoKOXYdbpt8pTO1bITySGQZRzZXDG");
        $request->getEventDispatcher()->addListener('request.error', function(Event $event) {
            $event->stopPropagation();
            if ($event['response']->getStatusCode() == 404) {

            }
        });
    }

    /**
     * @return array|mixed
     */
    public function sumologic_api_cookie($cookies = NULL) {
        $cid = 'sumologic_api_cookie:' . \Drupal::currentUser()->id();
        if ($cookies) {
            \Drupal::cache()->set($cid,serialize($cookies));
            print_r('to cache');
            return;
        } else if ($cache = \Drupal::cache()->get($cid)) {
            print_r('from cache');
            $cookie = unserialize($cache->data);

            return $cookies;
        }

        var_dump(debug_print_backtrace());
    }
}

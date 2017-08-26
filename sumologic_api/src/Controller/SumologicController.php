<?php

namespace Drupal\sumologic_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\http_client_manager\Entity\HttpConfigRequest;
use Drupal\http_client_manager\HttpClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class SumologicController.
 *
 * @package Drupal\sumologic_api\Controller
 */
class SumologicController extends ControllerBase {

  /**
   * Json description for the Http Client.
   *
   * @var \Drupal\http_client_manager\HttpClientInterface
   */
  protected $httpClient;

  /**
   * {@inheritdoc}
   */
  public function __construct(HttpClientInterface $http_client) {
    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('sumologic_api.http_client')
    );
  }

  /**
   * Get Client.
   *
   * @return \Drupal\http_client_manager\HttpClientInterface
   *   The Http Client instance.
   */
  public function getClient() {
    return $this->httpClient;
  }

  /**
   * All jobs.
   *
   * @return string
   *   The service response.
   */
  public function addJob() {
    $request = HttpConfigRequest::add('parameters');

    if ($request) {
      $response = '<pre>' . print_r($request->execute(), true) . '</pre>';
    }
    else {
      $response = $this->t('Unable to ad an Job to Sumologic');
    }

    return [
      '#type' => 'markup',
      '#markup' => $response,
    ];
  }


  /**
   * View results.
   *
   * @param int $jobId
   *   The job Id.
   *
   * @return string
   *   The service response.
   */
  public function viewResults($jobId) {
    $client = $this->getClient();
    $response = $client->viewResults(['jobId' => $jobId]);

    return [
      '#type' => 'markup',
      '#markup' => '<pre>' . print_r($response, true) . '</pre>',
    ];
  }


}


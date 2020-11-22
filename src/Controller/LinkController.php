<?php

namespace Drupal\editorjs\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\editorjs\Event\LinkFetchEvent;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Defines controller for link plugin.
 *
 * @see https://github.com/editor-js/link
 */
class LinkController implements ContainerInjectionInterface {

  /**
   * The request client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $client;

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * EditorjsController constructor.
   *
   * @param \GuzzleHttp\ClientInterface $client
   *   The request client.
   */
  public function __construct(ClientInterface $client, EventDispatcherInterface $dispatcher) {
    $this->client = $client;
    $this->dispatcher = $dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client'),
      $container->get('event_dispatcher'),
    );
  }

  /**
   * Returns meta data for editorJs Link plugin.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The meta data.
   */
  public function fetch(Request $request): JsonResponse {
    $url = $request->get('url');
    if (\is_null($url)) {
      throw new BadRequestHttpException();
    }

    $event = new LinkFetchEvent($url);
    $this->dispatcher->dispatch(LinkFetchEvent::NAME, $event);

    $result = [
      'success' => \count($event->meta()),
      'meta' => $event->meta(),
    ];

    return new JsonResponse($result);
  }

}

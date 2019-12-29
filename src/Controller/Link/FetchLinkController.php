<?php

namespace Drupal\editorjs\Controller\Link;

use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FetchLinkController extends ControllerBase {

  /**
   * @var \GuzzleHttp\ClientInterface
   */
  protected $client;

  /**
   * @var \DOMDocument
   */
  protected $doc;

  public function __construct(ClientInterface $client) {
    $this->client = $client;
  }

  public static function create(ContainerInterface $container) {
    return new static($container->get('http_client'));
  }

  public function fetch(Request $request) {
    $url = $request->get('url');
    if (is_null($url)) {
      throw new BadRequestHttpException();
    }

    try {
      $responce = $this->client->request('GET', $url);

      $this->doc = new \DOMDocument();
      $this->doc->loadHTML($responce->getBody()->getContents());

      $result = [
        'success' => 1,
        'meta' => [
          'title' => $this->getTitle(),
          'description' => $this->getDesc(),
          'image' => ['url' => $this->getImgUrl()],
        ],
      ];
    } catch (GuzzleException $e) {
      $result = [
        'success' => 0,
      ];
    }

    return new JsonResponse($result);
  }

  protected function getImgUrl() {
    $url = '';
    /** @var \DOMElement $meta_node */
    foreach ($this->getMeta() as $meta_node) {
      if ($meta_node->getAttribute('property') == 'og:image') {
        $url = $meta_node->getAttribute('content');
      }
    }
    if (empty($url)) {
      /** @var \DOMElement $node */
      foreach ($this->doc->getElementsByTagName('img') as $node) {
        $url = $node->getAttribute('src');
        break;
      }
    }

    return $url;
  }

  /**
   * Returns description.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup|string
   */
  protected function getDesc() {
    $desc = $this->t('Description not found');
    /** @var \DOMElement $meta_node */
    foreach ($this->getMeta() as $meta_node) {
      if ($meta_node->getAttribute('name') == 'description') {
        $desc = $meta_node->getAttribute('content');
      }
    }

    return $desc;
  }

  /**
   * Returns responce title.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup|string
   */
  protected function getTitle() {
    $node_title = $this->doc->getElementsByTagName('title');

    return $node_title->count() ? $node_title->item(0)->nodeValue : $this->t('Title not found');
  }

  /**
   * Returns Meta collection.
   *
   * @return \DOMNodeList|mixed
   */
  protected function getMeta() {
    $meta = &drupal_static('fetch_link:meta');
    if ($meta) {
      return $meta;
    }
    $meta = $this->doc->getElementsByTagName('meta');

    return $meta;
  }

}

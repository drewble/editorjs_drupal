<?php

namespace Drupal\editorjs\EventSubscriber;

use Drupal\Component\Utility\Html;
use Drupal\editorjs\Event\LinkFetchEvent;
use GuzzleHttp\ClientInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Editorjs event subscriber.
 */
class EditorjsSubscriber implements EventSubscriberInterface {

  /**
   * The http client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $client;

  /**
   * EditorjsSubscriber constructor.
   *
   * @param \GuzzleHttp\ClientInterface $client
   *   The http client.
   */
  public function __construct(ClientInterface $client) {
    $this->client = $client;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      LinkFetchEvent::NAME => 'linkFetch',
    ];
  }

  /**
   * Set meta data.
   *
   * @param \Drupal\editorjs\Event\LinkFetchEvent $event
   *   The link fetch event.
   */
  public function linkFetch(LinkFetchEvent $event): void {
    $responce = $this->client->request('GET', $event->url());
    $dom = Html::load($responce->getBody()->getContents());

    $metatags = $this->metatags($dom->getElementsByTagName('meta'));

    $desc = $metatags['og:description'] ?? $metatags['description'] ?? '';
    $title = $metatags['og:title'] ?? '';
    $img = $metatags['og:image'] ?? '';
    if (empty($title)) {
      $title_tag = $dom->getElementsByTagName('title');
      $title = $title_tag->count() ? $title_tag->item(0)->nodeValue : '';
    }
    if (empty($img)) {
      $tag_imgs = $dom->getElementsByTagName('img');
      $img = $tag_imgs->count() ? $tag_imgs->item(0)->getAttribute('src') : '';
      if (empty(\parse_url($img, \PHP_URL_HOST))) {
        $img = \trim($event->url(), '/') . $img;
      }
    }
    $event->setMeta([
      'title' => $title,
      'description' => $desc,
      'image' => [
        'url' => $img,
      ],
    ]);
  }

  /**
   * Returns metatags array bu metatag NodeList.
   *
   * @param \DOMNodeList $list
   *   The metatag NodeList.
   *
   * @return array
   *   The metatags array.
   */
  protected function metatags(\DOMNodeList $list): array {
    $metatags = [];
    foreach ($list as $item) {
      if ($item->hasAttribute('name')) {
        $metatags[$item->getAttribute('name')] = $item->getAttribute('content');
      }
      if ($item->hasAttribute('property')) {
        $metatags[$item->getAttribute('property')] = $item->getAttribute('content');
      }
    }
    return $metatags;
  }

}

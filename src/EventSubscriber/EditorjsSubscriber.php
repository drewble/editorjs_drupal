<?php

namespace Drupal\editorjs\EventSubscriber;

use Drupal\Component\Utility\DiffArray;
use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\editorjs\Event\EditorJsEvents;
use Drupal\editorjs\Event\FormSubmitEvent;
use Drupal\editorjs\Event\LinkFetchEvent;
use Drupal\file\Entity\File;
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
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * EditorjsSubscriber constructor.
   *
   * @param \GuzzleHttp\ClientInterface $client
   *   The http client.
   */
  public function __construct(ClientInterface $client, EntityTypeManager $entityTypeManager) {
    $this->client = $client;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      EditorJsEvents::LINK_FETCH => 'linkFetch',
      EditorJsEvents::FORM_SUBMIT => 'processDifferenceValues',
    ];
  }


  /**
   * Processing difference values.
   *
   * @param \Drupal\editorjs\Event\FormSubmitEvent $event
   *   The event instance.
   */
  public function processDifferenceValues(FormSubmitEvent $event) {
    $diff = DiffArray::diffAssocRecursive($event->getOriginValue(), $event->getNewValue());
    if (empty($diff)) {
      return;
    }

    foreach ($diff as $diff_item) {
      if (isset($diff_item['type']) && $diff_item['type'] === 'image') {
        $fid = $diff_item['data']['file']['id'] ?? NULL;
        // Skip if file id not found.
        if (empty($fid)) {
          return;
        }
        // Change status to temporary.
        /** @var \Drupal\file\Entity\File $file */
        $file = $this->entityTypeManager->getStorage('file')->load($fid);
        if ($file) {
          $file->setTemporary();
          $file->save();
        }
      }
    }

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

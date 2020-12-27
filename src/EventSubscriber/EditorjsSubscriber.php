<?php

namespace Drupal\editorjs\EventSubscriber;

use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\DiffArray;
use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\editorjs\Event\EditorJsEvents;
use Drupal\editorjs\Event\MassageValuesEvent;
use Drupal\editorjs\Event\LinkFetchEvent;
use Drupal\file\Entity\File;
use Drupal\file\FileUsage\FileUsageInterface;
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
   * The file usage service.
   *
   * @var \Drupal\file\FileUsage\FileUsageInterface
   */
  protected $fileUsage;

  /**
   * EditorjsSubscriber constructor.
   *
   * @param \GuzzleHttp\ClientInterface $client
   *   The http client.
   * @param \Drupal\Core\Entity\EntityTypeManager $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\file\FileUsage\FileUsageInterface $fileUsage
   *   The file usage service.
   */
  public function __construct(ClientInterface $client, EntityTypeManager $entityTypeManager, FileUsageInterface $fileUsage) {
    $this->client = $client;
    $this->entityTypeManager = $entityTypeManager;
    $this->fileUsage = $fileUsage;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      EditorJsEvents::LINK_FETCH => 'linkFetch',
      EditorJsEvents::MASSAGE_FORM_VALUES => 'processDifferenceValues',
    ];
  }

  /**
   * Processing difference values.
   *
   * @param \Drupal\editorjs\Event\MassageValuesEvent $event
   *   The event instance.
   */
  public function processDifferenceValues(MassageValuesEvent $event) {
    foreach ($event->getNewValues() as $delta => $item) {
      $origin_delta = $item['_original_delta'] ?? $delta;
      $value = Json::decode($item['value'] ?? '');
      $origin_value = $event->getOriginValueByDelta($origin_delta);
      $origin_value = Json::decode($origin_value ?? '');

      $diff = DiffArray::diffAssocRecursive($origin_value, $value);
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
          if ($file && $file->isPermanent()) {
            $this->fileUsage->delete($file, 'editorjs');
            $file->setTemporary();
            $file->save();
          }
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

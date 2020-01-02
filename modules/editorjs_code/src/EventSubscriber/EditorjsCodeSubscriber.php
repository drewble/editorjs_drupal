<?php

namespace Drupal\editorjs_code\EventSubscriber;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\editorjs\Event\EdirorJsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * editorjs_code event subscriber.
 */
class EditorjsCodeSubscriber implements EventSubscriberInterface {

  /**
   * @param \Drupal\editorjs\Event\EdirorJsEvent $event
   */
  public function onBuild(EdirorJsEvent $event) {
    if ($event->getType() != 'code') {
      return;
    }
    $build = $event->getBuild();
    $build['#attached']['library'][] = 'editorjs_code/highlight';
    $event->setBuild($build);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      EdirorJsEvent::BUILD => ['onBuild', -100],
    ];
  }

}

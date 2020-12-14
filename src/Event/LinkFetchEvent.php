<?php

namespace Drupal\editorjs\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Defines event fetch metadata for linkTool.
 */
class LinkFetchEvent extends Event {

  /**
   * The input url.
   *
   * @var string
   */
  protected $url;

  /**
   * The meta data response.
   *
   * @var array
   *
   * @see https://github.com/editor-js/link#backend-response-format-
   */
  protected $meta = [];

  /**
   * LinkFetchEvent constructor.
   *
   * @param string $url
   *   The input url.
   */
  public function __construct(string $url) {
    $this->url = $url;
  }

  /**
   * Returns input url.
   *
   * @return string
   *   The input url.
   */
  public function url(): string {
    return $this->url;
  }

  /**
   * Set new meta data.
   *
   * @param array $meta
   *   The new meta data.
   */
  public function setMeta(array $meta): void {
    $this->meta = $meta;
  }

  /**
   * Returns response meta data.
   *
   * @return array
   *   The meta data.
   */
  public function meta(): array {
    return $this->meta;
  }

}

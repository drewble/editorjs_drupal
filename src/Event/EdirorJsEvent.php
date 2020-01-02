<?php

namespace Drupal\editorjs\Event;

use Symfony\Component\EventDispatcher\Event;

class EdirorJsEvent extends Event {

  const BUILD = 'editorjs.build';

  /**
   * This raw block data.
   *
   * @var array
   */
  protected $block;

  /**
   * This renderable EditorJs element.
   *
   * @var array
   */
  protected $build;

  /**
   * EdirorJsEvent constructor.
   *
   * @param $block
   * @param $build
   */
  public function __construct($block, $build) {
    $this->block = $block;
    $this->build = $build;
  }

  /**
   * Return EditorJs tool type.
   *
   * @return string
   */
  public function getType() {
    return $this->block['type'];
  }

  /**
   * Returns source data from editor js tool.
   *
   * @return array
   */
  public function getSource() {
    return $this->block;
  }

  /**
   * Returns renderable for current EditorJS tool.
   *
   * @return array
   */
  public function getBuild() {
    return $this->build;
  }

  /**
   * Set new build renderable data for current EditorJs tool.
   *
   * @param $build
   */
  public function setBuild($build) {
    $this->build = $build;
  }

}

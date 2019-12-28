<?php

namespace Drupal\editorjs\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a EditorJsPlugin annotation object.
 *
 * @Annotation
 */
class EditorJsPlugin extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The EditorJs plugin tool settings.
   *
   * @var array
   */
  public $settings;

}

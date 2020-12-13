<?php

namespace Drupal\editorjs\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines editorjs_tools annotation object.
 *
 * @Annotation
 */
class EditorjsTools extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $title;

  /**
   * The description of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description;

  /**
   * The name javascript class/object implementing this a tool.
   *
   * @var string
   */
  public $implementer;

}

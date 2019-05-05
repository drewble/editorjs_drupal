<?php

namespace Drupal\editorjs\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines editorjs annotation object.
 *
 * @Annotation
 */
class Editorjs extends Plugin {

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
   * The paragraph bundle of the plugin.
   *
   * @var string
   */
  public $paragraph_type;

  /**
   * The Editorjs plugin type.
   *
   * @var string
   */
  public $plugin_type;

}

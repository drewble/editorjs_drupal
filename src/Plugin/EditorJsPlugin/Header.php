<?php

namespace Drupal\editorjs\Plugin\EditorJsPlugin;

use Drupal\Core\Annotation\Translation;
use Drupal\editorjs\Annotation\EditorJsPlugin;

/**
 * Defines Header plugin.
 *
 * @EditorJsPlugin(
 *   id = "header",
 *   settings = {
 *     "class" = "Header",
 *     "inlineToolbar" = true
 *   }
 * )
 */
class Header extends EditorJsPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'editorjs'). '/assets/plugins/header.js';
  }

}

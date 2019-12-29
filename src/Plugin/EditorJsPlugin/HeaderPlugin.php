<?php

namespace Drupal\editorjs\Plugin\EditorJsPlugin;

use Drupal\editorjs\Annotation\EditorJsPlugin;

/**
 * Defines Header plugin.
 *
 * @EditorJsPlugin(
 *   id = "header",
 *   template = "editor_js_header",
 *   settings = {
 *     "class" = "Header",
 *     "inlineToolbar" = true
 *   }
 * )
 */
class HeaderPlugin extends EditorJsPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'editorjs'). '/assets/js/plugins/header.js';
  }

}

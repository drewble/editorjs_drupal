<?php

namespace Drupal\editorjs\Plugin\EditorJsPlugin;

use Drupal\editorjs\Annotation\EditorJsPlugin;

/**
 * Defines Header plugin.
 *
 * @EditorJsPlugin(
 *   id = "list",
 *   template = "editor_js_list",
 *   settings = {
 *     "class" = "List",
 *     "inlineToolbar" = true
 *   }
 * )
 */
class ListPlugin extends EditorJsPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'editorjs'). '/assets/js/plugins/list.js';
  }

}

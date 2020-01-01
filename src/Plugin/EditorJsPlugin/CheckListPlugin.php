<?php

namespace Drupal\editorjs\Plugin\EditorJsPlugin;

use Drupal\editorjs\Annotation\EditorJsPlugin;

/**
 * Defines Checklist plugin.
 *
 * @EditorJsPlugin(
 *   id = "check_list",
 *   template = "editor_js_checklist",
 *   settings = {
 *     "class" = "Checklist",
 *     "inlineToolbar" = true
 *   }
 * )
 */
class CheckListPlugin extends EditorJsPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'editorjs'). '/assets/js/plugins/checklist.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries() {
    return ['editorjs/plugins.checklist'];
  }

}

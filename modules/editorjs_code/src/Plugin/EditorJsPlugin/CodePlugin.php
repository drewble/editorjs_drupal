<?php

namespace Drupal\editorjs_code\Plugin\EditorJsPlugin;

use Drupal\editorjs\Annotation\EditorJsPlugin;
use Drupal\editorjs\Plugin\EditorJsPlugin\EditorJsPluginBase;

/**
 * Defines Checklist plugin.
 *
 * @EditorJsPlugin(
 *   id = "code",
 *   template = "editor_js_code",
 *   settings = {
 *     "class" = "CodeTool",
 *     "inlineToolbar" = true
 *   }
 * )
 */
class CodePlugin extends EditorJsPluginBase {

  /**
   * @inheritDoc
   */
  public function getFile() {
    return drupal_get_path('module', 'editorjs_code') . '/assets/js/code.js';
  }

}

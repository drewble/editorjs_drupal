<?php

namespace Drupal\editorjs\Plugin\EditorJsPlugin;

use Drupal\editorjs\Annotation\EditorJsPlugin;

/**
 * Defines Header plugin.
 *
 * @EditorJsPlugin(
 *   id = "linkTool",
 *   template = "editor_js_link",
 *   settings = {
 *     "class" = "LinkTool",
 *     "config" = {
 *       "endpoint" = "/admin/editorjs/link/fetch",
 *     }
 *   }
 * )
 */
class LinkPlugin extends EditorJsPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'editorjs') . '/assets/js/plugins/linkTool.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries() {
    return ['editorjs/plugins.link'];
  }

}

<?php

namespace Drupal\editorjs\Plugin\EditorjsTools;

use Drupal\editorjs\EditorJsToolsPluginBase;

/**
 * Plugin implementation of the editorjs_tools.
 *
 * @EditorJsTools(
 *   id = "list",
 *   implementer = "List",
 *   label = @Translation("List"),
 *   description = @Translation("Provides list elements.")
 * )
 */
class ListTool extends EditorJsToolsPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return "/{$this->getModulePath('editorjs')}/assets/vendor/list.min.js";
  }

  /**
   * {@inheritdoc}
   */
  public function prepareSettings($settings) {
    return [
      'inlineToolbar' => (bool) $settings['inlineToolbar'],
    ];
  }

}

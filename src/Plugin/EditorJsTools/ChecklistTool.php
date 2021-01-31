<?php

namespace Drupal\editorjs\Plugin\EditorjsTools;

use Drupal\editorjs\EditorJsToolsPluginBase;

/**
 * Plugin implementation of the editorjs_tools.
 *
 * @EditorJsTools(
 *   id = "checklist",
 *   implementer = "Checklist",
 *   label = @Translation("Checklist"),
 *   description = @Translation("Provides checklist elements.")
 * )
 */
class ChecklistTool extends EditorJsToolsPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return "/libraries/editorjs_dependencies/editorjs/tools/checklist.min.js";
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

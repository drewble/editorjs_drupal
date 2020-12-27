<?php

namespace Drupal\editorjs\Plugin\EditorjsTools;

use Drupal\editorjs\EditorJsToolsPluginBase;

/**
 * Plugin implementation of the editorjs_tools.
 *
 * @EditorJsTools(
 *   id = "inline_code",
 *   implementer = "InlineCode",
 *   label = @Translation("Inline code"),
 *   description = @Translation("Provides inline code.")
 * )
 */
class InlineCode extends EditorJsToolsPluginBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $settings = []) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return '/libraries/editorjs--inline-code/dist/bundle.js';
  }

  /**
   * {@inheritdoc}
   */
  public function prepareSettings(array $settings) {
    return [];
  }

}

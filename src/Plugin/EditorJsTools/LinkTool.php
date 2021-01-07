<?php

namespace Drupal\editorjs\Plugin\EditorjsTools;

use Drupal\Core\Url;
use Drupal\editorjs\EditorJsToolsPluginBase;

/**
 * Plugin implementation of the editorjs_tools.
 *
 * @EditorJsTools(
 *   id = "linkTool",
 *   implementer = "LinkTool",
 *   label = @Translation("Link tool"),
 *   description = @Translation("Provides link tool."),
 *   permission = "allow image tool"
 * )
 */
class LinkTool extends EditorJsToolsPluginBase {

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
    return '/libraries/editorjs--link/dist/bundle.js';
  }

  /**
   * {@inheritdoc}
   */
  public function prepareSettings($settings) {
    return [
      'config' => [
        'endpoint' => Url::fromRoute('editorjs.link')->toString(),
      ],
    ];
  }

}

<?php

namespace Drupal\editorjs\Plugin\EditorjsTools;

use Drupal\editorjs\EditorJsToolsPluginBase;

/**
 * Plugin implementation of the editorjs_tools.
 *
 * @EditorJsTools(
 *   id = "linkTool",
 *   implementer = "LinkTool",
 *   label = @Translation("Link tool"),
 *   description = @Translation("Provides link tool.")
 * )
 */
class LinkTool extends EditorJsToolsPluginBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $settings = []) {
    return [
      'endpoint' => [
        '#type' => 'textfield',
        '#title' => $this->t('Endpoint'),
        '#description' => $this->t('Endpoint for fetch link metadata.'),
        '#default_value' => $settings['endpoint'] ?? '/admin/editorjs/link',
      ],
    ];
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
        'endpoint' => $settings['endpoint'],
      ],
    ];
  }

}

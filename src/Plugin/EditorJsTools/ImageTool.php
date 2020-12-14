<?php

namespace Drupal\editorjs\Plugin\EditorjsTools;

use Drupal\editorjs\EditorJsToolsPluginBase;
use Drupal\file\Entity\File;

/**
 * Plugin implementation of the editorjs_tools.
 *
 * @EditorJsTools(
 *   id = "image",
 *   implementer = "ImageTool",
 *   label = @Translation("Image"),
 *   description = @Translation("Provides image tool.")
 * )
 */
class ImageTool extends EditorJsToolsPluginBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $settings = []) {
    $elements = [];

    $elements['endpoints']['byFile'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Endpoint for upload'),
      '#description' => $this->t('Your backend file upload endpoint.'),
      '#default_value' => $settings['endpoints']['byFile'] ?? '/admin/editorjs/upload',
    ];

    $elements['endpoints']['byUrl'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Endpoint for upload by url'),
      '#description' => $this->t('Your endpoint that provides uploading by Url.'),
      '#default_value' => $settings['endpoints']['byUrl'] ?? '/admin/editorjs/fetch',
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return '/libraries/editorjs--image/dist/bundle.js';
  }

  /**
   * {@inheritdoc}
   */
  public function prepareSettings($settings) {
    return [
      'config' => [
        'endpoints' => $settings['endpoints'],
      ],
    ];
  }

}

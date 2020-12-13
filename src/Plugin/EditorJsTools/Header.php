<?php

namespace Drupal\editorjs\Plugin\EditorjsTools;

use Drupal\editorjs\EditorJsToolsPluginBase;

/**
 * Plugin implementation of the editorjs_tools.
 *
 * @EditorJsTools(
 *   id = "header",
 *   implementer = "Header",
 *   label = @Translation("Header (Beta)"),
 *   description = @Translation("Provides header elements.")
 * )
 */
class Header extends EditorJsToolsPluginBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $settings = []) {
    $elements = [];

    $elements['placeholder'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Placeholder'),
      '#default_value' => $settings['placeholder'] ?? $this->t('Enter a header'),
    ];

    $elements['levels'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Levels'),
      '#options' => [
        '1' => 'H1',
        '2' => 'H2',
        '3' => 'H3',
        '4' => 'H4',
        '5' => 'H5',
        '6' => 'H6',
      ],
      '#default_value' => $settings['levels'] ?? [2, 3, 4, 5],
    ];

    $elements['defaultLevel'] = [
      '#type' => 'number',
      '#title' => $this->t('Default level'),
      '#min' => 1,
      '#max' => 6,
      '#default_value' => $settings['defaultLevel'] ?? 2,
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return '/libraries/editorjs--header/dist/bundle.js';
  }

  /**
   * {@inheritdoc}
   */
  public function prepareSettings($settings) {
    return [
      'config' => [
        'placeholder' => $settings['placeholder'],
        'levels' => array_map('intval', array_values(array_filter($settings['levels']))),
        'defaultLevel' => (int) $settings['defaultLevel'],
      ],
    ];
  }

}

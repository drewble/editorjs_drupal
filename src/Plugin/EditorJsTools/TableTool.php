<?php

namespace Drupal\editorjs\Plugin\EditorjsTools;

use Drupal\editorjs\EditorJsToolsPluginBase;

/**
 * Plugin implementation of the editorjs_tools.
 *
 * @EditorJsTools(
 *   id = "table",
 *   implementer = "Table",
 *   label = @Translation("Table"),
 *   description = @Translation("Provides table elements.")
 * )
 */
class TableTool extends EditorJsToolsPluginBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $settings = []) {
    $elements = parent::settingsForm($settings);

    $elements['rows'] = [
      '#type' => 'number',
      '#title' => $this->t('Rows'),
      '#description' => $this->t('The default rows count.'),
      '#default_value' => $settings['rows'] ?? 2,
    ];

    $elements['cols'] = [
      '#type' => 'number',
      '#title' => $this->t('Cols'),
      '#description' => $this->t('The default cols count.'),
      '#default_value' => $settings['cols'] ?? 3,
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return "/{$this->getModulePath('editorjs')}/assets/vendor/table.min.js";
  }

  /**
   * {@inheritdoc}
   */
  public function prepareSettings($settings) {
    return [
      'inlineToolbar' => (bool) $settings['inlineToolbar'],
      'config' => [
        'rows' => (int) $settings['rows'],
        'cols' => (int) $settings['cols'],
      ],
    ];
  }

}

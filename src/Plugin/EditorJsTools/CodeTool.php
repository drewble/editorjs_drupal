<?php

namespace Drupal\editorjs\Plugin\EditorjsTools;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\editorjs\EditorJsToolsPluginBase;

/**
 * Plugin implementation of the editorjs_tools.
 *
 * @EditorJsTools(
 *   id = "code",
 *   implementer = "CodeTool",
 *   label = @Translation("Code"),
 *   description = @Translation("Provides code tool.")
 * )
 */
class CodeTool extends EditorJsToolsPluginBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(FieldDefinitionInterface $fieldDefinition, array $settings = []) {
    return [
      'placeholder' => [
        '#type' => 'textfield',
        '#title' => $this->t('Placeholder'),
        '#default_value' => $settings['placeholder'] ?? $this->t('Enter a code'),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return "/libraries/editorjs_dependencies/editorjs/tools/code.min.js";
  }

  /**
   * {@inheritdoc}
   */
  public function prepareSettings($settings) {
    return [
      'config' => [
        'placeholder' => $settings['placeholder'],
      ],
    ];
  }

}

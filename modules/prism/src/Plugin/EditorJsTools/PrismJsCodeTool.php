<?php

namespace Drupal\editorjs_prism\Plugin\EditorjsTools;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\editorjs\EditorJsToolsPluginBase;

/**
 * Plugin implementation of the editorjs_tools.
 *
 * @EditorJsTools(
 *   id = "prism_code",
 *   implementer = "CodeTool",
 *   label = @Translation("PrismJs code"),
 *   description = @Translation("Provides PrismJs code tool. Not compatible with default CodeTool plugins.")
 * )
 */
class PrismJsCodeTool extends EditorJsToolsPluginBase {

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
      'languages' => [
        '#type' => 'textarea',
        '#title' => $this->t('Languages'),
        '#description' => $this->t('Language list. Instance: value|Label'),
        '#default_value' => $settings['languages'] ?? "php | PHP\r\njs | JavaScript\r\ncss | CSS\r\ntwig | TWIG",
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return "/{$this->getModulePath('editorjs_prism')}/assets/code-lang.min.js";
  }

  /**
   * {@inheritdoc}
   */
  public function prepareSettings($settings) {
    $langs = str_replace(["\r\n", "\r"], "\n", $settings['languages'] ?? []);
    $languages = [];
    foreach (explode("\n", $langs) as $item) {
      $item = trim($item);
      if (empty($item)) {
        continue;
      }
      [$code, $label] = explode('|', $item);
      $languages[trim($code)] = trim($label);
    }
    return [
      'config' => [
        'placeholder' => $settings['placeholder'],
        'languages' => $languages,
      ],
    ];
  }

}

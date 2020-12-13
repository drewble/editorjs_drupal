<?php

namespace Drupal\editorjs\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\MapItem;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\file\Plugin\Field\FieldType\FileItem;

/**
 * Defines the 'editorjs' field type.
 *
 * @FieldType(
 *   id = "editorjs",
 *   label = @Translation("Editor JS"),
 *   category = @Translation("General"),
 *   default_widget = "editorjs",
 *   default_formatter = "string",
 *   cardinality = 1
 * )
 */
class EditorjsItem extends MapItem {

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return [
      'tools' => [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $elements['tools'] = [
      '#type' => 'container',
    ];
    $settings = $this->getSettings();

    /** @var \Drupal\editorjs\EditorJsToolsPluginManager $manager */
    $manager = \Drupal::service('plugin.manager.editorjs_tools');

    foreach (array_keys($manager->getDefinitions()) as $plugin_id) {
      $tool_settings = $settings['tools'][$plugin_id] ?? [];
      /** @var \Drupal\editorjs\EditorJsToolsInterface $instance */
      $instance = $manager->createInstance($plugin_id);
      $elements['tools'][$plugin_id]['enable'] = [
        '#type' => 'checkbox',
        '#title' => $instance->label(),
        '#description' => $instance->description(),
        '#default_value' => $tool_settings['enable'] ?? FALSE,
      ];
      $elements['tools'][$plugin_id]['settings'] = [
        '#type' => 'details',
        '#title' => $this->t('Settings'),
        '#states' => [
          'visible' => [
            ':input[name="settings[tools][' . $plugin_id . '][enable]"]' => ['checked' => TRUE],
          ],
        ],
      ];

      $settings_elements = $instance->settingsForm($tool_settings['settings'] ?? []);
      $elements['tools'][$plugin_id]['settings'] += $settings_elements;
      if (empty($settings_elements)) {
        $elements['tools'][$plugin_id]['settings']['#access'] = FALSE;
      }
    }
    //$element['image']['enable'] = [
    //  '#type' => 'checkbox',
    //  '#title' => $this->t('Image plugin'),
    //  '#default_value' => $settings['image']['enable'],
    //];
    //
    //$element['image']['file_extensions'] = [
    //  '#type' => 'textfield',
    //  '#title' => $this->t('Allowed file extensions'),
    //  '#default_value' => $settings['image']['file_extensions'],
    //  '#description' => $this->t('Separate extensions with a space or comma and do not include the leading dot.'),
    //  '#element_validate' => [[FileItem::class, 'validateExtensions']],
    //  '#weight' => 1,
    //  '#maxlength' => 256,
    //  '#states' => [
    //    'visible' => [
    //      ':input[name="settings[image][enable]"]' => ['checked' => TRUE],
    //    ],
    //    'required' => [
    //      ':input[name="settings[image][enable]"]' => ['checked' => TRUE],
    //    ],
    //  ],
    //];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function toArray() {
    return \unserialize($this->getValue()['value']);
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $values['value'] = 'generateSampleValue';
    return $values;
  }

}

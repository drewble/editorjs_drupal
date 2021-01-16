<?php

namespace Drupal\editorjs\Plugin\Field\FieldType;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'editorjs' field type.
 *
 * @FieldType(
 *   id = "editorjs",
 *   label = @Translation("Editor JS"),
 *   category = @Translation("General"),
 *   default_widget = "editorjs",
 *   default_formatter = "editorjs_default",
 *   cardinality = 1
 * )
 */
class EditorjsItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('Raw value'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'blob',
          'size' => 'big',
          'serialize' => TRUE,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    return [
      'value' => Json::encode([
        [
          'type' => 'paragraph',
          'data' => [
            'text' => 'This is Editor.js a Block-Styled editor.',
          ],
        ],
      ]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function postSave($update) {
    /** @var \Drupal\editorjs\EditorJsToolsPluginManager $manager */
    $manager = \Drupal::service('plugin.manager.editorjs_tools');
    $value = Json::decode($this->values['value']) ?? [];
    foreach ($value as $item) {
      if ($manager->hasDefinition($item['type'])) {
        /** @var \Drupal\editorjs\EditorJsToolsInterface $instance */
        $instance = $manager->createInstance($item['type']);
        $instance->postSave($item, $this->getEntity(), $update);
      }
    }
    return FALSE;
  }

}

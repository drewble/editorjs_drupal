<?php

namespace Drupal\editorjs\Plugin\Field\FieldType;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\MapItem;
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
class EditorjsItem extends MapItem {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('Encode value'));

    return $properties;
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
    return ['value' => ''];
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

  /**
   * {@inheritdoc}
   */
  public function __get($name) {
    if (!isset($this->values[$name])) {
      $this->values[$name] = '';
    }

    return $this->values[$name];
  }

}

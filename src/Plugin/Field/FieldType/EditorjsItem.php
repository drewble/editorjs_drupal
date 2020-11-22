<?php

namespace Drupal\editorjs\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\MapItem;

/**
 * Defines the 'editorjs' field type.
 *
 * @FieldType(
 *   id = "editorjs",
 *   label = @Translation("Editor JS"),
 *   category = @Translation("General"),
 *   default_widget = "editorjs",
 *   default_formatter = "string",
 *   cardinality=1
 * )
 */
class EditorjsItem extends MapItem {

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

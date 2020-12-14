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
 *   default_formatter = "editorjs_default",
 *   cardinality = 1
 * )
 */
class EditorjsItem extends MapItem {

    // @todo add settings to image tool plugin.
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

  /**
   * {@inheritdoc}
   */
  public function postSave($update) {
    parent::postSave($update);
  }

}

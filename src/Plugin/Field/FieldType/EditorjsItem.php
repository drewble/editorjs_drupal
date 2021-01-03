<?php

namespace Drupal\editorjs\Plugin\Field\FieldType;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\MapItem;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\file\Plugin\Field\FieldType\FileItem;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
    return ['value' => ''];
  }

  /**
   * {@inheritdoc}
   */
  public function postSave($update) {
    /** @var \Drupal\editorjs\EditorJsToolsPluginManager $manager */
    $manager = \Drupal::service('plugin.manager.editorjs_tools');
    $value = Json::decode($this->values['value'] ?? '');
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

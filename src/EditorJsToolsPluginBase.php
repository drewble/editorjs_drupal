<?php

namespace Drupal\editorjs;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\editorjs\Plugin\Field\FieldType\EditorjsItem;

/**
 * Base class for editorjs_tools plugins.
 */
abstract class EditorJsToolsPluginBase extends PluginBase implements EditorJsToolsInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function label() {
    // Cast the label to a string since it is a TranslatableMarkup object.
    return (string) $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function description() {
    return (string) $this->pluginDefinition['description'];
  }

  /**
   * {@inheritdoc}
   */
  public function implementer() {
    return (string) $this->pluginDefinition['implementer'];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $settings = []) {

    $elements['inlineToolbar'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Inline toolbar'),
      '#default_value' => $settings['inlineToolbar'] ?? FALSE,
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(array $value, FieldableEntityInterface $entity, $update) {}

}

<?php

namespace Drupal\editorjs;

use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\editorjs\Plugin\Field\FieldType\EditorjsItem;

/**
 * Interface for editorjs_tools plugins.
 */
interface EditorJsToolsInterface {

  /**
   * Returns the translated plugin label.
   *
   * @return string
   *   The translated title.
   */
  public function label();

  /**
   * Returns the translated plugin description.
   *
   * @return string
   *   The translated description.
   */
  public function description();

  /**
   * Returns javascript name class/object implements tool.
   *
   * @return string
   *   The translated description.
   */
  public function implementer();

  /**
   * Returns form elements for EditorJs toll settings.
   *
   * @param array $settings
   *   The saved settings.
   *
   * @return array
   *   The renderable form elements.
   */
  public function settingsForm(array $settings = []);

  /**
   * Returns the Drupal root-relative path to the file EditorJs tools.
   *
   * @return string
   *   The path to file EditorJs tools.
   */
  public function getFile();

  /**
   * Prepare settings before build.
   *
   * @param array $settings
   *   The saved settings.
   *
   * @return array
   *   The prepare settings contains keys 'config', 'inlineToolbar' or other.
   */
  public function prepareSettings(array $settings);

  /**
   * Defines post-save behavior.
   *
   * @param array $value
   *   The new value.
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   *   The entity that field belongs to.
   * @param bool $update
   *   Specifies whether the entity is being updated or created.
   *
   * @see \Drupal\editorjs\Plugin\Field\FieldType\EditorjsItem::postSave
   */
  public function postSave(array $value, FieldableEntityInterface $entity, $update);

}
<?php

namespace Drupal\editorjs;

use Drupal\Core\Form\FormStateInterface;

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
   * This method is called from massage form value.
   *
   * For instance this is the proper phase for remove deprecated entities.
   *
   * @param mixed $diff_value
   *   The difference value.
   *
   * @see \Drupal\editorjs\Plugin\Field\FieldWidget\EditorjsWidget::massageFormValues
   */
  public function processValueDifference($diff_value);

}

<?php

namespace Drupal\editorjs_media\Ajax;

use Drupal\Core\Ajax\CommandInterface;

/**
 * Provides an AJAX command for saving the contents of an editor dialog.
 *
 * This command is implemented in MODULE_DIR/assets/media-image.min.js in
 * Drupal.AjaxCommands.prototype.editorJsDialogSave.
 */
class EditorJsDialogSave implements CommandInterface {

  /**
   * An array of values that will be passed back to the editor by the dialog.
   *
   * @var mixed
   */
  protected $values;

  /**
   * Constructs an EditorDialogSave object.
   *
   * @param mixed $values
   *   The values that should be passed to the form constructor in Drupal.
   */
  public function __construct($values) {
    $this->values = $values;
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    return [
      'command' => 'editorJsDialogSave',
      'values' => $this->values,
    ];
  }

}

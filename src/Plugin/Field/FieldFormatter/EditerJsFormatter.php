<?php

namespace Drupal\editorjs\Plugin\Field\FieldFormatter;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'EditerJs' formatter.
 *
 * @FieldFormatter(
 *   id = "editorjs_default",
 *   label = @Translation("EditerJs"),
 *   field_types = {
 *     "editorjs"
 *   }
 * )
 */
class EditerJsFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#markup' => $item->value,
      ];
    }

    return $element;
  }

}

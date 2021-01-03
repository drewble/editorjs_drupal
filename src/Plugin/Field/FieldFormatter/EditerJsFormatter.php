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
        '#theme' => 'ce_blocks',
      ] + $this->prepareValue($item->value);
    }

    return $element;
  }

  /**
   * Prepare source value for render.
   *
   * @param string $value
   *   The source value.
   *
   * @return array
   *   Renderable structure.
   */
  public function prepareValue($value): array {
    $build = [];
    foreach (Json::decode($value) as $item) {
      $build[] = [
        '#theme' => 'ce_block__' . $item['type'],
        '#data' => $item,
      ];
    }
    return $build;
  }

}

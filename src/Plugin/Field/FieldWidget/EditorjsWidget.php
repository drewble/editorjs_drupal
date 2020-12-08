<?php

namespace Drupal\editorjs\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the 'editorjs' field widget.
 *
 * @FieldWidget(
 *   id = "editorjs",
 *   label = @Translation("EditorJs"),
 *   field_types = {"editorjs"},
 * )
 */
class EditorjsWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element['value'] = $element + [
      '#type' => 'hidden',
      '#default_value' => $items[$delta]->value ?? '',
      '#attached' => [
        'library' => ['editorjs/init'],
        'drupalSettings' => ['editorjs' => [$items->getName() => $items->getSettings()]],
      ],
      '#attributes' => [
        'class' => ['editorjs'],
      ],
    ];

    return $element;
  }

}

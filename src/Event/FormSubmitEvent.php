<?php

namespace Drupal\editorjs\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Defines event post form submit.
 *
 * @see \Drupal\editorjs\Plugin\Field\FieldWidget\EditorjsWidget::massageFormValues
 */
final class FormSubmitEvent extends Event {

  /**
   * The new form value.
   *
   * @var array
   */
  protected $newValue;

  /**
   * The origin form value.
   *
   * @var array
   */
  protected $originValue;

  /**
   * FormSubmitEvent constructor.
   *
   * @param array $newValue
   *   The new value.
   * @param array $originValue
   *   The origin value.
   */
  public function __construct(array $newValue, array $originValue) {
    $this->newValue = $newValue;
    $this->originValue = $originValue;
  }

  /**
   * Returns new value.
   *
   * @return array
   *   The new value.
   */
  public function getNewValue(): array {
    return $this->newValue;
  }

  /**
   * Returns origin value.
   *
   * @return array
   *   The origin value.
   */
  public function getOriginValue(): array {
    return $this->originValue;
  }

}

<?php

namespace Drupal\editorjs\Event;

use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Defines event post form submit.
 *
 * @see \Drupal\editorjs\Plugin\Field\FieldWidget\EditorjsWidget::massageFormValues
 */
final class MassageValuesEvent extends Event {

  /**
   * The new form value.
   *
   * @var array
   */
  protected $newValues;

  /**
   * The form renderable array.
   *
   * @var array
   */
  protected $form;

  /**
   * The form state.
   *
   * @var \Drupal\Core\Form\FormStateInterface
   */
  protected $formState;

  /**
   * The origin value key in storage.
   *
   * @var string
   */
  protected $originKey;

  /**
   * FormSubmitEvent constructor.
   *
   * @param array $newValues
   *   The new value.
   * @param array $form
   *   The form srtucture.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   * @param string $origin_key
   *   The origin value key in storage.
   */
  public function __construct(array $newValues, array $form, FormStateInterface $form_state, $origin_key) {
    $this->newValues = $newValues;
    $this->form = $form;
    $this->formState = $form_state;
    $this->originKey = $origin_key;
  }

  /**
   * Returns origin values from storage.
   *
   * @param string $delta
   *   The field delta.
   *
   * @return mixed
   *   The origin value.
   */
  public function getOriginValueByDelta($delta) {
    return $this->formState->get('origin:' . $this->originKey . ':' . $delta);
  }

  /**
   * Returns origin value key in storage.
   *
   * @return string
   *   The origin key.
   */
  public function getOriginKey(): string {
    return $this->originKey;
  }

  /**
   * Set new value.
   *
   * @param array $new_values
   *   The new values.
   */
  public function setNewValues(array $new_values): void {
    $this->newValues = $new_values;
  }

  /**
   * Returns new value.
   *
   * @return array
   *   The new value.
   */
  public function getNewValues(): array {
    return $this->newValues;
  }

  /**
   * Returns form structure.
   *
   * @return array
   *   The origin value.
   */
  public function getForm(): array {
    return $this->form;
  }

  /**
   * Returns form state.
   *
   * @return \Drupal\Core\Form\FormStateInterface
   *   The origin value.
   */
  public function getFormState(): FormStateInterface {
    return $this->formState;
  }

}

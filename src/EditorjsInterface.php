<?php

namespace Drupal\editorjs;

use Drupal\paragraphs\ParagraphInterface;

/**
 * Interface for editorjs plugins.
 */
interface EditorjsInterface {

  /**
   * Returns the translated plugin label.
   *
   * @return string
   *   The translated title.
   */
  public function label();

  public function getParagraphType();

  /**
   * Prepare paragraph data for Editorjs.
   *
   * @param \Drupal\paragraphs\ParagraphInterface $paragraph
   *
   * @return array
   */
  public function getData(ParagraphInterface $paragraph);

  public function createParagraph();

  public function setValues(ParagraphInterface $paragraph, $new_values);

}

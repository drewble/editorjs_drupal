<?php

namespace Drupal\editorjs\Plugin\Editorjs;

use Drupal\editorjs\EditorjsPluginBase;
use Drupal\paragraphs\ParagraphInterface;

/**
 * Plugin implementation of the editorjs.
 *
 * @Editorjs(
 *   id = "paragraph",
 *   label = @Translation("Paragraph"),
 *   description = @Translation("Default plugin for editorjs."),
 *   paragraph_type = "editorjs_default",
 *   plugin_type = "paragraph_default"
 * )
 */
class Paragraph extends EditorjsPluginBase {

  public function getData(ParagraphInterface $paragraph) {
    $output = parent::getData($paragraph);
    if (!$paragraph->get('editorjs_text_long')->isEmpty()) {
      $output['data']['text'] = $paragraph
        ->get('editorjs_text_long')
        ->first()
        ->getString();
    }
    return $output;
  }

  public function setValues(ParagraphInterface $paragraph, $new_values) {
    $paragraph
      ->set('editorjs_text_long', $new_values['text'])
      ->save();
  }

}

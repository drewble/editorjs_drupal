<?php

namespace Drupal\editorjs\Plugin\Editorjs;

use Drupal\editorjs\EditorjsPluginBase;
use Drupal\paragraphs\ParagraphInterface;

/**
 * Plugin implementation of the editorjs.
 *
 * @Editorjs(
 *   id = "list_element",
 *   label = @Translation("List element"),
 *   description = @Translation("List element plugin for editorjs."),
 *   paragraph_type = "editorjs_list",
 *   plugin_type = "list"
 * )
 */
class ListElement extends EditorjsPluginBase {

  public function getData(ParagraphInterface $paragraph) {
    $output = parent::getData($paragraph);
    if (!$paragraph->get('editorjs_list_style')->isEmpty()) {
      $output['data']['style'] = $paragraph
        ->get('editorjs_list_style')
        ->first()
        ->getString();
    }
    $output['data']['items'][] ='test list element';
    return $output;
  }

  public function setValues(ParagraphInterface $paragraph, $new_values) {
    $paragraph
      ->set('editorjs_list_style', $new_values['style'])
      ->save();
  }
}
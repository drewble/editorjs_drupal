<?php

namespace Drupal\editorjs\Plugin\Editorjs;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\editorjs\EditorjsPluginBase;
use Drupal\paragraphs\ParagraphInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the editorjs.
 *
 * @Editorjs(
 *   id = "title",
 *   label = @Translation("Title"),
 *   description = @Translation("Title plugin."),
 *   paragraph_type = "editorjs_title",
 *   plugin_type = "header"
 * )
 */
class Title extends EditorjsPluginBase {

  public function getData(ParagraphInterface $paragraph) {
    $output = parent::getData($paragraph);
    if (!$paragraph->get('editorjs_text_plain')->isEmpty()) {
      $output['data']['text'] = $paragraph
        ->get('editorjs_text_plain')
        ->first()
        ->getString();
    }
    if (!$paragraph->get('editorjs_title_level')->isEmpty()) {
      $output['data']['level'] = $paragraph
        ->get('editorjs_title_level')
        ->first()
        ->getString();
    }
    return $output;
  }

  public function createParagraph() {
    return $this
      ->entityTypeManager
      ->getStorage('paragraph')
      ->create([
        'type' => $this->getParagraphType(),
      ]);
  }

  public function setValues(ParagraphInterface $paragraph, $new_values) {
    $paragraph
      ->set('editorjs_text_plain', $new_values['text'])
      ->set('editorjs_title_level', $new_values['level'])
      ->save();
  }

}

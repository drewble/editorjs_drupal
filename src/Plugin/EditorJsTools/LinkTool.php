<?php

namespace Drupal\editorjs\Plugin\EditorjsTools;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Url;
use Drupal\editorjs\EditorJsToolsPluginBase;

/**
 * Plugin implementation of the editorjs_tools.
 *
 * @EditorJsTools(
 *   id = "linkTool",
 *   implementer = "LinkTool",
 *   label = @Translation("Link tool"),
 *   description = @Translation("Provides link tool."),
 *   permission = "allow image tool"
 * )
 */
class LinkTool extends EditorJsToolsPluginBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(FieldDefinitionInterface $fieldDefinition, array $settings = []) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return "/libraries/editorjs_dependencies/editorjs/tools/link.min.js";
  }

  /**
   * {@inheritdoc}
   */
  public function prepareSettings($settings) {
    return [
      'config' => [
        'endpoint' => Url::fromRoute('editorjs.link')->toString(),
      ],
    ];
  }

}

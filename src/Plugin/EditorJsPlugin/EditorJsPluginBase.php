<?php

namespace Drupal\editorjs\Plugin\EditorJsPlugin;

use Drupal\Core\Plugin\PluginBase;

abstract class EditorJsPluginBase extends PluginBase implements EditorJsPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function getSettings() {
    return $this->pluginDefinition['settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function build($data) {
    if ($template = $this->getTemplate()) {
      return [
        '#theme' => $template,
        '#data' => $data,
      ];
    }
    return [];
  }

  /**
   * Returns plugin template name or false.
   *
   * @return bool|mixed
   */
  public function getTemplate() {
    return isset($this->pluginDefinition['template']) ? $this->pluginDefinition['template'] : FALSE;
  }

}

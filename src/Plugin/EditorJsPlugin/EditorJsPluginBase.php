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

}

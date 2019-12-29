<?php

namespace Drupal\editorjs\Plugin\EditorJsPlugin;

interface EditorJsPluginInterface {

  /**
   * Returns EditorJs tool settings.
   *
   * @return string
   */
  public function getSettings();

  /**
   * This path to plugin.
   *
   * @return string
   */
  public function getFile();

  /**
   * Returns build renderable.
   *
   * @return array
   */
  public function build($data);

  /**
   * Returns plugin template name or false.
   *
   * @return bool|mixed
   */
  public function getTemplate();

  /**
   * Returns dependency libraries.
   *
   * @return array
   */
  public function getLibraries();
}

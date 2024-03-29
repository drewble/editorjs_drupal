<?php

namespace Drupal\editorjs;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides manager for editorjs tools.
 */
class EditorJsToolsPluginManager extends DefaultPluginManager {

  /**
   * Constructs EditorjsToolsPluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/EditorJsTools',
      $namespaces,
      $module_handler,
      'Drupal\editorjs\EditorJsToolsInterface',
      'Drupal\editorjs\Annotation\EditorJsTools'
    );
    $this->alterInfo('editorjs_tools_info');
    $this->setCacheBackend($cache_backend, 'editorjs_tools_plugins');
  }

}

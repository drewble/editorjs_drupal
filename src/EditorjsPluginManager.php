<?php

namespace Drupal\editorjs;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Editorjs plugin manager.
 */
class EditorjsPluginManager extends DefaultPluginManager {

  /**
   * Constructs EditorjsPluginManager object.
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
      'Plugin/Editorjs',
      $namespaces,
      $module_handler,
      'Drupal\editorjs\EditorjsInterface',
      'Drupal\editorjs\Annotation\Editorjs'
    );
    //@todo Disable hook?
    //$this->alterInfo('editorjs_info');
    $this->setCacheBackend($cache_backend, 'editorjs_plugins');
  }

  /**
   * Return plugin instance if found plugin by paragraph bundle.
   *
   * @param string $paragraph_type
   * @return bool|\Drupal\editorjs\EditorjsInterface|object
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function getInstanceByBundle(string $paragraph_type) {
    /** @var \Drupal\editorjs\EditorjsInterface $definition */
    foreach ($this->getDefinitions() as $definition) {
      if ($definition['paragraph_type'] === $paragraph_type) {
        return $this->createInstance($definition['id']);
      }
    }
    return FALSE;
  }

  /**
   * Return plugin instance if found plugin by editorjs plugin type.
   *
   * @param string $plugin_type
   * @return bool|object|\Drupal\editorjs\EditorjsInterface
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function getInstanceByPluginType(string $plugin_type) {
    /** @var \Drupal\editorjs\EditorjsInterface $definition */
    foreach ($this->getDefinitions() as $definition) {
      if ($definition['plugin_type'] === $plugin_type) {
        return $this->createInstance($definition['id']);
      }
    }
    return FALSE;
  }

}

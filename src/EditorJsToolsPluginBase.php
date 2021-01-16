<?php

namespace Drupal\editorjs;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for editorjs_tools plugins.
 */
abstract class EditorJsToolsPluginBase extends PluginBase implements EditorJsToolsInterface, ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * The current account proxy.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $accountProxy;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->accountProxy = $container->get('current_user');
    $instance->moduleHandler = $container->get('module_handler');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    // Cast the label to a string since it is a TranslatableMarkup object.
    return (string) $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function description() {
    return (string) $this->pluginDefinition['description'];
  }

  /**
   * {@inheritdoc}
   */
  public function implementer() {
    return (string) $this->pluginDefinition['implementer'];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $settings = []) {

    $elements['inlineToolbar'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Inline toolbar'),
      '#default_value' => $settings['inlineToolbar'] ?? FALSE,
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(array $value, FieldableEntityInterface $entity, $update) {}

  /**
   * {@inheritdoc}
   */
  public function allowed(): AccessResult {
    if (empty($this->pluginDefinition['permission'])) {
      return AccessResult::allowed();
    }
    return AccessResult::allowedIfHasPermission($this->accountProxy, $this->pluginDefinition['permission']);
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(): array {
    return [];
  }

  /**
   * Returns path to module.
   *
   * @param string $moduleName
   *   The module name.
   *
   * @return string
   *   The path to module.
   */
  protected function getModulePath($moduleName) {
    return $this->moduleHandler->getModule($moduleName)->getPath();
  }

}

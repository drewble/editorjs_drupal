<?php

namespace Drupal\editorjs\Plugin\Editor;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\editor\Entity\Editor;
use Drupal\editor\Plugin\EditorBase;
use Drupal\editorjs\EditorJsPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a CKEditor-based text editor for Drupal.
 *
 * @Editor(
 *   id = "editorjs",
 *   label = @Translation("EditorJs"),
 *   supports_content_filtering = FALSE,
 *   supports_inline_editing = TRUE,
 *   is_xss_safe = FALSE,
 *   supported_element_types = {
 *     "textarea"
 *   }
 * )
 */
class EditorJs extends EditorBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\editorjs\EditorJsPluginManager
   */
  protected $editorJsManager;

  /**
   * EditorJs constructor.
   *
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param \Drupal\editorjs\EditorJsPluginManager $editorJsManager
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EditorJsPluginManager $editorJsManager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->editorJsManager = $editorJsManager;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   *
   * @return \Drupal\Core\Plugin\ContainerFactoryPluginInterface|static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.editorjs_plugin')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultSettings() {
    return [
      'tools' => [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $editor = $form_state->get('editor');
    $settings = $editor->getSettings();
    $form['tools'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Tools'),
      '#default_value' => $settings['tools'],
      '#options' => $this->getOptions(),
    ];

    return $form;
  }

  /**
   * @inheritDoc
   */
  public function getJSSettings(Editor $editor) {
    $settings = [
      'plugins' => [],
      'tools' => [],
    ];
    $tools = isset($editor->getSettings()['tools']) ? $editor->getSettings()['tools'] : [];
    foreach ($this->editorJsManager->getDefinitions() as $plugin_id => $def) {
      if (in_array($plugin_id, $tools)) {
        /** @var \Drupal\editorjs\Plugin\EditorjsPlugin\EditorJsPluginInterface $instance */
        $instance = $this->editorJsManager->createInstance($plugin_id);
        $settings['plugins'][] = $instance->getFile();
        $settings['tools'][$plugin_id] = $instance->getSettings();
      }
    }

    return $settings;
  }

  /**
   * @inheritDoc
   */
  public function getLibraries(Editor $editor) {
    return ['editorjs/init'];
  }

  /**
   * Returns EditorJs plugins.
   *
   * @return array
   */
  protected function getOptions() {
    $options = [];
    foreach ($this->editorJsManager->getDefinitions() as $plugin_id => $def) {
      if (isset($def['settings']['class'])) {
        $options[$plugin_id] = $def['settings']['class'];
      }
    }

    return $options;
  }

}

<?php

namespace Drupal\editorjs\Plugin\Filter;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\editorjs\EditorJsPluginManager;
use Drupal\filter\Annotation\Filter;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'EditorJs Filter' filter.
 *
 * @Filter(
 *   id = "editorjs",
 *   title = @Translation("EditorJs Filter"),
 *   description = @Translation("Convert EditorJs data to HTML."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 *   weight = -100
 * )
 */
class EditorJsFilter extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * @var \Drupal\editorjs\EditorJsPluginManager
   */
  protected $pluginManager;

  /**
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * EditorJsFilter constructor.
   *
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param \Drupal\Core\Render\RendererInterface $renderer
   * @param \Drupal\editorjs\EditorJsPluginManager $pluginManager
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    RendererInterface $renderer,
    EditorJsPluginManager $pluginManager,
    LoggerInterface $logger
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->renderer = $renderer;
    $this->pluginManager = $pluginManager;
    $this->logger = $logger;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('renderer'),
      $container->get('plugin.manager.editorjs_plugin'),
      $container->get('logger.factory')->get('EditorJs')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $output = [];
    $blocks = Json::decode($text);
    foreach ($blocks as $block) {
      if ($block['type'] == 'paragraph') {
        $output[] = [
          '#theme' => 'editor_js_paragraph',
          '#data' => $block['data'],
        ];
        continue;
      }

      if (!$this->pluginManager->hasDefinition($block['type'])) {
        $this->logger->error('Not found EditorJs plugin: @plugin', ['@plugin' => $block['type']]);
        continue;
      }

      /** @var \Drupal\editorjs\Plugin\EditorJsPlugin\EditorJsPluginInterface $instance */
      $instance = $this->pluginManager->createInstance($block['type']);
      $output[] = $instance->build($block['data']);
    }
    return new FilterProcessResult($this->renderer->render($output));
  }

}

<?php

namespace Drupal\editorjs\Plugin\Filter;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\editorjs\EditorJsPluginManager;
use Drupal\editorjs\Event\EdirorJsEvent;
use Drupal\filter\Annotation\Filter;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

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
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
   * @param \Drupal\Core\Render\RendererInterface $renderer
   * @param \Drupal\editorjs\EditorJsPluginManager $pluginManager
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EventDispatcherInterface $eventDispatcher,
    RendererInterface $renderer,
    EditorJsPluginManager $pluginManager,
    LoggerInterface $logger
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->eventDispatcher = $eventDispatcher;
    $this->renderer = $renderer;
    $this->pluginManager = $pluginManager;
    $this->logger = $logger;
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
      $container->get('event_dispatcher'),
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
    if (!is_array($blocks)) {
      return new FilterProcessResult($text);
    }
    foreach ($blocks as $block) {
      if ($block['type'] == 'paragraph') {
        $output[] = $this
          ->eventDispatchBuild($block, [
            '#theme' => 'editor_js_paragraph',
            '#data' => $block['data'],
          ]);
      }

      if (!$this->pluginManager->hasDefinition($block['type'])) {
        $this->logger->error('Not found EditorJs plugin: @plugin', ['@plugin' => $block['type']]);
        continue;
      }

      /** @var \Drupal\editorjs\Plugin\EditorJsPlugin\EditorJsPluginInterface $instance */
      $instance = $this->pluginManager->createInstance($block['type']);
      $output[] = $this->eventDispatchBuild($block, $instance->build($block['data']));
    }

    return new FilterProcessResult($this->renderer->render($output));
  }

  protected function eventDispatchBuild($block, $build) {
    return ($this
      ->eventDispatcher
      ->dispatch(EdirorJsEvent::BUILD, new EdirorJsEvent($block, $build)))
      ->getBuild();
  }

}

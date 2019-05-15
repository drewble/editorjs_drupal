<?php

namespace Drupal\editorjs;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\paragraphs\ParagraphInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for editorjs plugins.
 */
abstract class EditorjsPluginBase extends PluginBase implements EditorjsInterface, ContainerFactoryPluginInterface {

  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManager $entityTypeManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    // Cast the label to a string since it is a TranslatableMarkup object.
    return (string) $this->pluginDefinition['label'];
  }

  public function getParagraphType() {
    return (string) $this->pluginDefinition['paragraph_type'];
  }

  public function getPluginType() {
    return (string) $this->pluginDefinition['plugin_type'];
  }

  public function createParagraph() {
    return $this
      ->entityTypeManager
      ->getStorage('paragraph')
      ->create([
        'type' => $this->getParagraphType(),
      ]);
  }

  public function getData(ParagraphInterface $paragraph) {
    return [
      'type' => $this->getPluginType(),
      'data' => [
        'pid' => $paragraph->id(),
      ],
    ];
  }

}

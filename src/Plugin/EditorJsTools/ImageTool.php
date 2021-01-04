<?php

namespace Drupal\editorjs\Plugin\EditorjsTools;

use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\editorjs\EditorJsToolsPluginBase;
use Drupal\file\Plugin\Field\FieldType\FileItem;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the editorjs_tools.
 *
 * @EditorJsTools(
 *   id = "image",
 *   implementer = "ImageTool",
 *   label = @Translation("Image"),
 *   description = @Translation("Provides image tool.")
 * )
 */
class ImageTool extends EditorJsToolsPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityRepository|object|null
   */
  protected $entityRepository;

  /**
   * The file usage backend.
   *
   * @var \Drupal\file\FileUsage\DatabaseFileUsageBackend|object|null
   */
  protected $fileUsage;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->entityRepository = $container->get('entity.repository');
    $instance->fileUsage = $container->get('file.usage');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $settings = []) {
    $elements = [];

    $elements['headers']['allow-extensions'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Allowed file extensions'),
      '#default_value' => $settings['headers']['allow-extensions'] ?? 'png gif jpg jpeg',
      '#description' => $this->t('Separate extensions with a space or comma and do not include the leading dot.'),
      '#element_validate' => [[FileItem::class, 'validateExtensions']],
    ];

    $elements['endpoints']['byFile'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Endpoint for upload'),
      '#description' => $this->t('Your backend file upload endpoint.'),
      '#default_value' => $settings['endpoints']['byFile'] ?? '/admin/editorjs/upload',
    ];

    $elements['endpoints']['byUrl'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Endpoint for upload by url'),
      '#description' => $this->t('Your endpoint that provides uploading by Url.'),
      '#default_value' => $settings['endpoints']['byUrl'] ?? '/admin/editorjs/upload_url',
    ];

    $elements['endpoints']['fetchStyleUrl'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Fetch image style url'),
      '#description' => $this->t('Endpoint for get image style url.'),
      '#default_value' => $settings['endpoints']['fetchStyleUrl'] ?? '/admin/editorjs/style_url',
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return '/libraries/editorjs-dimage/dist/bundle.js';
  }

  /**
   * {@inheritdoc}
   */
  public function prepareSettings($settings) {
    $output['config'] = [
      'image_styles' => image_style_options(),
    ];
    $output['config']['additionalRequestHeaders'] = $settings['headers'];
    $output['config']['endpoints'] = $settings['endpoints'];
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(array $value, FieldableEntityInterface $entity, $update) {
    $uuid = $value['data']['file']['uuid'] ?? NULL;
    // Skip if file id not found.
    if (empty($uuid)) {
      return;
    }

    /** @var \Drupal\file\Entity\File $file */
    $file = $this->entityRepository->loadEntityByUuid('file', $uuid);
    // Setting status to permanent and add to file usage.
    if ($file) {
      $usage_list = $this->fileUsage->listUsage($file);
      if (!isset($usage_list['editorjs'][$entity->getEntityTypeId()][$entity->id()])) {
        $this->fileUsage->add($file, 'editorjs', $entity->getEntityTypeId(), $entity->id());
      }
      if ($file->isTemporary()) {
        $file->setPermanent();
        $file->save();
      }
    }
  }

}

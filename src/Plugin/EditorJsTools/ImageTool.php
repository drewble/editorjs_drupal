<?php

namespace Drupal\editorjs\Plugin\EditorjsTools;

use Drupal\Core\Access\CsrfRequestHeaderAccessCheck;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
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
 *   description = @Translation("Provides image tool."),
 *   permission = "allow image tool"
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
   * The module manager.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The CSRF token generator.
   *
   * @var \Drupal\Core\Access\CsrfTokenGenerator
   */
  protected $tokenGenerator;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->entityRepository = $container->get('entity.repository');
    $instance->fileUsage = $container->get('file.usage');
    $instance->moduleHandler = $container->get('module_handler');
    $instance->tokenGenerator = $container->get('csrf_token');
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
    if ($this->moduleHandler->moduleExists('image')) {
      $output['config'] = [
        'image_styles' => image_style_options(),
      ];
    }
    $settings['headers']['X-CSRF-Token'] = $this->tokenGenerator->get(CsrfRequestHeaderAccessCheck::TOKEN_KEY);
    $output['config']['additionalRequestHeaders'] = $settings['headers'];
    $output['config']['endpoints'] = $this->getEndpoints();
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

  /**
   * Returns endpoints.
   *
   * @return array
   *   The endpoints.
   */
  protected function getEndpoints(): array {
    return [
      'byFile' => Url::fromRoute('editorjs.image.upload')->toString(),
      'byUrl' => Url::fromRoute('editorjs.image.upload_url')->toString(),
      'fetchStyleUrl' => Url::fromRoute('editorjs.image.style_url')->toString(),
    ];
  }

}

<?php

namespace Drupal\editorjs_media\Plugin\EditorjsTools;

use Drupal\Core\Url;
use Drupal\editorjs\EditorJsToolsPluginBase;
use Drupal\media_library\MediaLibraryState;
use Drupal\media_library\MediaLibraryUiBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines Media image tool.
 *
 * @EditorJsTools(
 *   id = "media_image",
 *   implementer = "MediaImage",
 *   label = @Translation("Media Image"),
 *   description = @Translation("Provides Media image tool."),
 *   permission = "allow media image tool"
 * )
 */
class MediaImageTool extends EditorJsToolsPluginBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity display repository.
   *
   * @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface
   */
  protected $entityDisplayRepository;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->entityDisplayRepository = $container->get('entity_display.repository');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $settings = []) {
    $elements['placeholder'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Placeholder'),
      '#default_value' => $settings['placeholder'] ?? $this->t('Enter a caption'),
    ];

    $media_types = $this->entityTypeManager->getStorage('media_type')->loadByProperties([
      'source' => 'image',
    ]);
    $option_types = [];
    foreach ($media_types as $id => $media_type) {
      $option_types[$id] = $media_type->label();
    }
    $elements['media_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Media types selectable in the Media Library'),
      '#default_value' => $settings['media_types'] ?? [],
      '#options' => $option_types,
      '#required' => TRUE,
    ];

    $view_mode_options = $this->entityDisplayRepository->getViewModeOptions('media');

    $elements['view_mode'] = [
      '#type' => 'select',
      '#options' => $view_mode_options,
      '#title' => $this->t('Default view mode'),
      '#default_value' => $settings['view_mode'] ?? 'default',
      '#description' => $this->t('The view mode that an embedded media item should be displayed in by default. This can be overridden using the <code>data-view-mode</code> attribute.'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return '/libraries/editorjs-media-image/dist/bundle.js';
  }

  /**
   * {@inheritdoc}
   */
  public function prepareSettings(array $settings) {
    if (empty($settings['media_types'])) {
      throw new \LogicException("The allowed media types empty.");
    }

    $state = MediaLibraryState::create(
      'editorjs_media.opener',
      $settings['media_types'],
      current($settings['media_types']),
      1
    );
    return [
      'config' => [
        'placeholder' => $settings['placeholder'],
        'view_mode' => $settings['view_mode'] ?? 'default',
        'DrupalMediaLibrary_url' => Url::fromRoute('media_library.ui')
          ->setOption('query', $state->all())
          ->toString(TRUE)
          ->getGeneratedUrl(),
        'DrupalMediaLibrary_dialogOptions' => MediaLibraryUiBuilder::dialogOptions(),
      ],
    ];
  }

}

<?php

namespace Drupal\editorjs_media\Plugin\EditorjsTools;

use Drupal\Core\Access\CsrfRequestHeaderAccessCheck;
use Drupal\Core\Field\FieldDefinitionInterface;
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
   * The module manager.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->entityDisplayRepository = $container->get('entity_display.repository');
    $instance->moduleHandler = $container->get('module_handler');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(FieldDefinitionInterface $fieldDefinition, array $settings = []) {
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
    $default_value = $settings['media_types'] ?? [];
    if (empty($default_value) && !empty($option_types)) {
      reset($option_types);
      $default_value = [key($option_types)];
    }
    $elements['media_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Media types selectable in the Media Library'),
      '#default_value' => $default_value,
      '#options' => $option_types,
      '#required' => TRUE,
    ];

    $view_mode_options = [
      '' => $this->t('None'),
    ] + $this->entityDisplayRepository->getViewModeOptions('media');

    $elements['view_mode'] = [
      '#type' => 'select',
      '#options' => $view_mode_options,
      '#title' => $this->t('Default view mode'),
      '#default_value' => $settings['view_mode'] ?? '',
      '#description' => $this->t('The view mode that an embedded media item should be displayed in by default. This can be overridden using the <code>data-view-mode</code> attribute.'),
    ];

    $elements['info'] = [
      '#type' => 'container',
      '#states' => [
        'visible' => [
          "[name=\"fields[{$fieldDefinition->getName()}][settings_edit_form][settings][tools][media_image][settings][view_mode]\"]" => ['value' => ''],
        ],
      ],
    ];

    $elements['info'][] = [
      '#markup' => $this->t('Adds feature for choose image style.'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return "/{$this->getModulePath('editorjs_media')}/assets/media-image.min.js";
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
    $output['config'] = [
      'placeholder' => $settings['placeholder'],
      'view_mode' => $settings['view_mode'] ?? '',
      'endpoints' => [
        'token' => \Drupal::csrfToken()->get(CsrfRequestHeaderAccessCheck::TOKEN_KEY),
        'fetchStyleUrl' => Url::fromRoute('editorjs.image.style_url')->toString(),
      ],
      'DrupalMediaLibrary_url' => Url::fromRoute('media_library.ui')
        ->setOption('query', $state->all())
        ->toString(TRUE)
        ->getGeneratedUrl(),
      'DrupalMediaLibrary_dialogOptions' => MediaLibraryUiBuilder::dialogOptions(),
    ];
    if ($this->moduleHandler->moduleExists('image') && empty($settings['view_mode'])) {
      $output['config']['image_styles'] = image_style_options();
    }

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(): array {
    return [
      'core/jquery',
      'core/drupal',
      'core/drupal.ajax',
    ];
  }

}

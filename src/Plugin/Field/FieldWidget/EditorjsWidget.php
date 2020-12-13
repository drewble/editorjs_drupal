<?php

namespace Drupal\editorjs\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the 'editorjs' field widget.
 *
 * @FieldWidget(
 *   id = "editorjs",
 *   label = @Translation("EditorJs"),
 *   field_types = {"editorjs"},
 * )
 */
class EditorjsWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * The editorJs tools manager.
   *
   * @var \Drupal\editorjs\EditorJsToolsPluginManager
   */
  protected $toolsManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->toolsManager = $container->get('plugin.manager.editorjs_tools');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $settings = $this->prepareSettings($items->getSettings());

    $element['value'] = $element + [
      '#type' => 'hidden',
      '#default_value' => $items[$delta]->value ?? '',
      '#attached' => [
        'library' => ['editorjs/init'],
        'drupalSettings' => ['editorjs' => [$items->getName() => $settings]],
      ],
      '#attributes' => [
        'class' => ['editorjs'],
        'data-field-name' => $items->getName(),
      ],
    ];
    return $element;
  }

  /**
   * Prepare settings tools for init EditorJs.
   *
   * @param array $settings
   *   The source saved settings.
   *
   * @return array
   *   The settings for tools.
   */
  protected function prepareSettings(array $settings = []) {
    if (empty($settings['tools'])) {
      return [];
    }
    // Getting only enabled tools.
    foreach ($settings['tools'] as $plugin_id => &$tool) {
      if ($tool['enable']) {
        /** @var \Drupal\editorjs\EditorJsToolsInterface $instance */
        $instance = $this->toolsManager->createInstance($plugin_id);
        $tool = $instance->prepareSettings($tool['settings'] ?? []);
        $tool += [
          'class' => $instance->implementer(),
          'class_file' => $instance->getFile(),
        ];
      }
    }

    return $settings;
  }

}

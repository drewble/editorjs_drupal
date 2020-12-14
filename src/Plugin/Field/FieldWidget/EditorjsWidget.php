<?php

namespace Drupal\editorjs\Plugin\Field\FieldWidget;

use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\DiffArray;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\editorjs\Event\EditorJsEvents;
use Drupal\editorjs\Event\FormSubmitEvent;
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
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->toolsManager = $container->get('plugin.manager.editorjs_tools');
    $instance->dispatcher = $container->get('event_dispatcher');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'tools' => [],
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['tools'] = [
      '#type' => 'container',
    ];

    $settings = $this->getSettings();
    foreach (array_keys($this->toolsManager->getDefinitions()) as $plugin_id) {
      $tool_settings = $settings['tools'][$plugin_id] ?? [];
      /** @var \Drupal\editorjs\EditorJsToolsInterface $instance */
      $instance = $this->toolsManager->createInstance($plugin_id);
      $element['tools'][$plugin_id]['enable'] = [
        '#type' => 'checkbox',
        '#title' => $instance->label(),
        '#description' => $instance->description(),
        '#default_value' => $tool_settings['enable'] ?? FALSE,
      ];
      $visible_name = "fields[{$this->fieldDefinition->getName()}][settings_edit_form][settings][tools][{$plugin_id}][enable]";
      $element['tools'][$plugin_id]['settings'] = [
        '#type' => 'details',
        '#title' => $this->t('Settings'),
        '#states' => [
          'visible' => [
            ':input[name="' . $visible_name . '"]' => ['checked' => TRUE],
          ],
        ],
      ];

      $settings_elements = $instance->settingsForm($tool_settings['settings'] ?? []);
      $element['tools'][$plugin_id]['settings'] += $settings_elements;
      if (empty($settings_elements)) {
        $element['tools'][$plugin_id]['settings']['#access'] = FALSE;
      }
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $settings = $this->prepareSettings($this->getSettings());
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
    // Save origin value.
    $form_state->set($items->getName() . ':' . $delta, $items[$delta]->value);

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
        continue;
      }
      unset($settings['tools'][$plugin_id]);
    }

    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as $delta => $item) {
      $origin_delta = $item['_original_delta'] ?? $delta;
      $value = Json::decode($item['value'] ?? '');
      $origin_value = $form_state->get($this->fieldDefinition->getName() . ':' . $origin_delta);
      $origin_value = Json::decode($origin_value ?? '');

      $this
        ->dispatcher
        ->dispatch(EditorJsEvents::FORM_SUBMIT, new FormSubmitEvent($value, $origin_value));
    }
    return parent::massageFormValues($values, $form, $form_state);
  }


}

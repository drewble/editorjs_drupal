<?php

namespace Drupal\editorjs\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\editorjs\Event\EditorJsEvents;
use Drupal\editorjs\Event\MassageValuesEvent;
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
   * The tools instances collection.
   *
   * @var \Drupal\editorjs\EditorJsToolsInterface[]
   */
  protected $instanceTools = [];

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
      'tools' => [
        'list' => [
          'status' => TRUE,
          'settings' => ['inlineToolbar' => TRUE],
        ],
        'inline_code' => [
          'status' => TRUE,
        ],
        'header' => [
          'status' => TRUE,
          'settings' => [
            'placeholder' => t('Enter a header'),
            'levels' => [2, 3, 4, 5],
            'defaultLevel' => 2,
          ],
        ],
      ],
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
      $description = $instance->description();
      if (!empty($instance->getPluginDefinition()['permission'])) {
        $description .= ' ' . $this->t('Please add permission to use this tool. <a href=":path" target="_blank">EditorJs</a>', [
          ':path' => Url::fromRoute('user.admin_permissions', [], ['fragment' => 'module-editorjs'])->toString(),
        ]);
      }
      $element['tools'][$plugin_id]['status'] = [
        '#type' => 'checkbox',
        '#title' => $instance->label(),
        '#description' => $description,
        '#default_value' => $tool_settings['status'] ?? FALSE,
      ];
      $visible_name = "fields[{$this->fieldDefinition->getName()}][settings_edit_form][settings][tools][{$plugin_id}][enable]";
      $element['tools'][$plugin_id]['settings'] = [
        '#type' => 'details',
        '#title' => $this->t('Settings'),
        '#tree' => TRUE,
        '#states' => [
          'visible' => [
            ':input[name="' . $visible_name . '"]' => ['checked' => TRUE],
          ],
        ],
      ];

      $settings_elements = $instance->settingsForm($this->fieldDefinition, $tool_settings['settings'] ?? []);
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

    $element['value'] = $element + [
      '#type' => 'hidden',
      '#default_value' => $items[$delta]->value ?? '',
      '#attached' => [
        'library' => ['editorjs/init'],
        'drupalSettings' => ['editorjs' => [$items->getName() => $this->prepareSettings()]],
      ],
      '#attributes' => [
        'class' => ['editorjs'],
        'data-field-name' => $items->getName(),
      ],
    ];
    // Save origin value.
    $form_state->set('origin:' . $items->getName() . ':' . $delta, $items[$delta]->value);
    foreach ($this->getInstanceTools() as $instance) {
      $element['value']['#attached']['library'] += $instance->getLibraries();
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [$this->t('Enabled tools:')];
    foreach ($this->getSetting('tools') ?? [] as $plugin_id => $tool) {
      if (empty($tool['status'])) {
        continue;
      }
      $def = $this->toolsManager->getDefinition($plugin_id, FALSE);
      if (empty($def)) {
        $summary[] = $this->t('Not found :id tool.', [':id' => $plugin_id]);
        continue;
      }
      $summary[] = $def['label'];
    }
    return $summary;
  }


  /**
   * Prepare settings tools for init EditorJs.
   *
   * @return array
   *   The settings for tools.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function prepareSettings(): array {
    $tools = $this->getSetting('tools') ?? [];
    $settings = [];

    foreach ($this->getInstanceTools() as $plugin_id => $instance) {
      if (!$instance->allowed()) {
        continue;
      }
      $tool = $instance->prepareSettings($tools[$plugin_id]['settings'] ?? []);
      $tool += [
        'class' => $instance->implementer(),
        'class_file' => $instance->getFile(),
      ];

      $settings['tools'][$plugin_id] = $tool;
    }

    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $event = new MassageValuesEvent($values, $form, $form_state, $this->fieldDefinition->getName());
    $this
      ->dispatcher
      ->dispatch(EditorJsEvents::MASSAGE_FORM_VALUES, $event);
    return parent::massageFormValues($event->getNewValues(), $form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    $dependencies = parent::calculateDependencies();
    foreach ($this->getSetting('tools') ?? [] as $id => $tool) {
      if (empty($tool['status'])) {
        continue;
      }
      $def = $this->toolsManager->getDefinition($id);
      if (in_array($def['provider'], $dependencies['module'] ?? [])) {
        continue;
      }
      $dependencies['module'][] = $def['provider'];
    }
    return $dependencies;
  }

  /**
   * Returns tools instances.
   *
   * @return \Drupal\editorjs\EditorJsToolsInterface[]
   *   The instances collection.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function getInstanceTools() {

    $tools = $this->getSetting('tools') ?? [];
    foreach ($tools as $plugin_id => $tool) {
      if (empty($tool['status'])) {
        continue;
      }
      if (array_key_exists($plugin_id, $this->instanceTools)) {
        continue;
      }
      $this->instanceTools[$plugin_id] = $this->toolsManager->createInstance($plugin_id);
    }

    return $this->instanceTools;
  }

}

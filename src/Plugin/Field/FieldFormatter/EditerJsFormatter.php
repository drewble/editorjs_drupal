<?php

namespace Drupal\editorjs\Plugin\Field\FieldFormatter;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'EditerJs' formatter.
 *
 * @FieldFormatter(
 *   id = "editorjs_default",
 *   label = @Translation("EditerJs"),
 *   field_types = {
 *     "editorjs"
 *   }
 * )
 */
class EditerJsFormatter extends FormatterBase {

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
  public static function defaultSettings() {
    return [
      'negate' => 1,
      'tools' => [],
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['negate'] = [
      '#type' => 'radios',
      '#options' => [
        $this->t('Show selected tools'),
        $this->t('Hide selected tools'),
      ],
      '#title_display' => 'invisible',
      '#default_value' => $this->getSetting('negate') ?? 1,
    ];

    $tools = ['paragraph' => 'Paragraph'];
    foreach ($this->toolsManager->getDefinitions() as $plugin_id => $def) {
      $tools[$plugin_id] = $def['label'];
    }
    $element['tools'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Tools'),
      '#options' => $tools,
      '#default_value' => $this->getSetting('tools'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#theme' => 'ce_blocks',
      ] + $this->prepareValue($item->value);
      $element[$delta]['#cache'] = [
        'contexts' => $items->getEntity()->getCacheContexts(),
        'tags' => $items->getEntity()->getCacheTagsToInvalidate(),
        'max-age' => $items->getEntity()->getCacheMaxAge(),
      ];
    }

    return $element;
  }

  /**
   * Prepare source value for render.
   *
   * @param string $value
   *   The source value.
   *
   * @return array
   *   Renderable structure.
   */
  public function prepareValue($value): array {
    $build = [];
    $tools = array_filter($this->getSetting('tools'));
    foreach (Json::decode($value) as $item) {
      if ($this->getSetting('negate')) {
        if (in_array($item['type'], $tools)) {
          continue;
        }
        $build[] = $this->buildTool($item);
      }
      elseif (in_array($item['type'], $tools)) {
        $build[] = $this->buildTool($item);
      }
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $tools = implode(', ', array_filter($this->getSetting('tools')));
    $tools = empty($tools) ? $this->t('Nothing') : $tools;
    if ($this->getSetting('negate')) {
      $summary[] = $this->t('Hidden tools: :tools', [':tools' => $tools]);
    }
    else {
      $summary[] = $this->t('Show only this tools: :tools', [':tools' => $tools]);
    }
    return $summary;
  }

  /**
   * Build tool block.
   *
   * @param array $data
   *   The source data for build renderable array.
   *
   * @return array
   *   The rendearable array.
   */
  protected function buildTool(array $data): array {
    return [
      '#theme' => 'ce_block__' . $data['type'],
      '#data' => $data,
    ];
  }

}

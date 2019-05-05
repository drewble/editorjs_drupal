<?php

namespace Drupal\editorjs\Plugin\Field\FieldWidget;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\editor\Plugin\EditorPluginInterface;
use Drupal\editorjs\EditorjsInterface;
use Drupal\editorjs\EditorjsPluginManager;
use Drupal\paragraphs\ParagraphInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the 'editorjs' field widget.
 *
 * @FieldWidget(
 *   id = "editorjs",
 *   label = @Translation("EditorJs"),
 *   field_types = {
 *     "entity_reference_revisions"
 *   },
 * )
 */
class EditorJsWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * The editorjs plugin manager.
   *
   * @var \Drupal\editorjs\EditorjsPluginManager
   */
  protected $editorjsPluginManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    EntityTypeManager $entityTypeManager,
    EditorjsPluginManager $editorjsPluginManager
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->entityTypeManager = $entityTypeManager;
    $this->editorjsPluginManager = $editorjsPluginManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.editorjs')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

  }

  /**
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array|mixed
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function formMultipleElements(FieldItemListInterface $items, array &$form, FormStateInterface $form_state) {
    $values = [];
    $elements['editorjs'] = [
      '#markup' => "<div id='editorjs'>Editor.js</div>",
      '#attached' => [
        'library' => ['editorjs/init'],
      ]
    ];

    //$this
    //  ->entityTypeManager
    //  ->getStorage('paragraph')
    //  ->delete(
    //    $this
    //      ->entityTypeManager
    //      ->getStorage('paragraph')
    //      ->loadMultiple()
    //  );

    if ($items->count()) {
      /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
      foreach ($items->referencedEntities() as $paragraph) {
        $values[] = $this->getDataByInstance($paragraph);
      }
    }

    $elements[0]['hide_element'] = array(
      '#type' => 'hidden',
      '#default_value' => Json::encode($values),
      '#element_validate' => [[$this, 'elementValidate']],
    );

    return $elements;
  }

  /**
   * Massages the form values into the format expected for field values.
   *
   * @param array $elements
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @param array $form
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function elementValidate(array $elements, FormStateInterface $form_state, array $form) {

    $blocks = Json::decode($elements['#value']);
    $field_name = $this->fieldDefinition->getName();

    // Remove paragraphs.
    /** @var EntityInterface $entity */
    $entity = $form_state
      ->getFormObject()
      ->getEntity();
    if (!$entity->get($field_name)->isEmpty()) {
      /** @var ParagraphInterface $paragraph */
      foreach ($entity->get($field_name)->referencedEntities() as $paragraph) {
        if ($this->isRemove($paragraph->id(), $blocks)) {
          $paragraph->delete();
        }
      }
    }

    foreach ($blocks as $weight => $block) {
      if ($paragraph = $this->prepareEntity($block)) {
        $form_state->setValue([$field_name, $weight, 'entity'], $paragraph);
      }
    }
  }

  /**
   * Prepare data. Create|edit and return paragraph.
   *
   * @param $block
   * @return bool|ParagraphInterface
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function prepareEntity($block) {
    $plugin_instance = $this
      ->editorjsPluginManager
      ->getInstanceByPluginType($block['type']);
    if ($plugin_instance instanceof EditorjsInterface) {
      $paragraph = $this
        ->entityTypeManager
        ->getStorage('paragraph')
        ->load($block['data']['pid']);
      if (!($paragraph instanceof ParagraphInterface)) {
        $paragraph = $plugin_instance->createParagraph();
      }
      $plugin_instance->setValues($paragraph, $block['data']);
      return $paragraph;
    }
    return FALSE;
  }

  /**
   * Return data
   *
   * @param \Drupal\paragraphs\ParagraphInterface $paragraph
   * @return bool|array
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function getDataByInstance(ParagraphInterface $paragraph) {
    $plugin_instance = $this
      ->editorjsPluginManager
      ->getInstanceByBundle($paragraph->bundle());
    if ($plugin_instance instanceof EditorjsInterface) {
      return $plugin_instance->getData($paragraph);
    }
    return FALSE;
  }

  protected function isRemove($pid, $newData = []) {
    foreach ($newData as $block) {
      if ($block['data']['pid'] === $pid) {
        return FALSE;
      }
    }
    return TRUE;
  }

}

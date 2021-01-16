<?php

namespace Drupal\Tests\editorjs\Kernel;

use Drupal\Component\Serialization\Json;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\field\Kernel\FieldKernelTestBase;

/**
 * Tests the editorjs field type.
 *
 * @group editorjs
 */
class EditorJsFieldItemTest extends FieldKernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['file', 'editorjs'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    FieldStorageConfig::create([
      'field_name' => 'field_editorjs',
      'entity_type' => 'entity_test',
      'type' => 'editorjs',
    ])->save();

    FieldConfig::create([
      'field_name' => 'field_editorjs',
      'label' => 'EditorJs',
      'entity_type' => 'entity_test',
      'bundle' => 'entity_test',
    ])->save();

  }

  /**
   * Tests that field values are saved a retrievable.
   */
  public function testFieldCreate() {
    $entity = EntityTest::create();
    $value = Json::encode([
      [
        'type' => 'paragraph',
        'data' => [
          'text' => $this->randomString(),
        ],
      ],
    ]);
    $entity->field_editorjs = $value;
    $entity->name->value = $this->randomMachineName();
    $entity->save();

    $this->assertEquals($entity->field_editorjs->value, $value);
  }

  /**
   * Tests the sample generate for editorjs field.
   */
  public function testFieldGenerateSample() {
    $entity = EntityTest::create();
    $entity->field_editorjs->generateSampleItems();
    $entity->name->value = $this->randomMachineName();
    $entity->save();
    $this->assertEquals(Json::decode($entity->field_editorjs->value), [
      [
        'type' => 'paragraph',
        'data' => [
          'text' => 'This is Editor.js a Block-Styled editor.',
        ],
      ],
    ]);
  }

}

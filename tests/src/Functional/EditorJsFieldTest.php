<?php

namespace Drupal\Tests\editorjs\Functional;

use Drupal\Component\Serialization\Json;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests the creation of 'editorjs' fields.
 *
 * @group telephone
 */
class EditorJsFieldTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'field',
    'node',
    'editorjs',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * A user with permission to create articles.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $webUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->drupalCreateContentType(['type' => 'article']);
    $this->webUser = $this->drupalCreateUser([
      'create article content',
      'edit own article content',
    ]);
    $this->drupalLogin($this->webUser);

    // Add the 'editorjs' field to the article content type.
    FieldStorageConfig::create([
      'field_name' => 'field_editorjs',
      'entity_type' => 'node',
      'type' => 'editorjs',
    ])->save();
    FieldConfig::create([
      'field_name' => 'field_editorjs',
      'label' => 'EditorJs',
      'entity_type' => 'node',
      'bundle' => 'article',
    ])->save();

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = \Drupal::service('entity_display.repository');
    $display_repository->getFormDisplay('node', 'article')
      ->setComponent('field_editorjs', [
        'type' => 'editorjs',
        'settings' => [
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
                'placeholder' => 'Enter a header',
                'levels' => [2, 3, 4, 5],
                'defaultLevel' => 2,
              ],
            ],
          ],
        ],
      ])
      ->save();

    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('field_editorjs', [
        'type' => 'editorjs_default',
        'weight' => 1,
        'settings' => [
          'negate' => TRUE,
        ],
      ])
      ->save();

  }

  /**
   * Tests "editorjs" field widget.
   */
  public function testFieldWidget() {
    $this->drupalGet('node/add/article');
    $field_editorjs = $this->assertSession()->hiddenFieldExists('field_editorjs[0][value]');

    $value = $this->randomString();
    $field_editorjs_value = Json::encode([
      [
        'type' => 'paragraph',
        'data' => [
          'text' => $value,
        ],
      ],
    ]);
    $field_editorjs->setValue($field_editorjs_value);
    $title = $this->randomString();
    $edit = [
      'title[0][value]' => $title,
    ];

    $this->submitForm($edit, 'Save');
    $this->assertSession()->pageTextContains($value);
  }

}
